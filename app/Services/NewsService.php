<?php


namespace App\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

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
            ->select('n')
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
}