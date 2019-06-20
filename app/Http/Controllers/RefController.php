<?php

namespace App\Http\Controllers;

use App\Entities\References\ClaimType;
use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use App\Entities\References\Industry;
use App\Entities\References\VideoType;
use App\Http\Resources\Reference\DoctrineReferenceResource;
use App\Http\Requests\Reference\ReferenceIndexRequest;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;

class RefController extends Controller
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * RefController constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Получить список справочников
     * @param ReferenceIndexRequest $request
     * @param $locale
     * @return array
     */
    public function index(ReferenceIndexRequest $request, $locale)
    {
        $conflictReasons = $this->localizeReference($this->getReference(ConflictReason::class));
        $conflictResults = $this->localizeReference($this->getReference(ConflictResult::class));
        $eventStatuses = $this->localizeReference($this->getReference(EventStatus::class));
        $claimTypes = $this->localizeReference($this->getReference(ClaimType::class));
        $eventTypes = $this->localizeReference($this->getReference(EventType::class));
        $industries = $this->localizeReference($this->getReference(Industry::class));

        $videoTypes = $this->em
            ->createQuery('SELECT ref FROM App\Entities\References\VideoType ref')
            ->getResult();

        $videoTypes = collect($videoTypes)->map(function (VideoType $type) {
            return [
                'id'   => $type->getId(),
                'code' => $type->getCode()
            ];
        });

        return compact(
            'conflictReasons',
            'conflictResults',
            'eventStatuses',
            'claimTypes',
            'eventTypes',
            'industries',
            'videoTypes');
    }

    /**
     * Получить контрольную сумму справочников для контроля изменений
     * @return array
     */
    public function checkSum()
    {
        //функция, которую передадим в reducer для создания md5 хэша сущностей
        $reducerCallback = function ($carry, $item) {
            return md5($carry . $item->getId() .$item->getNameRu() . $item->getNameEn() . $item->getNameEs());
        };

        $conflictReasonCheckSum = $this->getReference(ConflictReason::class)->reduce($reducerCallback);
        $conflictResultCheckSum = $this->getReference(ConflictResult::class)->reduce($reducerCallback);
        $eventStatusCheckSum = $this->getReference(EventStatus::class)->reduce($reducerCallback);
        $claimTypeCheckSum = $this->getReference(ClaimType::class)->reduce($reducerCallback);
        $eventTypeCheckSum = $this->getReference(EventType::class)->reduce($reducerCallback);
        $industryCheckSum = $this->getReference(Industry::class)->reduce($reducerCallback);

        //хэш типа видео вычисляется по полям id и code
        $videoTypeCheckSum = $this->getReference(VideoType::class)->reduce(function ($carry, VideoType $item) {
            return md5($carry . $item->getId() . $item->getCode());
        });

        return [
            'checkSum' => md5(
                $conflictReasonCheckSum
                . $conflictResultCheckSum
                . $eventStatusCheckSum
                . $eventTypeCheckSum
                . $claimTypeCheckSum
                . $industryCheckSum
                . $videoTypeCheckSum)
        ];
    }

    /**
     * Получить справочник по типу сущности в виде массива
     * @param $class
     * @return Collection
     */
    private function getReference($class)
    {
        $array = $this->em
            ->createQuery('SELECT ref FROM '. $class .' ref')
            ->getResult();

        return collect($array);
    }

    /**
     * Локализовать справочник в зависимости от переданной в path локали
     * @param $referenceCollection Collection
     * @return array
     */
    private function localizeReference(Collection $referenceCollection)
    {
        return DoctrineReferenceResource::collection($referenceCollection)->toArray(null);
    }
}
