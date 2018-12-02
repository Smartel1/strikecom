<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    private $data = [
        ['name'=>'Строительство'],
        ['name'=>'Транспорт'],
        ['name'=>'Образование'],
        ['name'=>'Здравоохранение'],
        ['name'=>'Торговля'],
        ['name'=>'Научные исследования'],
        ['name'=>'Электроэнергетика'],
        ['name'=>'Добывающая промышленность'],
        ['name'=>'Сельское хозяйство'],
        ['name'=>'Культура'],
        ['name'=>'Пищепром'],
        ['name'=>'ЖКХ'],
        ['name'=>'Спорт'],
        ['name'=>'Обрабатывающие производства'],
    ];

    public function run()
    {
        DB::table('industries')->insert($this->data);
    }
}