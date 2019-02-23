<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $fillable = [
        'name_ru', 'name_en', 'name_es',
    ];

    protected $visible = ['id', 'name'];

    protected $appends = ['name'];

    public $timestamps = false;

    public function getNameAttribute()
    {
        //todo локализация
        return $this->name_ru ?? 'untranslated';
    }
}
