<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConflictStatusSeeder extends Seeder
{
    private $data = [
        ['code'=> 'new', 'name'=> 'Новый'],
        ['code'=> 'in progress', 'name'=> 'В развитии'],
        ['code'=> 'finished', 'name'=> 'Завершен'],
    ];

    public function run()
    {
        DB::table('conflict_statuses')->insert($this->data);
    }
}