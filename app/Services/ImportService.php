<?php

namespace App\Services;

use App\Entities\Comment;
use App\Entities\Conflict;
use App\Entities\Event;
use App\Entities\News;
use App\Entities\Photo;
use App\Entities\References\ConflictReason;
use App\Entities\References\ConflictResult;
use App\Entities\References\EventStatus;
use App\Entities\References\EventType;
use App\Entities\References\Industry;
use App\Entities\References\VideoType;
use App\Entities\User;
use App\Entities\Video;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use LaravelDoctrine\ORM\Facades\EntityManager;

class ImportService
{
    /**
     * @param $json
     * @return ArrayCollection
     */
    public function fetchConflictReasons($json)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            $reason = new ConflictReason;
            $reason->setNameRu(array_get($item, 'name'));
            $reason->setNameEn(array_get($item, 'name_en'));
            $reason->setNameEs(array_get($item, 'name_es'));

            $reason->_id = $item['_id'];
            EntityManager::persist($reason);
            $collection->add($reason);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return ArrayCollection
     */
    public function fetchIndustries($json)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            $industry = new Industry;
            $industry->setNameRu(array_get($item, 'name'));
            $industry->setNameEn(array_get($item, 'name_en'));
            $industry->setNameEs(array_get($item, 'name_es'));

            $industry->_id = $item['_id'];
            EntityManager::persist($industry);
            $collection->add($industry);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return ArrayCollection
     */
    public function fetchConflictResults($json)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            $conflictResult = new ConflictResult;
            $conflictResult->setNameRu(array_get($item, 'name'));
            $conflictResult->setNameEn(array_get($item, 'name_en'));
            $conflictResult->setNameEs(array_get($item, 'name_es'));

            $conflictResult->_id = $item['_id'];
            EntityManager::persist($conflictResult);
            $collection->add($conflictResult);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return ArrayCollection
     */
    public function fetchEventStatuses($json)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            $status = new EventStatus;
            $status->setNameRu(array_get($item, 'name'));
            $status->setNameEn(array_get($item, 'name_en'));
            $status->setNameEs(array_get($item, 'name_es'));

            $status->_id = $item['_id'];
            EntityManager::persist($status);
            $collection->add($status);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return ArrayCollection
     */
    public function fetchEventTypes($json)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            $type = new EventType;
            $type->setNameRu(array_get($item, 'name'));
            $type->setNameEn(array_get($item, 'name_en'));
            $type->setNameEs(array_get($item, 'name_es'));

            $type->_id = $item['_id'];
            EntityManager::persist($type);
            $collection->add($type);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return ArrayCollection
     */
    public function fetchUsers($json)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            $user = new User;
            $user->setUuid(array_get($item, 'uuid'));
            $user->setName(array_get($item, 'name'));
            $user->setEmail(array_get($item, 'emailOrNumber'));
            $user->setFcm(array_get($item, 'tokenFCM'));
            $user->setImageUrl(array_get($item, 'image'));
            $user->setRoles((boolean)array_get($item, 'admin') ? ['ADMIN'] : []);
            $user->setPush(array_get($item, 'push'));

            $user->_id = $item['_id'];
            $user->favouritePosts = $item['favorites'];
            EntityManager::persist($user);
            $collection->add($user);
        }

        return $collection;
    }

    /**
     * @param $json
     * @return ArrayCollection
     */
    public function fetchConflicts($json)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            if (!array_has($item, 'lat') or !array_has($item, 'lng')) {
                $uuid = $item['_id'];
                \Log::error("Не указаны координаты конфликта $uuid");
                continue;
            }

            $conflict = new Conflict;
            $conflict->setDateFrom(array_get($item, 'date_start'));
            $conflict->setDateTo(array_get($item, 'date_end'));
            $conflict->setLatitude(array_get($item, 'lat'));
            $conflict->setLongitude(array_get($item, 'lng'));

            $conflict->_id = $item['_id'];
            EntityManager::persist($conflict);
            $collection->add($conflict);
        }

        return $collection;
    }

    /**
     * @param $json
     * @param ArrayCollection $users
     * @param ArrayCollection $conflicts
     * @param ArrayCollection $eventStatuses
     * @param ArrayCollection $eventTypes
     * @param $factories
     * @param ArrayCollection $industries
     * @param ArrayCollection $reasons
     * @param ArrayCollection $results
     * @return ArrayCollection
     */
    public function fetchEvents($json, ArrayCollection $users, ArrayCollection $conflicts, ArrayCollection $eventStatuses,
                                ArrayCollection $eventTypes, $factories, ArrayCollection $industries,
                                ArrayCollection $reasons, ArrayCollection $results)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            $uuid = $item['_id'];

            $disputUID = array_get($item, 'inDisput');

            //Не состоит в конфликте - значит это новость
            if (!$disputUID) continue;

            /** @var Conflict $conflict */
            $conflict = $conflicts->matching(Criteria::create()->where(Criteria::expr()->eq('_id', $disputUID)))->first();

            if (!$conflict) {
                \Log::error("Не удалось привязать конфликт события $uuid");
                continue;
            }

            $eventStatus = null;

            if (array_get($item, 'status')) {
                /** @var EventStatus $eventStatus */
                $eventStatus = $eventStatuses->matching(
                    Criteria::create()->where(Criteria::expr()->eq('_id', array_get($item, 'status')))
                )->first();

                if (!$eventStatus) {
                    \Log::error("Не удалось привязать статус события $uuid");
                    continue;
                }
            }

            $eventType = null;

            if (array_get($item, 'type')) {
                /** @var EventType $eventType */
                $eventType = $eventTypes->matching(
                    Criteria::create()->where(Criteria::expr()->eq('_id', array_get($item, 'type')))
                )->first();

                if (!$eventType) {
                    \Log::error("Не удалось привязать тип события $uuid");
                    continue;
                }
            }

            $author = null;

            if (array_get($item, 'creator')) {
                /** @var User $author */
                $author = $users->matching(
                    Criteria::create()->where(Criteria::expr()->eq('uuid', array_get($item, 'creator')))
                )->first();

                if (!$author) {
                    \Log::error("Не удалось привязать автора к событию $uuid");
                    $author = null;
                }
            }

            $event = new Event;

            $event->setTitleRu(array_get($item, 'name'));
            $event->setTitleEn(array_get($item, 'name_en'));
            $event->setTitleEs(array_get($item, 'name_es'));
            $event->setContentRu(array_get($item, 'content'));
            $event->setContentEn(array_get($item, 'content_en'));
            $event->setContentEs(array_get($item, 'content_es'));
            $event->setDate(array_get($item, 'date'));
            $event->setLatitude(array_get($item, 'lat'));
            $event->setLongitude(array_get($item, 'lng'));
            $event->setViews(array_get($item, 'count_view', 0));
            $event->setSourceLink(array_get($item, 'link'));
            $event->setConflict($conflict);
            $event->setEventStatus($eventStatus);
            $event->setEventType($eventType);
            $event->setAuthor($author);
            $event->setPublished(array_get($item, 'isModerated', false));

            $event->_id = $item['_id'];
            EntityManager::persist($event);
            $collection->add($event);

            $imageUrls = (array)array_get($item, 'images');

            //сохраняем фотографии
            foreach ($imageUrls as $imageUrl) {
                $photo = new Photo();
                $photo->setUrl($imageUrl);
                EntityManager::persist($photo);
                $event->getPhotos()->add($photo);
            }

            $videos = (array)array_get($item, 'videos');

            //сохраняем видео
            foreach ($videos as $receivedVideo) {
                $type = array_get($receivedVideo, 'type');
                if ($type === -2) continue;

                $video = new Video();
                $video->setUrl(array_get($receivedVideo, 'link'));
                $video->setPreviewUrl(array_get($receivedVideo, 'image'));
                $video->setVideoType($this->defineVideoType($type));
                EntityManager::persist($video);
                $event->getVideos()->add($video);
            }

            $inn = array_get($item, 'inn');

            if ($inn) {
                $company = $factories->where('_id', $inn)->first();

                if (!$company) {
                    \Log::error("Не удалось привязать фабрику события $uuid к конфликту, но событие создано");
                } else {
                    $event->getConflict()->setCompanyName($company['name']);
                }

            }

            $industry_id = array_get($item, 'industry');

            if ($industry_id) {
                $industry = $industries->matching(
                    Criteria::create()->where(Criteria::expr()->eq('_id', $industry_id))
                )->first();

                if (!$industry) {
                    \Log::error("Не удалось привязать отрасль события $uuid к конфликту, но событие создано");
                } else {
                    $event->getConflict()->setIndustry($industry);
                }
            }

            $reason_id = array_get($item, 'cause');

            if ($reason_id) {
                $reason = $reasons->matching(
                    Criteria::create()->where(Criteria::expr()->eq('_id', $reason_id))
                )->first();

                if (!$reason) {
                    \Log::error("Не удалось привязать причину события $uuid к конфликту, но событие создано");
                } else {
                    $event->getConflict()->setConflictReason($reason);
                }
            }

            $result_id = array_get($item, 'result');

            if ($result_id) {
                $result = $results->matching(
                    Criteria::create()->where(Criteria::expr()->eq('_id', $result_id))
                )->first();

                if (!$result) {
                    \Log::error("Не удалось привязать результат события $uuid к конфликту, но событие создано");
                } else {
                    $event->getConflict()->setConflictResult($result);
                }
            }

//            $regionName = array_get($item, 'region');
//
//            if ($regionName) {
//                $region = $this->defineRegion($regionName);
//
//                if (!$region) {
//                    \Log::error("Не удалось привязать регион события $uuid к конфликту, но событие создано");
//                } else {
//                    $event->conflict->region_id = $region->id;
//                }
//            }

            EntityManager::persist($event->getConflict());
            EntityManager::persist($event);
        }

        return $collection;
    }

    /**
     * @param $json
     * @param ArrayCollection $users
     * @return ArrayCollection
     */
    public function fetchNews($json, ArrayCollection $users)
    {
        $collection = new ArrayCollection;

        foreach (json_decode($json, true) as $item) {

            if (array_has($item, 'inDisput')) continue;

            $uuid = $item['_id'];

            $author = null;

            if (array_get($item, 'creator')) {
                $author = $users->matching(
                    Criteria::create()->where(Criteria::expr()->eq('uuid', array_get($item, 'creator')))
                )->first();

                if (!$author) {
                    \Log::error("Не удалось привязать пользователя к новости $uuid");
                    $author = null;
                }
            }

            $news = new News;
            $news->setTitleRu(array_get($item, 'name'));
            $news->setTitleEn(array_get($item, 'name_en'));
            $news->setTitleEs(array_get($item, 'name_es'));
            $news->setContentRu(array_get($item, 'content'));
            $news->setContentEn(array_get($item, 'content_en'));
            $news->setContentEs(array_get($item, 'content_es'));
            $news->setDate(array_get($item, 'date'));
            $news->setViews(array_get($item, 'count_view', 0));
            $news->setSourceLink(array_get($item, 'link'));
            $news->setAuthor($author);
            $news->setPublished(array_get($item, 'isModerated', false));

            $news->_id = $item['_id'];

            $collection->add($news);

            $imageUrls = (array)array_get($item, 'images');

            foreach ($imageUrls as $imageUrl) {
                $photo = new Photo();
                $photo->setUrl($imageUrl);
                EntityManager::persist($photo);
                $news->getPhotos()->add($photo);
            }

            $videos = (array)array_get($item, 'videos');

            foreach ($videos as $receivedVideo) {
                $type = array_get($receivedVideo, 'type');
                if ($type === -2) continue;

                $video = new Video();
                $video->setUrl(array_get($receivedVideo, 'link'));
                $video->setPreviewUrl(array_get($receivedVideo, 'image'));
                $video->setVideoType($this->defineVideoType($type));
                EntityManager::persist($video);
                $news->getVideos()->add($video);
            }

            EntityManager::persist($news);
        }

        return $collection;
    }

    /**
     * @param $users
     * @param $events
     * @param $news
     */
    public function fetchFavourites($users, ArrayCollection $events, ArrayCollection $news)
    {
        /** @var User $user */
        foreach ($users as $user) {
            foreach (object_get($user, 'favouritePosts', []) as $postId) {
                /** @var Event $event */
                $event = $events->matching(
                    Criteria::create()->where(Criteria::expr()->eq('_id', $postId))
                )->first();

                if ($event) {
                    $user->getFavouriteEvents()->add($event);
                    EntityManager::persist($event);
                    continue;
                }
                /** @var News $post */
                $post = $news->matching(
                    Criteria::create()->where(Criteria::expr()->eq('_id', $postId))
                )->first();

                if ($post) {
                    $user->getFavouriteNews()->add($post);
                    EntityManager::persist($post);
                }
            }
        }
    }

    /**
     * @param $json
     * @param ArrayCollection $users
     * @param ArrayCollection $events
     * @param ArrayCollection $news
     */
    public function fetchComments($json, ArrayCollection $users, ArrayCollection $events, ArrayCollection $news)
    {
        foreach (json_decode($json, true) as $item) {
            $uuid = $item['_id'];

            $author = null;

            if (array_get($item, 'idUser')) {
                $author = $users->matching(
                    Criteria::create()->where(Criteria::expr()->eq('uuid', array_get($item, 'idUser')))
                )->first();

                if (!$author) {
                    \Log::error("Не удалось привязать пользователя к комментарию $uuid");
                    $author = null;
                }
            }

            $event = $events->matching(
                Criteria::create()->where(Criteria::expr()->eq('_id', array_get($item, 'idPost')))
            )->first();

            $post = $news->matching(
                Criteria::create()->where(Criteria::expr()->eq('_id', array_get($item, 'idPost')))
            )->first();

            $date = \DateTime::createFromFormat('U', array_get($item, 'date', now()));

            $comment = new Comment;
            $comment->setContent(array_get($item, 'content'));
            $comment->setUser($author);
            $comment->setCreatedAt($date);
            $comment->setUpdatedAt($date);

            if ($event) $comment->getEvents()->add($event);
            if ($post) $comment->getNews()->add($post);

            //не записываем картинки и жалобы, так как их нет в дампе

            EntityManager::persist($comment);
        }
    }

    /**
     * @param $type
     * @return mixed
     */
    private function defineVideoType($type)
    {
        if ($type === 0) return EntityManager::getRepository(VideoType::class)->findOneBy(['code' => 'youtube_link']);
        if ($type === 1) return EntityManager::getRepository(VideoType::class)->findOneBy(['code' => 'vk_link']);
        return EntityManager::getRepository(VideoType::class)->findOneBy(['code' => 'other']);
    }

    public function truncateTable($tableName)
    {
        $connection = EntityManager::getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('DELETE FROM '.$tableName);
            $connection->query('ALTER SEQUENCE '.$tableName.'_id_seq RESTART WITH 1');
            // Beware of ALTER TABLE here--it's another DDL statement and will cause
            // an implicit commit.
            $connection->commit();
        } catch (\Exception $e) {dd($e);
            $connection->rollback();
        }
    }
}