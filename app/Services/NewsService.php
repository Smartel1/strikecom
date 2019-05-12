<?php


namespace App\Services;

use App\Entities\News;
use App\Entities\Photo;
use App\Entities\Tag;
use App\Entities\Video;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
     */
    public function index($filters, $perPage, $page)
    {
        $expr = $this->em->getExpressionBuilder();

        //Запрашиваем новости с их связанными сущностями, сортируя по убыванию даты
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('n, SIZE(n.comments) comments_count')
            ->from('App\Entities\News', 'n')
            ->leftJoin('n.user', 'u')
            ->leftJoin('n.photos', 'p')
            ->leftJoin('n.videos', 'v')
            ->leftJoin('n.tags', 't')
            ->orderBy('n.date', 'desc');

        //Если передан фильтр по тэгу, добавляем условие
        $tagId = array_get($filters, 'tag_id');

        if ($tagId) {
            $queryBuilder->andWhere($expr->eq('t.id', $tagId));
        }

        //Если указана конкретная локаль, то выводим только локализованные записи
        $locale = app('locale');

        if ($locale !== 'all') {
            $queryBuilder
                ->andWhere($expr->isNotNull('n.title_' . $locale))
                ->andWhere($expr->isNotNull('n.content_' . $locale));
        }

        //Пагинируем результат
        $doctrinePaginator = new Paginator(
            $queryBuilder->setFirstResult($perPage * ($page - 1))->setMaxResults($perPage)->getQuery()
        );

        //Переводим в формат, понятный laravel
        $laravelPaginator = new LengthAwarePaginator(
            collect($doctrinePaginator),
            $doctrinePaginator->count(),
            $perPage,
            $page,
            ['path'=>request()->url()]
        );

        return $laravelPaginator;
    }

    /**
     * Создать новость
     * @param $data
     * @param $user
     * @return News
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
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
     * @param News $news
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
     * @throws \Doctrine\ORM\ORMException
     */
    private function syncPhotos(News $news, array $photoUrls)
    {
        foreach ($news->getPhotos() as $oldPhoto){
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    private function syncVideos(News $news, array $receivedVideos)
    {
        foreach ($news->getVideos() as $oldVideo){
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
     * @throws \Doctrine\ORM\ORMException
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(News $news)
    {
        $this->em->remove($news);

        $this->em->flush();
    }
}