<?php
	
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header ("Last-Modified: " . gmdate ("D, d M Y H:i:s") . " GMT");
	//header("Content-Type: application/xml; charset=utf-8");

	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");//Dont cache
	header("Pragma: no-cache");//Dont cache
	header("Expires: " . date('D, d M Y H:i:s'));
?>
<!DOCTYPE html>
<html lang="en-US" data-ng-app="app">
	<head>
		<meta charset="utf-8">
		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title >{{$root.title}}Cargo Mall ฝากสั่งสินค้าจาก taobao, tmall, 1688 นำเข้า จีน-ไทย แลกเงินหยวน เรทถูกที่สุด</title>

		<meta name="fragment" content="!">

		<meta name="keywords" content="taobao, tmall, 1688, สั่งซื้อสินค้าจากจีนม สั่ง taobao , alipay, โอนเงิน alipay, สั่ง taobao เอง ,สั่งเว็บจีน  ,สั่งของเว็บจีน , ซื้อของเว็บจีน , สั่งซื้อจากจีน , สั่งซื้อสินค้าจากจีน ,สั่งสินค้าจากจีน">

		<meta http-equiv="Content-Security-Policy" content="img-src * 'self' data:; default-src * 'self' gap: ; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; ">

		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0"/>

		<meta name="author" content="Cargo Mall">
		<meta property="og:url"           content="" />
	  	<meta property="og:type"          content="website" />
	  	<meta property="og:title"         content="Cargo Mall" />
	  	<meta property="og:description"   content="" />
	  	<meta property="og:image"         content="" />
		
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
			
		<base href="/" /> 
	 	 
		<!-- 
		<base href="/cargomall/" />
		 -->
		<link rel="shortcut icon" type="image/png" href="favicon.ico"/>
		<!-- include js -->
		<script src='scripts/node_modules/angular/angular.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>'></script>
		<script src='scripts/node_modules/angular-ui-bootstrap/dist/ui-bootstrap-tpls.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>'></script>
		<script src='scripts/node_modules/angular-route/angular-route.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>'></script>
		<script src='scripts/node_modules/angular-animate/angular-animate.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>'></script>
		<script src="scripts/node_modules/angular-cookies/angular-cookies.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/node_modules/angular-ui-router/release/angular-ui-router.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/node_modules/oclazyload/dist/ocLazyLoad.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/node_modules/ng-file-upload/dist/ng-file-upload.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/node_modules/ng-file-upload/dist/ng-file-upload-shim.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/node_modules/angular-bind-html-compile-ci-dev/angular-bind-html-compile.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/node_modules/angular-translate/angular-translate.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/node_modules/ng-youtube/src/ng-youtube-embed.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/node_modules/pdfmake/build/pdfmake.js"></script>
		<script src="scripts/html2canvas.js"></script>
		<script type="text/javascript" src='scripts/node_modules/ngstorage/ngStorage.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>' charset="utf-8"></script>
		<script type="text/javascript" src='scripts/node_modules/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>' charset="utf-8"></script>
		
		<script src="scripts/node_modules/chart.js/dist/Chart.bundle.min.js"></script>

		<script src="scripts/node_modules/angular-socialshare/dist/angular-socialshare.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		
		<script src='scripts/main.js?version=ajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sd<?=strtotime(date('YmdHis')).rand(9,99999999) ?>'></script>
		<script src='scripts/route.js?version=ajsda9s87d9ajsda9s87d98a7sd8a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sd<?=strtotime(date('YmdHis')).rand(99,999999999) ?>'></script>
		<script src='scripts/factory.js?version=ajsajsda9s87d98a7sdda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sd<?=date('YmdHis').rand(999,999999999) ?>'></script>
		<script src='scripts/util.js?version=ajsda9s87d98ajsda9s87d98a7sda7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sdajsda9s87d98a7sd<?=date('YmdHis').rand(9999,999999999) ?>'></script>

		<script src="scripts/ckeditor_sdk/vendor/ckeditor/ckeditor.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/ckeditor_sdk/samples/assets/picoModal-2.0.1.min.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/ckeditor_sdk/samples/assets/contentloaded.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/ckeditor_sdk/samples/assets/beautify-html.js?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>"></script>
		<script src="scripts/jquery.min.js"></script>
		<script src="scripts/bootstrap.min.js"></script>
		
		<!-- include js end -->

		<!-- include css -->
		<link rel="stylesheet" href="scripts/node_modules/bootstrap/dist/css/bootstrap.min.css?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>">
		<link rel="stylesheet" href="scripts/node_modules/angular-ui-bootstrap/dist/ui-bootstrap-csp.css?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>">
		<link rel="stylesheet" href="scripts/node_modules/angular-bootstrap-colorpicker/css/colorpicker.css?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>">
		<link rel="stylesheet" href="fa/css/all.min.css?version=<?=strtotime(date('YmdHis')).rand(9,99999999) ?>">
		
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-161508645-1"></script>
		<script> window.prerenderReady = false; </script>
		<!-- include css end -->
	</head>

	<body data-ng-controller="AppController">
		<script type="text/javascript">
		  $(document).ready(function(){
		   
		  $('.dropdown-submenu a.test').on("click", function(e){
		    $(this).next('ul').toggle();
		    e.stopPropagation();
		    e.preventDefault();
		  });
		  // alert( 'asd' );
		});
		</script>
		<link rel="stylesheet" href="css/theme.css?version=2">
		<style type="text/css" media="screen">
			body{
				font-size: {{FONT_SIZE}}em;
				color: {{FONT_COLOR}};
				background-color : {{BG_COLOR}};
			}	
			a{
				color: {{FONT_COLOR}};
			}
			.navbar-inverse{
				background-color : {{BG_COLOR}};
				color: {{FONT_COLOR}};
			}

			.navbar-inverse .navbar-nav>li>a{
				color: {{ (FONT_COLOR=='#000'?'#FFF':FONT_COLOR) }};
			}
			
		</style>
		<div class="overlay" data-ng-show="overlay.overlay" style="padding-top: 0px; z-index: 1000001;">
			<div class="loader"></div>
		</div>

		
		<div class="overlay" data-ng-show="ShowForgotPassDialog" style="padding-top: 0px;">
			<div class="login-form">
				<div data-ng-include src="'views/forgot-pass.html'"></div>
			</div>
			
		</div>

		<div class="overlay" data-ng-show="ShowLoginDialog" style="padding-top: 0px;">
			<div class="login-form hidden-xs">
				<div data-ng-include src="'views/login.html'"></div>
			</div>
			<div class="login-form-xs visible-xs">
				<div data-ng-include src="'views/login.html'"></div>
			</div>
		</div>

		<div class="overlay" data-ng-show="ShowRegisterDialog" style="padding-top: 0px;">
			<div class="login-form hidden-xs">
				<div data-ng-include src="'views/register.html'"></div>
			</div>
			<div class="login-form-xs visible-xs">
				<div data-ng-include src="'views/register.html'"></div>
			</div>
		</div>

		<div class="overlay" data-ng-show="ShowEditProfileDialog" style="padding-top: 0px;">
			<div class="login-form hidden-xs" style="margin-top:0px;">
				<div data-ng-include src="'views/edit-profile.html'"></div>
			</div>
			<div class="login-form-xs visible-xs">
				<div data-ng-include src="'views/edit-profile.html'"></div>
			</div>
		</div>

		<div class="overlay" data-ng-show="ShowDialogRemoveItem" style="padding-top: 0px;">
			<div class="dialog-confirm-form">
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
						{{ 'CONFIRM_REMOVE_TXT' | translate }}
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
						<button class="btn btn-default" data-ng-click="closeDialog()">ยกเลิก</button>
						<button class="btn btn-primary" data-ng-click="removeItem(RemoveItemIndex)">ยืนยัน</button>
					</div>
				</div>
			</div>
		</div>

		<div class="overlay" data-ng-show="ShowDialogRemoveAddress" style="padding-top: 0px;">
			<div class="dialog-confirm-form">
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
						{{ 'CONFIRM_REMOVE_ADDRESS_TXT' | translate }}
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
						<button class="btn btn-default" data-ng-click="closeDialogRemoveAddress()">ยกเลิก</button>
						<button class="btn btn-primary" data-ng-click="removeAddress(RemoveAddressIndex)">ยืนยัน</button>
					</div>
				</div>
			</div>
		</div>

		<div class="overlay" data-ng-show="ShowDialogCancelOrder" style="padding-top: 0px;">
			<div class="dialog-confirm-form">
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
						{{ 'CANCEL_ORDER_TXT' | translate }}
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
						<button class="btn btn-default" data-ng-click="closeCancelOrderDialog()">ปิด</button>
						<button class="btn btn-primary" data-ng-click="cancelOrder()">ยืนยันการยกเลิก</button>
					</div>
				</div>
			</div>
		</div>

		<div class="overlay" data-ng-show="ShowDialogConfirmOrder" style="padding-top: 0px;">
			<div class="dialog-confirm-form">
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
						{{ 'CONFIRM_ORDER_TXT' | translate }}
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
						<button class="btn btn-default" data-ng-click="closeConfirmOrderDialog()">ยกเลิก</button>
						<button class="btn btn-primary" data-ng-click="confirmOrder()">ยืนยันการสั่งซื้อ</button>
					</div>
				</div>
			</div>
		</div>

		<div class="overlay" data-ng-show="ShowDialogSuccessOrder" style="padding-top: 0px;">
			<div class="dialog-confirm-form">
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
						<span style="color: green; font-weight: bolder; font-size: 1.2em;">ยืนยันการสั่งซื้อสำเร็จ !</span>
						<br><br>
						หมายเลขการสั่งซื้อของคุณคือ <b>{{OrderNumber}}</b><br>
						ขณะนี้เจ้าหน้าที่ของเราได้รับคำสั่งซื้อของท่านและอยู่ระหว่างการตรวจสอบข้อมูลแล้ว คุณสามารถตรวจสอบสถานะของ order ได้ที่เมนู <a href="tracking/order/detail/{{OrderID}}" target="_blank">"ตรวจสอบรายการสถานะสินค้า"</a> ค่ะ
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
						<button class="btn btn-primary" data-ng-click="closeSuccessOrderDialog()">กลับไปยังหน้าหลัก</button>
					</div>
				</div>
			</div>
		</div>

		<div data-ng-show="LandingPage != null" >
			<div class="overlay" data-ng-show="LandingPage.type == 'image'" style="padding-top: 0px;">
				<div class="dialog-landing-page hidden-xs" data-ng-class="{'landing-small':LandingPage.landing_size == 'small', 'landing-medium':LandingPage.landing_size == 'medium', 'landing-large':LandingPage.landing_size == 'large'}">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12" style="padding-right: 0px; padding-left: 0px;">
							<img src="{{LandingPage.image_path}}" style="width:100%;">
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button class="btn btn-default" data-ng-click="LandingPage = null">ปิดหน้านี้</button>
						</div>
					</div>
				</div>

				<div class="dialog-landing-page visible-xs landing-large">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12" style="padding-right: 0px; padding-left: 0px;">
							<img src="{{LandingPage.image_path}}" style="width:100%;">
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button class="btn btn-default" data-ng-click="LandingPage = null">ปิดหน้านี้</button>
						</div>
					</div>
				</div>
			</div>

			<div class="overlay" data-ng-show="LandingPage.type == 'text'" style="padding-top: 0px;">

				<div class="dialog-landing-page hidden-xs" data-ng-class="{'landing-small':LandingPage.landing_size == 'small', 'landing-medium':LandingPage.landing_size == 'medium', 'landing-large':LandingPage.landing_size == 'large'}" style="border: {{ LandingPage.border_width}}px solid {{LandingPage.border_color}}; background-color: {{LandingPage.background_color}}; border-radius: {{LandingPage.border_radius}}px; color: {{ LandingPage.font_color}};display: flex;
					  justify-content: center;
					  align-items: center;">
					  
					<div class="row">

						<div class="col-md-12 col-sm-12 col-xs-12" style="padding-right: 0px; padding-left: 0px;" bind-html-compile="LandingPage.text_desc">
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button class="btn btn-default" data-ng-click="LandingPage = null">ปิดหน้านี้</button>
						</div>
					</div>

					<div class="row">

					</div>
				</div>

				<div class="dialog-landing-page visible-xs landing-large" style="border: {{ LandingPage.border_width}}px solid {{LandingPage.border_color}}; background-color: {{LandingPage.background_color}}; border-radius: {{LandingPage.border_radius}}px; color: {{ LandingPage.font_color}};display: flex;
					  justify-content: center;
					  align-items: center;">
					  
					<div class="row">

						<div class="col-md-12 col-sm-12 col-xs-12" style="padding-right: 0px; padding-left: 0px;" bind-html-compile="LandingPage.text_desc">
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button class="btn btn-default" data-ng-click="LandingPage = null">ปิดหน้านี้</button>
						</div>
					</div>

					<div class="row">

					</div>
				</div>

			</div>
		</div>

		<div class="overlay" data-ng-show="ShowOverlay" style="padding-top: 0px;">
			
		</div>

		<div class="ng-view container"></div>

	</body>
	<style type="text/css" media="screen">

		input:required{
			border-color: red;
		}

		select:required{
			border-color: red;
		}

		.landing-small{
			margin: 0 auto; 
			width: 30%;
			margin-top:12%;
		}

		.landing-medium{
			margin: 0 auto; 
			width: 60%;
			margin-top:7%;
		}

		.landing-large{
			margin: 0 auto; 
			margin-top:2%;
			width: 94%;
		}

		.dialog-landing-page {
		  border: 1px solid #999;
		  border-radius: 10px;
		  background-color: #FFF;
		  
		  padding: 20px;
		}

		.dialog-confirm-form {
		  border: 1px solid #999;
		  border-radius: 10px;
		  background-color: #FFF;
		  width: 50%;
		  margin: 0 auto; 
		  margin-top:5%;
		  padding: 20px;
		}

		.login-form {
		  border: 1px solid #999;
		  border-radius: 10px;
		  background-color: #FFF;
		  width: 70%;

		  max-height: 100%;
		  margin: 0 auto; 
		  margin-top:5%;
		  padding: 20px;
		  overflow: scroll;
		  overflow-x: hidden;
		}

		.login-form-xs {
		  border: 1px solid #999;
		  border-radius: 10px;
		  background-color: #FFF;
		  width: 100%;
		  max-height: 100%;
		  margin: 0 auto; 
		  margin-top:5%;
		  padding-bottom: 20px;
		  overflow: scroll;
		  overflow-x: hidden;
		}

		.loader {
		  border: 16px solid #f3f3f3;
		  border-radius: 50%;
		  border-top: 16px solid #3498db;
		  width: 120px;
		  height: 120px;
		  margin: 0 auto; 
		  margin-top:20%;
		  -webkit-animation: spin 2s linear infinite; /* Safari */
		  animation: spin 2s linear infinite;
		}

		/* Safari */
		@-webkit-keyframes spin {
		  0% { -webkit-transform: rotate(0deg); }
		  100% { -webkit-transform: rotate(360deg); }
		}

		@keyframes spin {
		  0% { transform: rotate(0deg); }
		  100% { transform: rotate(360deg); }
		}

		.overlay{
		    margin:0 auto;
		    position: fixed;
		    height: 100%;
		    width: 100%;
		    z-index: 1000000;
		    opacity:8.0;
		    filter:alpha(opacity=80);
		    background-color: rgba(0, 0, 0, 0.5);
		    text-align:center;   
		}

		.inner-overlay{
		    margin:0 auto;
		    position: fixed;
		    height: 100%;
		    width: 100%;
		    z-index: 1000000;
		    opacity:8.0;
		    text-align:center;   
		}

		.text-line-through{
			color:red;
			text-decoration:line-through;
		}

		.btn-info:hover{
			background-color: #ccc;
			border-color: #ccc;
			color: #FFF;
		}

		.btn-info{
			background-color: #999;
			border-color: #999;
			color : #FFF;
		}

		.table-caption-head{
			margin: 10px;
			font-size: 1.3em;
			font-weight: bolder;
			text-align: center;
		}

		@media (min-width: 769px) and (max-width: 800px) {
			.navbar-header {
                    float: none;
                }
                
                .navbar-left, .navbar-right {
                    float: none !important;
                }

                .navbar-toggle {
                    display: block;
                }

                .navbar-collapse {
                    border-top: 1px solid transparent;
                    box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
                }

                .navbar-fixed-top {
                    top: 0;
                    border-width: 0 0 1px;
                }

                .navbar-collapse.collapse {
                    display: none !important;
                }

                .navbar-nav {
                    float: none !important;
                    margin-top: 7.5px;
                }

                    .navbar-nav > li {
                        float: none;
                    }

                        .navbar-nav > li > a {
                            padding-top: 10px;
                            padding-bottom: 10px;
                        }

                .collapse.in {
                    display: block !important;
                }

		}
	</style>
	
</html>
