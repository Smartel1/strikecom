<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConflictReasonSeeder extends Seeder
{
    private $data = [
        ['code'=> 'conditions', 'name'=> 'Условия труда'],
        ['code'=> 'payment', 'name'=> 'Оплата труда'],
        ['code'=> 'workday', 'name'=> 'Рабочее время'],
        ['code'=> 'delay', 'name'=> 'Задержка ЗП'],
        ['code'=> 'cuts', 'name'=> 'Сокращения'],
        ['code'=> 'contract', 'name'=> 'Коллективный договор'],
        ['code'=> 'liquidation', 'name'=> 'Ликвидация предприятия'],
        ['code'=> 'other', 'name'=> 'Прочее'],
    ];

    public function run()
    {
        DB::table('conflict_reasons')->insert($this->data);
    }
}