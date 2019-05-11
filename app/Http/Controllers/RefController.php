<?php

namespace App\Http\Controllers;

use App\Entities\News;
use App\Http\Resources\News\NewsDetailResource;
use App\Models\ConflictReason;
use App\Models\ConflictResult;
use App\Models\EventStatus;
use App\Models\EventType;
use App\Http\Requests\Reference\ReferenceIndexRequest;
use App\Http\Resources\Reference\ReferenceResource;
use App\Models\Industry;
use App\Models\Region;
use App\Models\VideoType;
use LaravelDoctrine\ORM\Facades\EntityManager;

class RefController extends Controller
{
    public function index(ReferenceIndexRequest $request, $locale)
    {
        $eventTypes = ReferenceResource::collection(EventType::get());
        $conflictReasons = ReferenceResource::collection(ConflictReason::get());
        $conflictResults = ReferenceResource::collection(ConflictResult::get());
        $eventStatuses = ReferenceResource::collection(EventStatus::get());
        $industries = ReferenceResource::collection(Industry::get());
        $regions = ReferenceResource::collection(Region::get());
        $videoTypes = VideoType::get();

        return compact('eventTypes',
            'conflictReasons',
            'conflictResults',
            'eventStatuses',
            'industries',
            'regions',
            'videoTypes');
    }

    public function checkSum()
    {
        $eventTypeCheckSum = EventType::get()->reduce(function($carry, $item){
            return md5($carry . $item->id .$item->name_ru . $item->name_en . $item->name_es);
        });

        $conflictReasonCheckSum = ConflictReason::get()->reduce(function($carry, $item){
            return md5($carry . $item->id .$item->name_ru . $item->name_en . $item->name_es);
        });

        $conflictResultCheckSum = ConflictResult::get()->reduce(function($carry, $item){
            return md5($carry . $item->id .$item->name_ru . $item->name_en . $item->name_es);
        });

        $eventStatusCheckSum = EventStatus::get()->reduce(function($carry, $item){
            return md5($carry . $item->id .$item->name_ru . $item->name_en . $item->name_es);
        });

        $industryCheckSum = Industry::get()->reduce(function($carry, $item){
            return md5($carry . $item->id .$item->name_ru . $item->name_en . $item->name_es);
        });

        $regionCheckSum = Region::get()->reduce(function($carry, $item){
            return md5($carry . $item->id .$item->name_ru . $item->name_en . $item->name_es);
        });

        $videoTypeCheckSum = VideoType::get()->reduce(function($carry, $item){
            return md5($carry . $item->id .$item->name_ru . $item->name_en . $item->name_es);
        });

        return [
            'checkSum' => md5($eventTypeCheckSum . $conflictReasonCheckSum . $conflictResultCheckSum . $eventStatusCheckSum
                . $industryCheckSum . $regionCheckSum . $videoTypeCheckSum)
        ];
    }

    public function test()
    {
        $r= EntityManager::createQuery('select n, u from App\Entities\News n join n.user u')->getResult();
        $r = EntityManager::createQueryBuilder()
            ->select('n')
            ->from('App\Entities\News', 'n')
            ->leftJoin('n.user', 'u')
            ->getQuery()
            ->getResult();

        return dd($r);
        $repo = EntityManager::getRepository(News::class);
        $news = $repo->find(1);
        return NewsDetailResource::make($news);
    }
}
