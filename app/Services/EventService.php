<?php


namespace App\Services;

use App\Criteria\BelongsToConflicts;
use App\Criteria\ByPermission;
use App\Criteria\HasTag;
use App\Criteria\HasLocalizedContent;
use App\Criteria\HasLocalizedTitle;
use App\Criteria\SafeBetween;
use App\Criteria\SafeEq;
use App\Criteria\SafeIn;
use App\DTO\LocalesDTO;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\Photo;
use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use App\Entities\References\Locality;
use App\Entities\Tag;
use App\Entities\User;
use App\Entities\Video;
use App\Exceptions\BusinessRuleValidationException;
use App\Repositories\ConflictRepository;
use App\Rules\NotAParentEvent;
use App\Rules\UserCanModerate;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\TransactionRequiredException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class EventService
{
    protected $em;

    protected $businessValidationService;

    private $pushService;

    private $locale;

    public function __construct(
        EntityManager $em,
        BusinessValidationService $businessValidationService,
        PushService $pushService
    )
    {
        $this->em = $em;
        $this->businessValidationService = $businessValidationService;
        $this->pushService = $pushService;
        $this->locale = app('locale');
    }

    /**
     * Найти события по фильтрам и вернуть с пагинацией
     * @param $filters array фильтры
     * @param $perPage int размер выборки
     * @param $page int номер страницы
     * @param User|null $user пользователь, запросивший ресурс
     * @return LengthAwarePaginator
     * @throws QueryException
     */
    public function index($filters, $perPage, $page, ?User $user)
    {
        //Запрашиваем новости с их связанными сущностями, сортируя по убыванию даты
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('e, p, v, t, c, SIZE(e.comments) comments_count')
            ->from('App\Entities\Event', 'e')
            ->leftJoin('e.photos', 'p')
            ->leftJoin('e.videos', 'v')
            ->leftJoin('e.tags', 't')
            ->leftJoin('e.conflict', 'c')
            ->addCriteria(ByPermission::make($user))
            ->addCriteria(SafeBetween::make(
                'e.date',
                Arr::has($filters, 'date_from')
                    ? Datetime::createFromFormat('U', Arr::get($filters, 'date_from'))
                    : null,
                Arr::has($filters, 'date_to')
                    ? Datetime::createFromFormat('U', Arr::get($filters, 'date_to'))
                    : null
            ))
            ->addCriteria(SafeEq::make('e.published', Arr::get($filters, 'published')))
            ->addCriteria(SafeIn::make('e.eventStatus', Arr::get($filters, 'event_status_ids')))
            ->addCriteria(SafeIn::make('e.eventType', Arr::get($filters, 'event_type_ids')))
            ->addCriteria(HasTag::make('e', Arr::get($filters, 'tag_id')))
            ->addCriteria(BelongsToConflicts::make(Arr::get($filters, 'conflict_ids')))
            ->addCriteria(HasLocalizedTitle::make('e', $this->locale))
            ->addCriteria(HasLocalizedContent::make('e', $this->locale))
            ->orderBy('e.date', 'desc');

        //Полнотекстовый фильтр по содержанию строки в событии
        if (Arr::has($filters, 'contains_content')) {
            $queryBuilder
                ->andWhere('LOWER(e.content_ru) like LOWER(:contains_content) or '
                    . 'LOWER(e.content_en) like LOWER(:contains_content) or '
                    . 'LOWER(e.content_es) like LOWER(:contains_content)')
                ->setParameter('contains_content', '%' . Arr::get($filters, 'contains_content') . '%');
        }
        if (Arr::has($filters, 'contains_content_en')) {
            $queryBuilder
                ->where('LOWER(e.content_en) like LOWER(:contains_content_en)')
                ->setParameter('contains_content_en', '%' . Arr::get($filters, 'contains_content_en') . '%');
        }
        if (Arr::has($filters, 'contains_content_es')) {
            $queryBuilder
                ->where('LOWER(e.content_es) like LOWER(:contains_content_es)')
                ->setParameter('contains_content_es', '%' . Arr::get($filters, 'contains_content_es') . '%');
        }

        //Фильтр "только избранные" (criteria не получилось сделать)
        if (Arr::get($filters, 'favourites')) {
            $queryBuilder
                ->andWhere(':user MEMBER OF e.likedUsers')
                ->setParameter('user', $user);
        }

        //Фильтр по странам (нужен join, поэтому не в criteria)
        if (Arr::has($filters, 'country_ids')) {
            $queryBuilder
                ->leftJoin('e.locality', 'loc')
                ->leftJoin('loc.region', 'reg')
                ->leftJoin('reg.country', 'ctr')
                ->andWhere($this->em->getExpressionBuilder()->in('ctr', ':countries'))
                ->setParameter('countries', Arr::get($filters, 'country_ids'));
        }

        //Фильтр по регионам (нужен join, поэтому не в criteria)
        if (Arr::has($filters, 'region_ids')) {
            $queryBuilder
                ->leftJoin('e.locality', 'loc')
                ->leftJoin('loc.region', 'reg')
                ->andWhere($this->em->getExpressionBuilder()->in('reg', ':regions'))
                ->setParameter('regions', Arr::get($filters, 'region_ids'));
        }

        //Если передан фильтр "вблизи точки", то применяем ограничение по формуле гаверсинуса
        //https://stackoverflow.com/questions/21084886/how-to-calculate-distance-using-latitude-and-longitude
        if (Arr::has($filters, 'near')) {
            $queryBuilder->andWhere('6371 * acos(cos(radians(:lat)) * cos(radians(e.latitude)) * cos(radians(e.longitude) - radians(:lng)) + sin(radians(:lat)) * sin(radians(e.latitude))) <= :radius')
                ->setParameter('lat', Arr::get($filters, 'near.lat'))
                ->setParameter('lng', Arr::get($filters, 'near.lng'))
                ->setParameter('radius', Arr::get($filters, 'near.radius'));
        }

        //Пагинируем результат
        $doctrinePaginator = new Paginator(
            $queryBuilder->setFirstResult($perPage * ($page - 1))->setMaxResults($perPage)->getQuery()
        );

        //Переводим в формат, понятный laravel
        $laravelPaginator = new LengthAwarePaginator(
            collect($doctrinePaginator),
            $doctrinePaginator->count(),
            (integer)$perPage,
            $page,
            ['path' => request()->url()]
        );

        return $laravelPaginator;
    }

    /**
     * Создать событие
     * @param $data
     * @param $user
     * @return Event
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws BusinessRuleValidationException
     */
    public function create($data, User $user)
    {
        //Если пользователь хочет сразу опубликовать событие, он должен быть модератором
        $this->businessValidationService->validate([
            (new UserCanModerate($user))->when(Arr::get($data, 'published') or Arr::has($data, 'locality_id'))
        ]);

        $this->em->beginTransaction();

        $event = new Event;
        $event->setAuthor($user);
        $this->fillEventFields($event, $data);
        $this->syncPhotos($event, Arr::get($data, 'photo_urls', []));
        $this->syncVideos($event, Arr::get($data, 'videos', []));
        $this->syncTags($event, Arr::get($data, 'tags', []));

        $this->em->persist($event);
        $this->em->flush();
        $this->em->commit();

        //Если обычный пользователь предлагает событие, посылаем пуш админам. Если модератор публикует - то всем
        if (!$event->isPublished()) {
            $this->pushService->eventCreatedByUser($event);
        } else {
            $this->pushService->eventPublished($event, new LocalesDTO(
                !is_null($event->getTitleRu()),
                !is_null($event->getTitleEn()),
                !is_null($event->getTitleEs())
            ));
        }

        return $event;
    }

    /**
     * @param Event $event
     * @param $data
     * @param $user
     * @return Event
     * @throws BusinessRuleValidationException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function update(Event $event, $data, $user)
    {
        $userChangesPublishStatus = (
            Arr::has($data, 'published')
            and
            (bool)Arr::get($data, 'published') !== $event->isPublished()
        );

        $userChangesConflict = (Arr::has($data, 'conflict_id') and ($data['conflict_id'] !== $event->getConflict()->getId()));

        $this->businessValidationService->validate([
            (new UserCanModerate($user))->when(
                $userChangesPublishStatus
                or Arr::has($data, 'locality_id')
                or $userChangesConflict
            ),
            (new NotAParentEvent($event))->when($userChangesConflict)
        ]);

        //Перед изменением смотрим, на каких языках уже есть переводы (чтобы не послать пуш второй раз)
        $nullLocalesBeforeUpdate = new LocalesDTO(
            is_null($event->getTitleRu()),
            is_null($event->getTitleEn()),
            is_null($event->getTitleEs())
        );

        $this->em->beginTransaction();

        $this->fillEventFields($event, $data);
        if (Arr::has($data, 'photo_urls')) $this->syncPhotos($event, Arr::get($data, 'photo_urls', []));
        if (Arr::has($data, 'videos')) $this->syncVideos($event, Arr::get($data, 'videos', []));
        if (Arr::has($data, 'tags')) $this->syncTags($event, Arr::get($data, 'tags', []));

        $this->em->persist($event);
        $this->em->flush();
        $this->em->commit();

        //Если событие опубликовано, то посылаем пуши в те топики по языкам, на которые переведено событие в этом обновлении.
        //Если событие публикуется в этом действии, то посылаются пуши на все языки, на которые локализовано событие
        if ($event->isPublished()) {
            $localesToPush = new LocalesDTO(
                ($nullLocalesBeforeUpdate->isRu() or $userChangesPublishStatus) and !is_null($event->getTitleRu()),
                ($nullLocalesBeforeUpdate->isEn() or $userChangesPublishStatus) and !is_null($event->getTitleEn()),
                ($nullLocalesBeforeUpdate->isEs() or $userChangesPublishStatus) and !is_null($event->getTitleEs())
            );

            $this->pushService->eventPublished($event, $localesToPush);

            //Если событие публикуется в этом действии, то автору посылается уведомление об этом
            if ($userChangesPublishStatus) {
                $this->pushService->sendYourPostModerated($event);
            }
        }

        return $event;
    }

    /**
     * Пометить событие как избранное или снять отметку
     * @param Event $event
     * @param User $user
     * @param bool $isFavourite
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setFavourite(Event $event, User $user, bool $isFavourite)
    {
        $currentFavourites = $user->getFavouriteEvents();

        if ($isFavourite) {
            //Добавим в избранное, если ещё не в избранном
            if (!$currentFavourites->contains($event)) {
                $currentFavourites->add($event);
            }
        } else {
            $currentFavourites->removeElement($event);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Event $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function incrementViews(Event $event)
    {
        $event->setViews($event->getViews() + 1);

        $this->em->flush();
    }

    /**
     * Заполнить поля события переданными данными
     * @param Event $event
     * @param $data
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function fillEventFields(Event $event, $data)
    {
        if (Arr::has($data, 'conflict_id')) $this->attachConflict($event, Arr::get($data, 'conflict_id'));
        if (Arr::has($data, 'date')) $event->setDate(Arr::get($data, 'date'));
        if (Arr::has($data, 'source_link')) $event->setSourceLink(Arr::get($data, 'source_link'));
        if (Arr::has($data, 'title_ru')) $event->setTitleRu(Arr::get($data, 'title_ru'));
        if (Arr::has($data, 'title_en')) $event->setTitleEn(Arr::get($data, 'title_en'));
        if (Arr::has($data, 'title_es')) $event->setTitleEs(Arr::get($data, 'title_es'));
        if (Arr::has($data, 'content_ru')) $event->setContentRu(Arr::get($data, 'content_ru'));
        if (Arr::has($data, 'content_en')) $event->setContentEn(Arr::get($data, 'content_en'));
        if (Arr::has($data, 'content_es')) $event->setContentEs(Arr::get($data, 'content_es'));
        if (Arr::has($data, 'published')) $event->setPublished(Arr::get($data, 'published'));
        if (Arr::has($data, 'latitude')) $event->setLatitude(Arr::get($data, 'latitude'));
        if (Arr::has($data, 'longitude')) $event->setLongitude(Arr::get($data, 'longitude'));

        if (Arr::has($data, 'locality_id')) $this->setLocality($event, Arr::get($data, 'locality_id'));
        if (Arr::has($data, 'event_status_id')) $this->setEventStatus($event, Arr::get($data, 'event_status_id'));
        if (Arr::has($data, 'event_type_id')) $this->setEventType($event, Arr::get($data, 'event_type_id'));

        //В зависимости от локали
        //при сохранении новости мы поле title записываем в поле title_ru [en/es]
        if (Arr::has($data, 'title') and $this->locale !== 'all') {
            $titleSetterName = 'setTitle' . $this->locale;
            $event->$titleSetterName(Arr::get($data, 'title'));
        }
        //content - то же самое
        if (Arr::has($data, 'content') and $this->locale !== 'all') {
            $contentSetterName = 'setContent' . $this->locale;
            $event->$contentSetterName(Arr::get($data, 'content'));
        }
    }

    /**
     * @param Event $event
     * @param string|null $localityId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function setLocality(Event $event, ?string $localityId)
    {
        if (!$localityId) {
            $event->setLocality(null);
            return;
        }

        /** @var Locality $locality */
        $locality = $this->em->find('App\Entities\References\Locality', $localityId);

        $event->setLocality($locality);
    }

    /**
     * @param Event $event
     * @param string|null $eventStatusId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function setEventStatus(Event $event, ?string $eventStatusId)
    {
        if (!$eventStatusId) {
            $event->setEventStatus(null);
            return;
        }

        /** @var $status EventStatus */
        $status = $this->em->find('App\Entities\References\EventStatus', $eventStatusId);

        $event->setEventStatus($status);
    }

    /**
     * @param Event $event
     * @param string|null $eventTypeId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function setEventType(Event $event, ?string $eventTypeId)
    {
        if (!$eventTypeId) {
            $event->setEventType(null);
            return;
        }

        /** @var $type EventType */
        $type = $this->em->find('App\Entities\References\EventType', $eventTypeId);

        $event->setEventType($type);
    }

    /**
     * @param Event $event
     * @param $conflictId
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function attachConflict(Event $event, $conflictId)
    {
        /** @var $conflict Conflict */
        $conflict = $this->em->find('App\Entities\Conflict', $conflictId);
        $event->setConflict($conflict);
    }

    /**
     * @param Event $event
     * @param array $photoUrls
     * @throws ORMException
     */
    private function syncPhotos(Event $event, array $photoUrls)
    {
        foreach ($event->getPhotos() as $oldPhoto) {
            $this->em->remove($oldPhoto);
        };

        //сохраняем фотографии
        foreach ($photoUrls as $photoUrl) {
            $photo = new Photo();
            $photo->setUrl($photoUrl);
            $this->em->persist($photo);
            $event->getPhotos()->add($photo);
        }
    }

    /**
     * @param Event $event
     * @param array $receivedVideos
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function syncVideos(Event $event, array $receivedVideos)
    {
        foreach ($event->getVideos() as $oldVideo) {
            $this->em->remove($oldVideo);
        };

        //сохраняем видео
        foreach ($receivedVideos as $receivedVideo) {
            $video = new Video();
            $video->setUrl($receivedVideo['url']);
            $video->setPreviewUrl(Arr::get($receivedVideo, 'preview_url'));
            $videoType = $this->em->find(
                'App\Entities\References\VideoType',
                Arr::get($receivedVideo, 'video_type_id')
            );
            $video->setVideoType($videoType);
            $this->em->persist($video);
            $event->getVideos()->add($video);
        }
    }

    /**
     * @param Event $event
     * @param array $receivedTags
     * @throws ORMException
     */
    private function syncTags(Event $event, array $receivedTags)
    {
        //Не удаляем старые теги из таблицы, они могут быть привязаны к другим событиям
        $event->getTags()->clear();

        //сохраняем тэги
        foreach ($receivedTags as $receivedTag) {
            $tagRepo = $this->em->getRepository('App\Entities\Tag');
            $tag = $tagRepo->findOneBy(['name' => $receivedTag]);
            if (!$tag) {
                $tag = new Tag;
                $tag->setName($receivedTag);
                $this->em->persist($tag);
            }
            $event->getTags()->add($tag);
        }
    }

    /**
     * @param Event $event
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws BusinessRuleValidationException
     */
    public function delete(Event $event)
    {
        $this->businessValidationService->validate([
            new NotAParentEvent($event)
        ]);

        $this->em->remove($event);
        $this->em->flush();

        //Если событие не было опубликовано, то посылаем уведомление автору о том, что его запись отклонена
        if (!$event->isPublished()) {
            $this->pushService->eventDeclined($event);
        }
    }

    /**
     * Вернуть родственников этого события, сгруппировав их по конфликтам.
     * Вернутся события из всех веток, которые имеют хоть одного общего предка с этим событием
     * @param Event $event
     * @return array
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRelatives(Event $event)
    {
        //Сначала берем конфликт этого события
        $conflict = $event->getConflict();
        if (!$conflict) return [];

        /** @var ConflictRepository $conflictRepository */
        $conflictRepository = $this->em->getRepository(Conflict::class);

        //Находим родоначальника ветки событий
        $rootParentConflict = $conflictRepository->getParentOfConflictBranch($conflict);
        //Находим всех его потомков
        $conflictsOfRoot = collect(
            $conflictRepository->childrenQueryBuilder($rootParentConflict, false, null, 'ASC', true)
                ->select('node.id') //node - такой псевдоним используется в childrenQueryBuilder
                ->addSelect('IDENTITY(node.parentEvent) as parent_event_id')
                ->addSelect('IDENTITY(node.parent) as parent_conflict_id')
                ->getQuery()
                ->getResult()
        );

        //приводим к плоскому массиву
        $conflictsOfRootIds = $conflictsOfRoot->pluck('id')->toArray();

        //Ищем события, относящиеся к этим конфликтам. Запрашиваем только необходимые поля. Оборачиваем в коллекцию
        $conflictEvents = collect(
            $this->em->createQueryBuilder()
                ->select('e.id, e.title_ru, e.title_en, e.title_es, IDENTITY(e.conflict) as conflict_id')
                ->from(Event::class, 'e')
                ->where($this->em->getExpressionBuilder()->in('e.conflict', $conflictsOfRootIds))
                ->orderBy('e.date')
                ->getQuery()
                ->getResult()
        );

        $locale = $this->locale;

        //Объединяем две коллекции (конфликты и события) в один массив
        return $conflictsOfRoot->map(function ($conflictItem) use ($conflictEvents, $locale) {
            $conflictItem['events'] = $conflictEvents->where('conflict_id', $conflictItem['id'])
                ->map(function ($eventItem) use ($locale) {
                    return $this->formatEventItemByLocale($eventItem, $locale);
                })
                ->values();
            return $conflictItem;
        })->toArray();
    }

    /**
     * На вход поступат массив с ключами [id, title_ru, title_en, title_es] и локаль.
     * На выходе будет либо тот же массив (если локаль = all), либо [id, title]
     * @param array $eventItem
     * @param string $locale
     * @return array
     */
    private function formatEventItemByLocale(array $eventItem, string $locale)
    {
        if ($this->locale === 'all') return $eventItem;

        //отображаем заголовок на нужной локали (если не переведено, то отображаем на русском),
        //считая, что на русский переведено всегда
        $title = (!in_array($eventItem['title_' . $locale], [null, '']))
            ? $eventItem['title_' . $locale]
            : $eventItem['title_ru'];

        return [
            'id'    => $eventItem['id'],
            'title' => $title
        ];
    }
}