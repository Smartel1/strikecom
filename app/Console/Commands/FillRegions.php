<?php

namespace App\Console\Commands;

use App\Entities\References\Country;
use App\Entities\References\Region;
use App\Services\ImportService;
use Illuminate\Console\Command;
use LaravelDoctrine\ORM\Facades\EntityManager;

class FillRegions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geo:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загрузить в БД записи, хранящиеся в папке resources/geo';

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
        $service->truncateTable('countries');
        $service->truncateTable('regions');

        $this->info('Очищены таблицы стран и регионов');

        $data = json_decode(file_get_contents(resource_path('geo/geo.json')), true);

        foreach ($data as $datum) {
            $country = new Country;
            $country->setNameRu($datum['title_ru']);
            $country->setNameEn($datum['title_en']);
            $country->setNameEs($datum['title_es']);
            EntityManager::persist($country);

            foreach ($datum['regions'] as $regionArray) {
                $region = new Region;
                $region->setName($regionArray['title_ru']);
                $region->setCountry($country);
                EntityManager::persist($region);
            }
        }

        EntityManager::flush();

        $this->info('готово');
    }

}
