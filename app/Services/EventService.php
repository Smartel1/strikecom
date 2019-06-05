<?php


namespace App\Services;

use App\Criteria\BelongsToConflicts;
use App\Criteria\HasTag;
use App\Criteria\HasLocalizedContent;
use App\Criteria\HasLocalizedTitle;
use App\Criteria\SafeBetween;
use App\Criteria\SafeEq;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\Photo;
use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use App\Entities\Tag;
use App\Entities\User;
use App\Entities\Video;
use App\Exceptions\BusinessRuleValidationException;
use App\Rules\NotAParentEvent;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\TransactionRequiredException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class EventService
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var BusinessValidationService
     */
    protected $businessValidationService;

    /**
     * NewsService constructor.
     * @param EntityManager $em
     * @param BusinessValidationService $bvs
     */
    public function __construct(EntityManager $em, BusinessValidationService $bvs)
    {
        $this->em = $em;
        $this->businessValidationService = $bvs;
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
            ->addCriteria(SafeBetween::make(
                'e.date',
                Arr::get($filters, 'date_from'),
                Arr::get($filters, 'date_to')
            ))
            ->addCriteria(SafeEq::make('e.eventStatus', Arr::get($filters, 'event_status_id')))
            ->addCriteria(SafeEq::make('e.eventType', Arr::get($filters, 'event_type_id')))
            ->addCriteria(HasTag::make('e', Arr::get($filters, 'tag_id')))
            ->addCriteria(BelongsToConflicts::make(Arr::get($filters, 'conflict_ids')))
            ->addCriteria(HasLocalizedTitle::make('e', app('locale')))
            ->addCriteria(HasLocalizedContent::make('e', app('locale')))
            ->orderBy('e.date', 'desc');

        //Фильтр "только избранные" (criteria не получилось сделать)
        if (Arr::get($filters, 'favourites')) {
            $queryBuilder
                ->andWhere(':user MEMBER OF e.likedUsers')
                ->setParameter('user', $user);
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
            ['path'=>request()->url()]
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
     */
    public function create($data, $user)
    {
        $this->em->beginTransaction();

        $event = new Event;
        $event->setUser($user);
        $this->attachConflict($event, Arr::get($data, 'conflict_id'));
        $this->fillNewsFields($event, $data);
        $this->syncPhotos($event, Arr::get($data, 'photo_urls', []));
        $this->syncVideos($event, Arr::get($data, 'videos', []));
        $this->syncTags($event, Arr::get($data, 'tags', []));

        $this->em->persist($event);
        $this->em->flush();
        $this->em->commit();

        return $event;
    }

    /**
     * @param Event $event
     * @param $data
     * @return Event
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function update(Event $event, $data)
    {
        $this->em->beginTransaction();

        $this->fillNewsFields($event, $data);
        $this->syncPhotos($event, Arr::get($data, 'photo_urls', []));
        $this->syncVideos($event, Arr::get($data, 'videos', []));
        $this->syncTags($event, Arr::get($data, 'tags', []));

        $this->em->persist($event);
        $this->em->flush();
        $this->em->commit();

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
    private function fillNewsFields(Event $event, $data)
    {
        //todo use Builder pattern
        $event->setDate(Arr::get($data, 'date'));
        $event->setSourceLink(Arr::get($data, 'source_link'));
        $event->setViews(0);
        $event->setTitleRu(Arr::get($data, 'title_ru'));
        $event->setTitleEn(Arr::get($data, 'title_en'));
        $event->setTitleEs(Arr::get($data, 'title_es'));
        $event->setContentRu(Arr::get($data, 'content_ru'));
        $event->setContentEn(Arr::get($data, 'content_en'));
        $event->setContentEs(Arr::get($data, 'content_es'));

        $this->setEventStatus($event, Arr::get($data, 'event_status_id'));
        $this->setEventType($event, Arr::get($data, 'event_type_id'));

        $locale = app('locale');

        //В зависимости от локали
        //при сохранении новости мы поле title записываем в поле title_ru [en/es]
        if (Arr::has($data, 'title') and $locale !== 'all') {
            $titleSetterName = 'setTitle' . $locale;
            $event->$titleSetterName(Arr::get($data, 'title'));
        }
        //content - то же самое
        if (Arr::has($data, 'content') and $locale !== 'all') {
            $contentSetterName = 'setContent' . $locale;
            $event->$contentSetterName(Arr::get($data, 'content'));
        }
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
        foreach ($event->getPhotos() as $oldPhoto){
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
        foreach ($event->getVideos() as $oldVideo){
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
        //Не удаляем старые теги из таблицы, они могут быть привязаны к дургим новостям
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
    }
}