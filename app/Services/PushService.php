<?php


namespace App\Services;


use App\DTO\LocalesDTO;
use App\Entities\Event;
use App\Entities\Interfaces\Post;
use App\Entities\News;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

/**
 * Сервис занимается формированием пуш-уведомлений.
 * Структуру этих уведомлений частично выдержал из старого приложения, некоторые данные вызывают вопросы
 */
class PushService
{
    /** @var Messaging */
    protected $messaging;

    //Топик, на который подписаны админы
    protected $TOPIC_ADMIN = 'admin';
    //Топики для пользователей делятся по языкам.
    protected $TOPIC_NEWS_RU = 'news_ru';
    protected $TOPIC_NEWS_EN = 'news_en';
    protected $TOPIC_NEWS_ES = 'news_es';
    protected $TOPIC_EVENTS_RU = 'events_ru';
    protected $TOPIC_EVENTS_EN = 'events_en';
    protected $TOPIC_EVENTS_ES = 'events_es';

    public function __construct()
    {
        $this->messaging = app('firebase')->getMessaging();
    }

    /**
     * Отправить оповещение админам о том, что пользователь предложил новость
     * @param News $news
     */
    public function newsCreatedByUser(News $news)
    {
        $author = $news->getAuthor();

        $message = CloudMessage::withTarget(Messaging\MessageTarget::TOPIC, $this->TOPIC_ADMIN)
            ->withNotification(
                Notification::create('ЗабастКом', 'На модерации новость от ' . $author->getName())
            )
            ->withData([
                'id'         => (string)$news->getId(),
                'title_ru'   => $news->getTitleRu() ? $news->getTitleRu() : 'Новость от ' . $author->getName(),
                'title_en'   => $news->getTitleEn() ? $news->getTitleEn() : 'News from ' . $author->getName(),
                'title_es'   => $news->getTitleEs() ? $news->getTitleEs() : 'Noticias de ' . $author->getName(),
                'creator_id' => (string)$author->getId(),
                'type'       => 'admin', //не знаю, зачем это передаётся
            ]);

        $this->send($message);
    }

    /**
     * Отправить оповещение админам о том, что пользователь предложил событие
     * @param Event $event
     */
    public function eventCreatedByUser(Event $event)
    {
        $author = $event->getAuthor();

        $message = CloudMessage::withTarget(Messaging\MessageTarget::TOPIC, $this->TOPIC_ADMIN)
            ->withNotification(
                Notification::create('ЗабастКом', 'На модерации событие от ' . $author->getName())
            )
            ->withData([
                'id'         => (string)$event->getId(),
                'title_ru'   => $event->getTitleRu() ? $event->getTitleRu() : 'Новость от ' . $author->getName(),
                'title_en'   => $event->getTitleEn() ? $event->getTitleEn() : 'News from ' . $author->getName(),
                'title_es'   => $event->getTitleEs() ? $event->getTitleEs() : 'Noticias de ' . $author->getName(),
                'creator_id' => (string)$author->getId(),
                'type'       => 'admin', //не знаю, зачем это передаётся
            ]);

        $this->send($message);
    }

    /**
     * Отправить оповещение всем пользователям о том, что опубликована свежая новость
     * Оповещения посылаются в три топика (для каждого языка - по необходимость)
     * Ещё один пуш шлём автору
     * @param News $news
     * @param LocalesDTO $locales определяет, по каким языкам нужно разослать
     */
    public function newsPublished(News $news, LocalesDTO $locales)
    {
        //Посылаем уведомление в топик русскоязычных пользователей
        if ($locales->isRu()) {
            $this->sendPublishNotification($news, 'ru');
        }

        //Посылаем уведомление в топик англоязычных пользователей
        if ($locales->isEn()) {
            $this->sendPublishNotification($news, 'en');
        }

        //Посылаем уведомление в топик испаноязычных пользователей
        if ($locales->isEs()) {
            $this->sendPublishNotification($news, 'es');
        }
    }

    /**
     * Отправить оповещение всем пользователям о том, что опубликовано событие
     * Оповещения посылаются в три топика (для каждого языка)
     * Ещё один пуш шлём автору
     * @param Event $event
     * @param LocalesDTO $locales
     */
    public function eventPublished(Event $event, LocalesDTO $locales)
    {
        //Посылаем уведомление в топик русскоязычных пользователей
        if ($locales->isRu()) {
            $this->sendPublishNotification($event, 'ru');
        }

        //Посылаем уведомление в топик англоязычных пользователей
        if ($locales->isEn()) {
            $this->sendPublishNotification($event, 'en');
        }

        //Посылаем уведомление в топик испаноязычных пользователей
        if ($locales->isEs()) {
            $this->sendPublishNotification($event, 'es');
        }
    }

    /**
     * Послать автору новости/события уведомление, что его пост одобрен и опубликован
     * @param Post $post
     */
    public function sendYourPostModerated(Post $post)
    {
        if ($post->getAuthor()->getFcm()) {
            $messageData = [
                'id'         => (string)$post->getId(),
                'title_ru'   => (string)$post->getTitleRu(),
                'title_en'   => (string)$post->getTitleEn(),
                'title_es'   => (string)$post->getTitleEs(),
                'creator_id' => (string)$post->getAuthor()->getId(),
                'type'       => 'moderated', //не знаю, зачем это передаётся
            ];

            if ($post instanceof Event) {
                $messageData['lat'] = $post->getLatitude();
                $messageData['lng'] = $post->getLongitude();
            }

            $messageToAuthor = CloudMessage::withTarget(Messaging\MessageTarget::TOKEN, $post->getAuthor()->getFcm())
                ->withNotification(Notification::create('ЗабастКом', 'Предложенный Вами пост прошел модерацию'))
                ->withData($messageData);

            $this->send($messageToAuthor);
        }
    }

    /**
     * Сформировать сообщение о публикации новости/события на нужном языке и отправить в нужный топик
     * @param $post
     * @param $lang
     */
    private function sendPublishNotification(Post $post, $lang)
    {
        $isEvent = $post instanceof Event;

        $messageData = [
            'id'         => (string)$post->getId(),
            'creator_id' => (string)$post->getAuthor()->getId(),
            'title'      => (string)$post->getTitleByLocale($lang),
            'type'       => 'news', //не знаю, зачем это передаётся
        ];

        if ($post instanceof Event) {
            $messageData['lat'] = $post->getLatitude();
            $messageData['lng'] = $post->getLongitude();
        }

        $topics = [
            'ru' => $isEvent ? $this->TOPIC_EVENTS_RU : $this->TOPIC_NEWS_RU,
            'en' => $isEvent ? $this->TOPIC_EVENTS_EN : $this->TOPIC_NEWS_EN,
            'es' => $isEvent ? $this->TOPIC_EVENTS_ES : $this->TOPIC_NEWS_ES,
        ];

        $titles = [
            'ru' => 'ЗабастКом',
            'en' => 'ZabastCom',
            'es' => 'ZabastCom',
        ];

        $bodies = [
            'ru' => $isEvent
                ? $post->getTitleRu() ? $post->getTitleRu() : 'В приложении опубликовано событие'
                : $post->getTitleRu() ? $post->getTitleRu() : 'В приложении опубликована новость',
            'en' => $isEvent
                ? $post->getTitleEn() ? $post->getTitleEn() : 'Event was published'
                : $post->getTitleEn() ? $post->getTitleEn() : 'News was published',
            'es' => $isEvent
                ? $post->getTitleEs() ? $post->getTitleEs() : 'El evento ha sido publicado'
                : $post->getTitleEs() ? $post->getTitleEs() : 'La noticia ha sido publicada',
        ];

        $messageToTopic = CloudMessage::withTarget(Messaging\MessageTarget::TOPIC, $topics[$lang])
            ->withNotification(['title' => $titles[$lang], 'body' => $bodies[$lang]])
            ->withData($messageData);

        $this->send($messageToTopic);
    }

    /**
     * Отправить пуш автору отклоненной новости
     * @param News $news
     */
    public function newsDeclined(News $news)
    {
        if (!$news->getAuthor()->getFcm()) return;

        $messageToAuthor = CloudMessage::withTarget(Messaging\MessageTarget::TOKEN, $news->getAuthor()->getFcm())
            ->withNotification(Notification::create('ЗабастКом', 'Ваша новость не прошла модерацию и была удалена'))
            ->withData([
                'id'         => (string)$news->getId(),
                'message'    => 'Ваша новость не прошла модерацию и была удалена',
                'creator_id' => (string)$news->getAuthor()->getId(),
                'type'       => 'moderated', //не знаю, зачем это передаётся
            ]);

        $this->send($messageToAuthor);
    }

    /**
     * Отправить пуш автору отклоненного события
     * @param Event $event
     */
    public function eventDeclined(Event $event)
    {
        if (!$event->getAuthor()->getFcm()) return;

        $messageToAuthor = CloudMessage::withTarget(Messaging\MessageTarget::TOKEN, $event->getAuthor()->getFcm())
            ->withNotification(Notification::create('ЗабастКом', 'Ваше событие не прошло модерацию и было удалено'))
            ->withData([
                'id'         => (string)$event->getId(),
                'message'    => 'Ваше событие не прошло модерацию и было удалено',
                'creator_id' => (string)$event->getAuthor()->getId(),
                'type'       => 'moderated', //не знаю, зачем это передаётся
            ]);

        $this->send($messageToAuthor);
    }

    /**
     * Отправить пуш-уведомление. В случае ошибки написать в лог
     * @param $message
     */
    private function send($message)
    {
        try {
            $this->messaging->send($message);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}