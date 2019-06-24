<?php

namespace App\Console\Commands;

use App\Entities\Event;
use App\Entities\References\Country;
use App\Entities\References\Locality;
use App\Entities\References\Region;
use App\Services\ImportService;
use Illuminate\Console\Command;
use LaravelDoctrine\ORM\Facades\EntityManager;

class DumpRegions extends Command
{
    protected $signature = 'locations:load';

    protected $description = 'Подтянуть регионы из файла locations.json для событий';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Начинаем');

        $data = json_decode(file_get_contents(resource_path('dump06/locations.json')), true);

        foreach ($data as $datum) {

            $events = EntityManager::createQueryBuilder()
                ->select('e')
                ->from(Event::class, 'e')
                ->where('e.latitude = :lat AND e.longitude = :lng')
                ->setParameter('lat', $datum['lat'])
                ->setParameter('lng', $datum['lng'])
                ->getQuery()
                ->getResult();

            if (count($events) === 0) continue;

            $region = EntityManager::getRepository(Region::class)->findOneBy(['name' => $datum['province_ru']]);

            if (array_has($datum, 'locality_ru')) {
                $localityName = $datum['locality_ru'];
            } else {
                $localityName = $datum['area_ru'];
            }

            $locality = EntityManager::getRepository(Locality::class)->findOneBy(['name' => $localityName]);

            if (!$locality) {
                $locality = new Locality;
                $locality->setName($localityName);
                $locality->setRegion($region);
                EntityManager::persist($locality);
                EntityManager::flush();
            }

            /** @var Event $event */
            foreach ($events as $event) {
                $event->setLocality($locality);
                EntityManager::persist($event);
            }
        }

        EntityManager::flush();

        $this->info('Готово');
    }

}
