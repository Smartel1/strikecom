<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    private $data = [
        ['name_ru'=>'Республика Саха (Якутия)'],
        ['name_ru'=>'Камчатский край'],
        ['name_ru'=>'Приморский край'],
        ['name_ru'=>'Хабаровский край'],
        ['name_ru'=>'Амурская область'],
        ['name_ru'=>'Магаданская область'],
        ['name_ru'=>'Сахалинская область'],
        ['name_ru'=>'Чукотский автономный округ'],
        ['name_ru'=>'Еврейская автономная область'],
        ['name_ru'=>'Республика Башкортостан'],
        ['name_ru'=>'Республика Марий Эл'],
        ['name_ru'=>'Республика Мордовия'],
        ['name_ru'=>'Республика Татарстан'],
        ['name_ru'=>'Удмуртская Республика'],
        ['name_ru'=>'Чувашская Республика'],
        ['name_ru'=>'Пермский край'],
        ['name_ru'=>'Кировская область'],
        ['name_ru'=>'Нижегородская область'],
        ['name_ru'=>'Оренбургская область'],
        ['name_ru'=>'Пензенская область'],
        ['name_ru'=>'Самарская область'],
        ['name_ru'=>'Саратовская область'],
        ['name_ru'=>'Ульяновская область'],
        ['name_ru'=>'Республика Карелия'],
        ['name_ru'=>'Республика Коми'],
        ['name_ru'=>'Архангельская область'],
        ['name_ru'=>'Вологодская область'],
        ['name_ru'=>'Калининградская область'],
        ['name_ru'=>'Ленинградская область'],
        ['name_ru'=>'Мурманская область'],
        ['name_ru'=>'Новгородская область'],
        ['name_ru'=>'Псковская область'],
        ['name_ru'=>'Город федерального значения Санкт-Петербург'],
        ['name_ru'=>'Ненецкий автономный округ'],
        ['name_ru'=>'Республика Дагестан'],
        ['name_ru'=>'Ингушская Республика'],
        ['name_ru'=>'Кабардино-Балкарская Республика'],
        ['name_ru'=>'Карачаево-Черкесская Республика'],
        ['name_ru'=>'Республика Северная Осетия-Алания'],
        ['name_ru'=>'Чеченская Республика'],
        ['name_ru'=>'Ставропольский край'],
        ['name_ru'=>'Республика Алтай'],
        ['name_ru'=>'Республика Бурятия'],
        ['name_ru'=>'Республика Тыва'],
        ['name_ru'=>'Республика Хакасия'],
        ['name_ru'=>'Алтайский край'],
        ['name_ru'=>'Забайкальский край'],
        ['name_ru'=>'Красноярский край'],
        ['name_ru'=>'Иркутская область'],
        ['name_ru'=>'Кемеровская область'],
        ['name_ru'=>'Новосибирская область'],
        ['name_ru'=>'Омская область'],
        ['name_ru'=>'Томская область'],
        ['name_ru'=>'Курганская область'],
        ['name_ru'=>'Свердловская область'],
        ['name_ru'=>'Тюменская область'],
        ['name_ru'=>'Челябинская область'],
        ['name_ru'=>'Ханты-Мансийский автономный округ'],
        ['name_ru'=>'Ямало-Ненецкий автономный округ'],
        ['name_ru'=>'Белгородская область'],
        ['name_ru'=>'Брянская область'],
        ['name_ru'=>'Владимирская область'],
        ['name_ru'=>'Воронежская область'],
        ['name_ru'=>'Ивановская область'],
        ['name_ru'=>'Калужская область'],
        ['name_ru'=>'Костромская область'],
        ['name_ru'=>'Курская область'],
        ['name_ru'=>'Липецкая область'],
        ['name_ru'=>'Московская область'],
        ['name_ru'=>'Орловская область'],
        ['name_ru'=>'Рязанская область'],
        ['name_ru'=>'Смоленская область'],
        ['name_ru'=>'Тамбовская область'],
        ['name_ru'=>'Тверская область'],
        ['name_ru'=>'Тульская область'],
        ['name_ru'=>'Ярославская область'],
        ['name_ru'=>'Город федерального значения Москва'],
        ['name_ru'=>'Республика Адыгея'],
        ['name_ru'=>'Краснодарский край'],
        ['name_ru'=>'Астраханская область'],
        ['name_ru'=>'Волгоградская область'],
        ['name_ru'=>'Ростовская область'],
        ['name_ru'=>'Республика Калмыкия'],
        ['name_ru'=>'Город федерального значения Севастополь'],
        ['name_ru'=>'Республика Крым'],
    ];

    public function run()
    {
        DB::table('regions')->insert($this->data);
    }
}