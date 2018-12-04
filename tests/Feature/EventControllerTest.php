<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesApplication;

    public function testEventsStore()
    {
        Artisan::call('migrate:fresh');

        $response = $this->post('/api/event', [
            'conflict_id'       => null,
            'title'             => 'Беда в городе',
            'content'           => 'Рабы кричат и гневятся',
            'date'              => '2018-10-01',
            'source_link'       => 'https://domain.ru/img.gif',
            'event_status_id'   => '1',
            'event_type_id'     => '3',
            'tags'              => ['нищета', 'голод'],
            'image_urls'        => ['images/ff.gif'],
        ]);

        $response->assertStatus(200);

        //запрос на список событий
        $response = $this->get('/api/event');

        $response->assertStatus(200);

        //запрос одного события
        $response = $this->get('/api/event/1');

        $response->assertStatus(200);

        //запрос несуществующего события
        $response = $this->get('/api/event/2');

        $response->assertStatus(404);

        //запрос на обновление события
        $response = $this->put('/api/event/1', [
            'conflict_id'       => null,
            'title'             => 'Беда в мегаполисе',
            'content'           => 'Рабы беснуются и гневятся',
            'date'              => '2018-10-02',
            'source_link'       => 'https://domain.ru/img.png',
            'event_status_id'   => '2',
            'event_type_id'     => '5',
            'tags'              => ['голод'],
            'image_urls'        => ['images/ff.gif'],
        ]);

        $response->assertStatus(200);

        //запрос на обновление несуществующего события
        $response = $this->put('/api/event/2', [
            'conflict_id'       => null,
            'title'             => 'Беда в мегаполисе',
            'content'           => 'Рабы беснуются и гневятся',
            'date'              => '2018-10-02',
            'source_link'       => 'https://domain.ru/img.png',
            'event_status_id'   => '2',
            'event_type_id'     => '5',
            'tags'              => ['голод'],
            'image_urls'        => ['images/ff.gif'],
        ]);

        $response->assertStatus(404);

        //запрос на удаление события
        $response = $this->delete('/api/event/1');

        $response->assertStatus(200);

        //запрос на удаление несуществующего события
        $response = $this->delete('/api/event/1');

        $response->assertStatus(404);
    }


}
