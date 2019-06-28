<?php


namespace App\Services;


use App\Entities\Event;
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

    protected $TOPIC_NEWS = 'news1'; //todo это в тестовых целях. Потом сменить на 'news'
    protected $TOPIC_EVENTS = 'events';

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
     * Второй пуш шлём автору
     * @param News $news
     */
    public function newsPublished(News $news)
    {
        $messageToTopic = CloudMessage::withTarget(Messaging\MessageTarget::TOPIC, $this->TOPIC_NEWS)
            ->withNotification(
                Notification::create(
                    'ЗабастКом',
                    $news->getTitleRu() ? $news->getTitleRu() : 'В приложении опубликована новость'
                )
            )
            ->withData([
                'id'         => (string)$news->getId(),
                'title_ru'   => (string)$news->getTitleRu(),
                'title_en'   => (string)$news->getTitleEn(),
                'title_es'   => (string)$news->getTitleEs(),
                'creator_id' => (string)$news->getAuthor()->getId(),
                'type'       => 'news', //не знаю, зачем это передаётся
            ]);

        $this->send($messageToTopic);

        if ($news->getAuthor()->getFcm()) {
            $messageToAuthor = CloudMessage::withTarget(Messaging\MessageTarget::TOKEN, $news->getAuthor()->getFcm())
                ->withNotification(Notification::create('ЗабастКом', 'Ваша новость прошла модерацию'))
                ->withData([
                    'id'         => (string)$news->getId(),
                    'title_ru'   => (string)$news->getTitleRu(),
                    'title_en'   => (string)$news->getTitleEn(),
                    'title_es'   => (string)$news->getTitleEs(),
                    'creator_id' => (string)$news->getAuthor()->getId(),
                    'type'       => 'moderated', //не знаю, зачем это передаётся
                ]);

            $this->send($messageToAuthor);
        }
    }

    /**
     * Отправить оповещение всем пользователям о том, что опубликовано свежее событие
     * Второй пуш шлём автору
     * @param Event $event
     */
    public function eventPublished(Event $event)
    {
        $messageToTopic = CloudMessage::withTarget(Messaging\MessageTarget::TOPIC, $this->TOPIC_EVENTS)
            ->withNotification(
                Notification::create(
                    'ЗабастКом',
                    $event->getTitleRu() ? $event->getTitleRu() : 'В приложении опубликовано событие'
                )
            )
            ->withData([
                'id'         => (string)$event->getId(),
                'title_ru'   => (string)$event->getTitleRu(),
                'title_en'   => (string)$event->getTitleEn(),
                'title_es'   => (string)$event->getTitleEs(),
                'lat'        => (string)$event->getLatitude(),
                'lng'        => (string)$event->getLongitude(),
                'creator_id' => (string)$event->getAuthor()->getId(),
                'type'       => 'news', //не знаю, зачем это передаётся
            ]);

        $this->send($messageToTopic);

        if ($event->getAuthor()->getFcm()) {
            $messageToAuthor = CloudMessage::withTarget(Messaging\MessageTarget::TOKEN, $event->getAuthor()->getFcm())
                ->withNotification(Notification::create('ЗабастКом', 'Ваше событие прошло модерацию'))
                ->withData([
                    'id'         => (string)$event->getId(),
                    'title_ru'   => (string)$event->getTitleRu(),
                    'title_en'   => (string)$event->getTitleEn(),
                    'title_es'   => (string)$event->getTitleEs(),
                    'lat'        => (string)$event->getLatitude(),
                    'lng'        => (string)$event->getLongitude(),
                    'creator_id' => (string)$event->getAuthor()->getId(),
                    'type'       => 'moderated', //не знаю, зачем это передаётся
                ]);

            $this->send($messageToAuthor);
        }
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