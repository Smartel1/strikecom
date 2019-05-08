<?php

namespace App\Console\Commands;

use App\Models\ConflictReason;
use App\Services\ImportService;
use Illuminate\Console\Command;

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
    protected $description = 'Загрузить в БД записи, хранящиеся в папке resources/dump. 
    Обязательно привести эти данные в корректный json';

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
        

        $conflictReasons = $service->fetchConflictReasons(
            file_get_contents(resource_path('dump/causes.json'))
        );

        $this->info('Придуманы причины конфликтов');
        

        //не сохраняются как сунщость
        $factories = collect(json_decode(
            file_get_contents(resource_path('dump/factories.json')), true
        ));

        $this->info('Построены заводы');
        

        $industries = $service->fetchIndustries(
            file_get_contents(resource_path('dump/inndystries.json'))
        );

        $this->info('Определены отрасли');
        

        $conflictResults = $service->fetchConflictResults(
            file_get_contents(resource_path('dump/results.json'))
        );

        $this->info('Запланированы результаты конфликтов');
        

        $eventStatuses = $service->fetchEventStatuses(
            file_get_contents(resource_path('dump/statuses.json'))
        );

        $this->info('Объявлены статусы событий');
        

        $eventTypes = $service->fetchEventTypes(
            file_get_contents(resource_path('dump/types.json'))
        );

        $this->info('Распределены типы событий');
        

        $users = $service->fetchUsers(
            file_get_contents(resource_path('dump/users.json'))
        );

        $this->info('Рождены пользователи');
        

        $conflicts = $service->fetchConflicts(
            file_get_contents(resource_path('dump/disputs.json'))
        );

        $this->info('Разгорелись конфликты');
        

        $events = $service->fetchEvents(
            file_get_contents(resource_path('dump/posts.json')),
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
            file_get_contents(resource_path('dump/posts.json')),
            $users
        );

        $this->info('Опубликованы новости');

        $service->fetchFavourites($users, $events, $news);

        $this->info('Народ оценил');

        $this->warn('ошибки выведены в лог');
    }

}
