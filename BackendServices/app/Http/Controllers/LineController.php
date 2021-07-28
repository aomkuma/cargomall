<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

use Log;

use Storage;

use App\User;
use App\Models\LineMessage;
use App\Models\LineUserAccount;
use App\Models\LineJobDetail;

class LineController extends Controller
{

	public function index(Request $req){

		// Input mobile_no
    	$request_array = $req->all();
    	Log::info($request_array);
    	if($request_array){

    		$API_URL = env('LINE_REPLY_MASSAGE_URL');
			$ACCESS_TOKEN = env('LINE_ACCESS_TOKEN');
			$CHANEL_SECRET = env('LINE_CHANEL_SECRET');

			if ($request_array['events'] && sizeof($request_array['events']) > 0 ) {

			    foreach ($request_array['events'] as $event) {

			        $reply_token = $event['replyToken'];
			        $is_sent_reply = false;
			        $response_text = null;
			        $message_type = $event['message']['type'];
			        $line_user_id = $event['source']['userId'];
			        $message_group = 'other';
			        // check line user profile by line_user_id
			        // check user exist in system
			        $line_user = $this->checkLineUserAccount($line_user_id);

			        if(empty($line_user)){
			        	$line_user_profile = $this->callLineAPIUserProfile($ACCESS_TOKEN, $CHANEL_SECRET, $line_user_id);
			        	$this->addLineUserAccount($line_user_id, $line_user_profile);
			        	$line_user = $this->checkLineUserAccount($line_user_id);
			        }

			        // find user data
			        $user = $this->finduserByLineUserID($line_user_id);
			        callLineAPIUserProfile($ACCESS_TOKEN, $CHANEL_SECRET, $line_user_id);

			        if($message_type == 'text'){

			        	$text = trim($event['message']['text']);

			        	if(strlen($text) == 10){

			        		// verify user data
			        		$user = User::where('mobile_no', $text)
			        					->whereNull('line_user_id')
			        					->first();

			        		if($user){

			        			// register when match data
			        			$user->line_user_id = $line_user_id;
			        			$user->save();

			        			$LineUserAccount = [];
			        			$LineUserAccount['line_user_id'] = $line_user_id;
			        			$LineUserAccount['job_status'] = 'done';
			        			$LineUserAccount['active_status'] = true;
			        			$LineUserAccount['chat_type'] = 'other';

			        			LineUserAccount::create($LineUserAccount);

			        			$response_text = 'ยินดีด้วย คุณสามารถรับข้อมูลข่าวสาร รวมถึงรายละเอียดค่าใช้จ่ายได้นับจากนี้เป็นต้นไป';

			        		}else{

			        			// throws error back to user
			        			$response_text = 'ข้ออภัย ไม่พบข้แอมูลหมายเลขโทรศัพท์ที่ลงทะเบียนไว้กับระบบ';

			        		}

			        		$line_message = $this->saveLineMessage($message_type, $message_group, $text, $line_user);
							$this->pusherNoti($line_message);

							$is_sent_reply = true;
			        	}

			        	}else if($message_type == 'image'){
			        		$text = null;
	                        $message_id = $event['message']['id'];
	                		$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($ACCESS_TOKEN);
							$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $CHANEL_SECRET]);

							$response = $bot->getMessageContent($message_id);

							if ($response->isSucceeded()) {
								$path = 'line/' . $message_id . '.png';
								Storage::disk('public')->put($path, $response->getRawBody());
								$image_path = 'https://cargomall.co.th/' . Storage::url($path);

								$line_message = $this->saveLineMessage($message_type, $message_group, $text, $line_user, null, $image_path);
							}
						}

						else if($message_type == 'sticker'){

					    	$keywords = null;

					    	if(isset($event['message']['keywords'])){
					    		$keywords = $event['message']['keywords'];
					    	}else{
					    		$keywords = ['Unknown'];
					    	}
					    	$stickerId = $event['message']['stickerId'];
					    	$stickerResourceType = $event['message']['stickerResourceType'];
					    	// $stickers = $this->getStickerList();

					    	$sticker_message = null;
					    	$sticker_message = 'https://stickershop.line-scdn.net/stickershop/v1/sticker/'. $stickerId .'/android/sticker.png';

					    	if(empty($sticker_message)){
					    		$message_type = 'sticker';
						    	$text = $line_user->display_name . ' sent sticker emotion : ' . implode(', ', $keywords);
						    }else{
						    	$text = $sticker_message;
						    }
							$line_message = $this->saveLineMessage($message_type, $message_group, $text, $line_user, null, null, $sticker_message);
		                	// $this->pusherNoti($line_message);
		                	$is_sent_reply = false;

					    }

			        	// reply message back to user

			        	if($is_sent_reply){
				        	$data = [
				                'replyToken' => $reply_token,
				                'messages' => [['type' => 'text', 
			                					'text' => $response_text 
			                					]
			                				]
				            ];

				            
				            $POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);
				            $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

				            $send_result = $this->send_line_message($API_URL, $POST_HEADER, $post_body);

				        }
			        }

			    }

			}
		}    	

	}

	private function checkLineUserAccount($line_user_id){

    	$line_user = LineUserAccount::with('user')
					->where('line_user_id', $line_user_id)
					->first();

    	if($line_user){
    		if($line_user->job_status == 'done'){

    			$line_user->admin_job_id = null;
    			$line_user->job_status = 'new';
    			$line_user->save();

    			$count_line_job_detail = LineJobDetail::where('line_user_id', $line_user_id)
    									->where('job_status', 'new')
    									->count();

    			if($count_line_job_detail == 0 || $count_line_job_detail == null){
	    			// create LineJobDetail
	    			$LineJobDetail = [];
	    			$LineJobDetail['line_user_id'] = $line_user_id;
	    			$LineJobDetail['job_status'] = 'new';

	    			if($line_user->user){
						$LineJobDetail['user_id'] = $line_user->user->id;    					
	    			}

	    			LineJobDetail::create($LineJobDetail);
	    		}

    		} 

    		$line_user->line_config_item_id = $line_config_item_id;
    		$line_user->save();
    	}

    	return $line_user;

    }

    private function addLineUserAccount($line_user_id, $data){
    	
    	$LineUserAccount = [];

    	$LineUserAccount['id'] = $this->generateID();
    	$LineUserAccount['line_user_id'] = $line_user_id;
    	$LineUserAccount['display_name'] = filter_var( $data['display_name'], FILTER_SANITIZE_STRING);
    	$LineUserAccount['profile_url'] = $data['profile_url'];
    	$LineUserAccount['job_status'] = 'done';

    	LineUserAccount::create($LineUserAccount);

    }

    private function saveLineMessage($message_type, $message_group, $text, $user, $massage_from = null, $image_path = null, $sticker_path = null){

    	$user_id = null;
    	if($user){
    		if($user->user){
	    		$user_id = $user->user->id;
	    	}

    	}

    	$data = [
    			'user_id' => $user_id,
    			'admin_id' => $massage_from,
    			'line_user_id' => $user->line_user_id,
    			'message_type' => $message_type,
    			'message_group' => $message_group,
    			'message_desc' => $text,
    			'message_image' => $image_path,
    			'message_sticker' => $sticker_path
    		];

    	$result = LineMessage::create($data);

    	if(empty($massage_from)){
    		LineUserAccount::where('line_user_id', $user->line_user_id)
	    					->update(['chat_type' => $message_group]);
    	}

    	return $result;

    }

    private function generateID(){
		$pos1 = Str::random(4);
		$pos2 = date('is');
		$pos3 = date('md');
		$pos4 = rand(1000, 9999);
		return $pos1 . $pos2 . $pos3. $pos4;
	}

	private function pusherNoti($noti_obj){

        $app_id = env('PUSHER_APP_ID', '1125324');
        $app_key = env('PUSHER_KEY', 'bd770637ec2b82a87867');
        $app_secret = env('PUSHER_SECRET', '0c36cae3c8de0f39c782');
        $app_cluster = env('PUSHER_APP_CLUSTER', 'ap1');

        $pusher = new Pusher( $app_key, $app_secret, $app_id, array('cluster' => $app_cluster) );

        // get line_message with user
        $line_message = LineMessage::with('user')
        				->with('lineUserAccount')
        				->where('id', $noti_obj['id'])
        				->first()->toArray();

        $dataPusher = $line_message;

        $res = $pusher->trigger('line_cargo_mall', 'notification_event', $dataPusher);

        return $res;
    }

    private function callLineAPIUserProfile($ACCESS_TOKEN, $CHANEL_SECRET, $line_user_id){

    	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($ACCESS_TOKEN);
		$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $CHANEL_SECRET]);
		$response = $bot->getProfile($line_user_id);

		$data = [];

		if ($response->isSucceeded()) {
		    $profile = $response->getJSONDecodedBody();
		    $data['display_name'] = $profile['displayName'];
		    $data['profile_url'] = $profile['pictureUrl'];
		}

		return $data;
    }

    private function send_line_message($url, $post_header, $post_body)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
