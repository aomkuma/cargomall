<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

class LandingPage extends Model
{
    //
    protected $table = 'landing_page';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'start_date' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'id', 'type', 'image_path', 'text_desc', 'font_color', 'background_color', 'border_color', 'border_radius', 'border_width', 'landing_size', 'start_date', 'end_date', 'active_status', 'created_at', 'updated_at'
    ];

     protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
