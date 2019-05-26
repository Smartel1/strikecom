<?php


namespace App\Services;

use App\Criteria\HasTag;
use App\Criteria\HasLocalizedContent;
use App\Criteria\HasLocalizedTitle;
use App\Criteria\SafeGTE;
use App\Criteria\SafeLTE;
use App\Entities\News;
use App\Entities\Photo;
use App\Entities\Tag;
use App\Entities\User;
use App\Entities\Video;
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
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * NewsService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Найти новости по фильтрам и вернуть с пагинацией
     * @param $filters array фильтры
     * @param $perPage int размер выборки
     * @param $page int номер страницы
     * @return LengthAwarePaginator
     * @throws QueryException
     */
    public function index($filters, $perPage, $page)
    {
        //Запрашиваем новости с их связанными сущностями, сортируя по убыванию даты
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('n, SIZE(n.comments) comments_count')
            ->from('App\Entities\News', 'n')
            ->leftJoin('n.user', 'u')
            ->leftJoin('n.photos', 'p')
            ->leftJoin('n.videos', 'v')
            ->leftJoin('n.tags', 't')
            ->addCriteria(SafeGTE::make('n.date', Arr::get($filters, 'date_from')))
            ->addCriteria(SafeLTE::make('n.date', Arr::get($filters, 'date_to')))
            ->addCriteria(HasTag::make('n', Arr::get($filters, 'tag_id')))
            ->addCriteria(HasLocalizedTitle::make('n', app('locale')))
            ->addCriteria(HasLocalizedContent::make('n', app('locale')))
            ->orderBy('n.date', 'desc');

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
     * @param $user
     * @return News
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create($data, $user)
    {
        $this->em->beginTransaction();

        $news = new News;
        $news->setUser($user);
        $this->fillNewsFields($news, $data);
        $this->syncPhotos($news, Arr::get($data, 'photo_urls', []));
        $this->syncVideos($news, Arr::get($data, 'videos', []));
        $this->syncTags($news, Arr::get($data, 'tags', []));

        $this->em->persist($news);
        $this->em->flush();
        $this->em->commit();

        return $news;
    }

    /**
     * @param News $news
     * @param $data
     * @return News
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function update(News $news, $data)
    {
        $this->em->beginTransaction();

        $this->fillNewsFields($news, $data);
        $this->syncPhotos($news, Arr::get($data, 'photo_urls', []));
        $this->syncVideos($news, Arr::get($data, 'videos', []));
        $this->syncTags($news, Arr::get($data, 'tags', []));

        $this->em->persist($news);
        $this->em->flush();
        $this->em->commit();

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
     */
    private function fillNewsFields(News $news, $data)
    {
        $news->setDate(Arr::get($data, 'date'));
        $news->setSourceLink(Arr::get($data, 'source_link'));
        $news->setViews(0);
        $news->setTitleRu(Arr::get($data, 'title_ru'));
        $news->setTitleEn(Arr::get($data, 'title_en'));
        $news->setTitleEs(Arr::get($data, 'title_es'));
        $news->setContentRu(Arr::get($data, 'content_ru'));
        $news->setContentEn(Arr::get($data, 'content_en'));
        $news->setContentEs(Arr::get($data, 'content_es'));

        $locale = app('locale');

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
        //Не удаляем старые теги из таблицы, они могут быть привязаны к дургим новостям
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
    }
}