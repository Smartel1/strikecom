<?php

namespace App\Console\Commands;

use App\Services\ImportService;
use Illuminate\Console\Command;
use LaravelDoctrine\ORM\Facades\EntityManager;

class FillDBFromDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загрузить в БД записи, хранящиеся в папке resources/dump06';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param ImportService $service
     */
    public function handle(ImportService $service)
    {
        $this->info('Зародилась цивилизации');

        $service->truncateTable('comments');
        $service->truncateTable('conflicts');
        $service->truncateTable('conflict_reasons');
        $service->truncateTable('conflict_results');
        $service->truncateTable('events');
        $service->truncateTable('event_statuses');
        $service->truncateTable('event_types');
        $service->truncateTable('news');
        $service->truncateTable('industries');
        $service->truncateTable('regions');
        $service->truncateTable('users');
        $service->truncateTable('photos');
        $service->truncateTable('videos');

        $this->info('Очищены пустоши');

        $conflictReasons = $service->fetchConflictReasons(
            file_get_contents(resource_path('dump06/causes.json'))
        );

        $this->info('Придуманы причины конфликтов');
        

        //не сохраняются как сунщость
        $factories = collect(json_decode(
            file_get_contents(resource_path('dump06/factories.json')), true
        ));

        $this->info('Построены заводы');
        

        $industries = $service->fetchIndustries(
            file_get_contents(resource_path('dump06/inndystries.json'))
        );

        $this->info('Определены отрасли');
        

        $conflictResults = $service->fetchConflictResults(
            file_get_contents(resource_path('dump06/results.json'))
        );

        $this->info('Запланированы результаты конфликтов');
        

        $eventStatuses = $service->fetchEventStatuses(
            file_get_contents(resource_path('dump06/statuses.json'))
        );

        $this->info('Объявлены статусы событий');
        

        $eventTypes = $service->fetchEventTypes(
            file_get_contents(resource_path('dump06/types.json'))
        );

        $this->info('Распределены типы событий');
        

        $users = $service->fetchUsers(
            file_get_contents(resource_path('dump06/users.json'))
        );

        $this->info('Рождены пользователи');
        

        $conflicts = $service->fetchConflicts(
            file_get_contents(resource_path('dump06/disputs.json'))
        );

        $this->info('Разгорелись конфликты');
        

        $events = $service->fetchEvents(
            file_get_contents(resource_path('dump06/posts.json')),
            $users,
            $conflicts,
            $eventStatuses,
            $eventTypes,
            $factories,
            $industries,
            $conflictReasons,
            $conflictResults
        );

        $this->info('Зарегистрированы события');
        

        $news = $service->fetchNews(
            file_get_contents(resource_path('dump06/posts.json')),
            $users
        );

        $this->info('Опубликованы новости');

        $service->fetchFavourites($users, $events, $news);

        $this->info('Народ оценил');

        $service->fetchComments(
            file_get_contents(resource_path('dump06/messages.json')),
            $users,
            $events,
            $news
        );

        $this->info('Запущены слухи');

        EntityManager::flush();

        $this->info('Зафиксировано');

        $this->warn('ошибки выведены в лог');
    }

}
