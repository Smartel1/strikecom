<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    private $data = [

    ];

    public function run()
    {
        DB::table('regions')->insert($this->data);
    }
}