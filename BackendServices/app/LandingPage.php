<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    //
    protected $table = 'landing_page';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'type', 'image_path', 'text_desc', 'font_color', 'background_color', 'border_color', 'border_radius', 'border_width', 'landing_size', 'start_date', 'end_date', 'active_status', 'created_at', 'updated_at'
    ];
}
