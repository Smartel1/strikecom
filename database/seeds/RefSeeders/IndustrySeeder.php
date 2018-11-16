<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    private $data = [

    ];

    public function run()
    {
        DB::table('industries')->insert($this->data);
    }
}