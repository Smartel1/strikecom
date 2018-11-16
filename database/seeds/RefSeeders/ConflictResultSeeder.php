<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConflictResultSeeder extends Seeder
{
    private $data = [
        ['code'=> 'fully satisfied', 'name'=> 'Удовлетворены полностью'],
        ['code'=> 'partially satisfied', 'name'=> 'Удовлетворены частично'],
        ['code'=> 'not satisfied', 'name'=> 'Не удовлетворены'],
    ];

    public function run()
    {
        DB::table('conflict_results')->insert($this->data);
    }
}