<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'url', 'preview_url', 'video_type_id'
    ];

    protected $hidden = ['pivot'];

    public $dateFormat = 'U';

    public function type()
    {
        return $this->belongsTo(Video::class);
    }
}
