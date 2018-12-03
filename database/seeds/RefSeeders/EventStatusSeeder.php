<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventStatusSeeder extends Seeder
{
    private $data = [
        ['code'=> 'new', 'name'=> 'Новый'],
        ['code'=> 'in progress', 'name'=> 'В развитии'],
        ['code'=> 'finished', 'name'=> 'Завершен'],
    ];

    public function run()
    {
        DB::table('event_statuses')->insert($this->data);
    }
}