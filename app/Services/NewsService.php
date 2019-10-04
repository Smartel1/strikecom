<?php


namespace App\Services;

use App\Criteria\ByPermission;
use App\Criteria\HasTag;
use App\Criteria\HasLocalizedContent;
use App\Criteria\HasLocalizedTitle;
use App\Criteria\SafeBetween;
use App\Criteria\SafeEq;
use App\DTO\LocalesDTO;
use App\Entities\News;
use App\Entities\Photo;
use App\Entities\Tag;
use App\Entities\User;
use App\Entities\Video;
use App\Exceptions\BusinessRuleValidationException;
use App\Rules\UserCanModerate;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\TransactionRequiredException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class NewsService
{
    protected $em;

    private $businessValidationService;

    private $pushService;

    public function __construct(
        EntityManager $em,
        BusinessValidationService $businessValidationService,
        PushService $pushService
    )
    {
        $this->em = $em;
        $this->businessValidationService = $businessValidationService;
        $this->pushService = $pushService;
    }

    /**
     * Найти новости по фильтрам и вернуть с пагинацией
     * @param $filters array фильтры
     * @param $perPage int размер выборки
     * @param $page int номер страницы
     * @param $locale
     * @param User|null $user
     * @return LengthAwarePaginator
     * @throws QueryException
     */
    public function index($filters, $perPage, $page, $locale, ?User $user)
    {
        //Запрашиваем новости с их связанными сущностями, сортируя по убыванию даты
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('n, SIZE(n.comments) comments_count')
            ->from('App\Entities\News', 'n')
            ->leftJoin('n.photos', 'p')
            ->leftJoin('n.videos', 'v')
            ->leftJoin('n.tags', 't')
            ->addCriteria(ByPermission::make($user))
            ->addCriteria(SafeBetween::make(
                'n.date',
                Arr::has($filters, 'date_from')
                    ? Datetime::createFromFormat('U', Arr::get($filters, 'date_from'))
                    : null,
                Arr::has($filters, 'date_to')
                    ? Datetime::createFromFormat('U', Arr::get($filters, 'date_to'))
                    : null
            ))
            ->addCriteria(SafeEq::make('n.published', Arr::get($filters, 'published')))
            ->addCriteria(HasTag::make('n', Arr::get($filters, 'tag_id')))
            ->addCriteria(HasLocalizedTitle::make('n', $locale))
            ->addCriteria(HasLocalizedContent::make('n', $locale))
            ->orderBy('n.date', 'desc');

        //Фильтр "только избранные" (criteria не получилось сделать)
        if (Arr::get($filters, 'favourites')) {
            $queryBuilder
                ->andWhere(':user MEMBER OF n.likedUsers')
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
            ['path' => request()->url()]
        );

        return $laravelPaginator;
    }

    /**
     * Создать новость
     * @param $data
     * @param $locale
     * @param User $user
     * @return News
     * @throws BusinessRuleValidationException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function create($data, $locale, User $user)
    {
        //Если пользователь хочет сразу опубликовать новость, он должен быть модератором
        $this->businessValidationService->validate([
            (new UserCanModerate($user))->when(Arr::get($data, 'published'))
        ]);

        $this->em->beginTransaction();

        $news = new News;
        $news->setAuthor($user);
        $this->fillNewsFields($news, $data, $locale);
        $this->syncPhotos($news, Arr::get($data, 'photo_urls', []));
        $this->syncVideos($news, Arr::get($data, 'videos', []));
        $this->syncTags($news, Arr::get($data, 'tags', []));

        $this->em->persist($news);
        $this->em->flush();
        $this->em->commit();

        //Если обычный пользователь предлагает новость, посылаем пуш админам; если новость публикуется - то всем
        if (!$news->isPublished()) {
            $this->pushService->newsCreatedByUser($news);
        } else {
            $this->pushService->newsPublished($news, new LocalesDTO(
                !is_null($news->getTitleRu()),
                !is_null($news->getTitleEn()),
                !is_null($news->getTitleEs())
            ));
        }

        return $news;
    }

    /**
     * @param News $news
     * @param $data
     * @param $locale
     * @param $user
     * @return News
     * @throws BusinessRuleValidationException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function update(News $news, $data, $locale, $user)
    {
        $userChangesPublishStatus = (
            Arr::has($data, 'published')
            and
            (bool) Arr::get($data, 'published') !== $news->isPublished()
        );

        $this->businessValidationService->validate([
            (new UserCanModerate($user))->when($userChangesPublishStatus)
        ]);

        //Перед изменением смотрим, на каких языках уже есть переводы (чтобы не послать пуш второй раз)
        $nullLocalesBeforeUpdate = new LocalesDTO(
            is_null($news->getTitleRu()),
            is_null($news->getTitleEn()),
            is_null($news->getTitleEs())
        );

        $this->em->beginTransaction();

        $this->fillNewsFields($news, $data, $locale);
        if (Arr::has($data, 'photo_urls')) $this->syncPhotos($news, Arr::get($data, 'photo_urls', []));
        if (Arr::has($data, 'videos')) $this->syncVideos($news, Arr::get($data, 'videos', []));
        if (Arr::has($data, 'tags')) $this->syncTags($news, Arr::get($data, 'tags', []));

        $this->em->persist($news);
        $this->em->flush();
        $this->em->commit();

        //Если новость опубликована, то посылаем пуши в те топики по языкам, на которые переведена новость в этом обновлении.
        //Если новость публикуется в этом действии, то посылаются пуши на все языки, на которые локализована новость
        if ($news->isPublished()) {
            $localesToPush = new LocalesDTO(
                ($nullLocalesBeforeUpdate->isRu() or $userChangesPublishStatus) and !is_null($news->getTitleRu()),
                ($nullLocalesBeforeUpdate->isEn() or $userChangesPublishStatus) and !is_null($news->getTitleEn()),
                ($nullLocalesBeforeUpdate->isEs() or $userChangesPublishStatus) and !is_null($news->getTitleEs())
            );

            $this->pushService->newsPublished($news, $localesToPush);

            //Если новость публикуется в этом действии, то автору посылается уведомление об этом
            if ($userChangesPublishStatus) {
                $this->pushService->sendYourPostModerated($news);
            }
        }

        return $news;
    }

    /**
     * Пометить новость как избранное или снять отметку
     * @param News $news
     * @param User $user
     * @param bool $isFavourite
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setFavourite(News $news, User $user, bool $isFavourite)
    {
        $currentFavourites = $user->getFavouriteNews();

        if ($isFavourite) {
            //Добавим в избранное, если ещё не в избранном
            if (!$currentFavourites->contains($news)) {
                $currentFavourites->add($news);
            }
        } else {
            $currentFavourites->removeElement($news);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param News $news
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function incrementViews(News $news)
    {
        $news->setViews($news->getViews() + 1);

        $this->em->flush();
    }

    /**
     * Заполнить поля новости переданными данными
     * @param News $news
     * @param $data
     * @param $locale
     */
    private function fillNewsFields(News $news, $data, $locale)
    {
        //Устанавливаем/обновляем только те поля, которые переданы
        if (Arr::has($data, 'date')) $news->setDate(Arr::get($data, 'date'));
        if (Arr::has($data, 'source_link')) $news->setSourceLink(Arr::get($data, 'source_link'));
        if (Arr::has($data, 'title_ru')) $news->setTitleRu(Arr::get($data, 'title_ru'));
        if (Arr::has($data, 'title_en')) $news->setTitleEn(Arr::get($data, 'title_en'));
        if (Arr::has($data, 'title_es')) $news->setTitleEs(Arr::get($data, 'title_es'));
        if (Arr::has($data, 'content_ru')) $news->setContentRu(Arr::get($data, 'content_ru'));
        if (Arr::has($data, 'content_en')) $news->setContentEn(Arr::get($data, 'content_en'));
        if (Arr::has($data, 'content_es')) $news->setContentEs(Arr::get($data, 'content_es'));
        if (Arr::has($data, 'published'))  $news->setPublished(Arr::get($data, 'published'));

        //В зависимости от локали
        //при сохранении новости мы поле title записываем в поле title_ru [en/es]
        if (Arr::has($data, 'title') and $locale !== 'all') {
            $titleSetterName = 'setTitle' . $locale;
            $news->$titleSetterName(Arr::get($data, 'title'));
        }
        //content - то же самое
        if (Arr::has($data, 'content') and $locale !== 'all') {
            $contentSetterName = 'setContent' . $locale;
            $news->$contentSetterName(Arr::get($data, 'content'));
        }
    }

    /**
     * @param News $news
     * @param array $photoUrls
     * @throws ORMException
     */
    private function syncPhotos(News $news, array $photoUrls)
    {
        foreach ($news->getPhotos() as $oldPhoto) {
            $this->em->remove($oldPhoto);
        };

        //сохраняем фотографии
        foreach ($photoUrls as $photoUrl) {
            $photo = new Photo();
            $photo->setUrl($photoUrl);
            $this->em->persist($photo);
            $news->getPhotos()->add($photo);
        }
    }

    /**
     * @param News $news
     * @param array $receivedVideos
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function syncVideos(News $news, array $receivedVideos)
    {
        foreach ($news->getVideos() as $oldVideo) {
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
            $news->getVideos()->add($video);
        }
    }

    /**
     * @param News $news
     * @param array $receivedTags
     * @throws ORMException
     */
    private function syncTags(News $news, array $receivedTags)
    {
        //Не удаляем старые теги из таблицы, они могут быть привязаны к другим новостям
        $news->getTags()->clear();

        //сохраняем тэги
        foreach ($receivedTags as $receivedTag) {
            $tagRepo = $this->em->getRepository('App\Entities\Tag');
            $tag = $tagRepo->findOneBy(['name' => $receivedTag]);
            if (!$tag) {
                $tag = new Tag;
                $tag->setName($receivedTag);
                $this->em->persist($tag);
            }
            $news->getTags()->add($tag);
        }
    }

    /**
     * @param News $news
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(News $news)
    {
        $this->em->remove($news);
        $this->em->flush();

        //Если новость не была опубликована, то посылаем уведомление автору о том, что его запись отклонена
        if (!$news->isPublished()) {
            $this->pushService->newsDeclined($news);
        }
    }
}