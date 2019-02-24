<?php

namespace RefSeeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VideoTypesSeeder extends Seeder
{
    private $data = [
        ['code'=>'youtube_link'],
        ['code'=>'vk_link'],
        ['code'=>'other'],
    ];

    public function run()
    {
        DB::table('video_types')->insert($this->data);
    }
}