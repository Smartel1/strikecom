<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conflict extends Model
{
    protected static function boot()
    {
        parent::boot();

        //При сохранении модели мы поле title перезаписываем в поле title_ru [en/es]
        self::creating(function($model){

            $locale = app('locale');

            if (array_has($model, 'title') and $locale !== 'all') {
                $model["title_$locale"] = $model['title'];
            }

            unset($model['title']);
        });

        //При сохранении модели мы поле title перезаписываем в поле title_ru [en/es]
        self::updating(function($model){

            $locale = app('locale');

            if (array_has($model, 'title') and $locale !== 'all') {
                $model["title_$locale"] = $model['title'];
            }

            unset($model['title']);
        });
    }

    protected $fillable = [
        'title',
        'title_ru',
        'title_en',
        'title_es',
        'latitude',
        'longitude',
        'company_name',
        'date_from',
        'date_to',
        'conflict_reason_id',
        'conflict_result_id',
        'industry_id',
        'region_id',
    ];

    protected $dateFormat = 'U';

    protected $dates = ['date_from','date_to'];

    protected $casts = [
        'latitude'  => 'double',
        'longitude' => 'double',
        'date_from' => 'integer',
        'date_to'   => 'integer',
        'created_at'=> 'integer',
        'updated_at'=> 'integer',
    ];

    public function events ()
    {
        return $this->hasMany(Event::class);
    }
}
