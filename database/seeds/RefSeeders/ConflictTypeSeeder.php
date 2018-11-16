<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConflictTypeSeeder extends Seeder
{
    private $data = [
        ['code'=> 'call', 'name'=> 'Обращение'],
        ['code'=> 'demo', 'name'=> 'Демонстрация'],
        ['code'=> 'hunger', 'name'=> 'Голодовка'],
        ['code'=> 'strike', 'name'=> 'Забастовка частичная'],
        ['code'=> 'partial strike', 'name'=> 'Забастовка'],
        ['code'=> 'block', 'name'=> 'Перекрытие магистралей'],
        ['code'=> 'hassle', 'name'=> 'Столкновение'],
        ['code'=> 'trial', 'name'=> 'Судебный процесс'],
        ['code'=> 'inquiry', 'name'=> 'Расследование'],
        ['code'=> 'threat', 'name'=> 'Угроза'],
        ['code'=> 'talk', 'name'=> 'Переговоры'],
        ['code'=> '142', 'name'=> 'ст. 142 УК РФ'],
    ];

    public function run()
    {
        DB::table('conflict_types')->insert($this->data);
    }
}