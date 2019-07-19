<h2>thebestcom</h2>
Варианты локалей (locale): all, ru, en, es.
<ul>
    <li>При передаче [all] выводятся все варианты перевода сущностей и справочников. [title_ru, title_en, title_es]</li>
    <li>При передаче [ru/en/es] выводится лишь одно поле [title], содержимое которого соответствует локали.</li>
    <li>При создании/обновлении сущностей локаль тоже влияет: если передана локаль [ru/en/es], то можно передавать [title], которое
        сохранится в базе в нужном поле.</li>
    <li>С любой локалью можно при сохранении/обновлении сущностей передавать поля с суффиксом локали [title_ru, title_en, title_es],
        но если передать локаль [ru] и [title] без суффикса, то он будет иметь приоритет над [title_ru]</li>
</ul>

Рекомендую всегда прикреплять к запросам заголовок Accept: application/json. Иначе можно получить html в ответе (при ошибках сервера)

События и новости могут быть опубликованными или нет
<ul>
    <li>Только модераторы могут смотреть неопубликованные записи</li>
    <li>Только модераторы могут публиковать или снимать с публикации записи</li>
</ul>
Методы на обновление конфликтов, событий и новостей работают как PATCH
это значит, что значения полей, которые не переданы в запросе на обновление, не изменятся
Например, можно передать только {"published":true} в запросе на обновление новости. Новость опубликуется, поля не изменятся

<u>Ролевая система:</u> <br>
У каждого пользователя есть массив ролей. У обычных пользователей этот массив пустой. У администраторов в этом массиве есть 'ADMIN',
 у модераторов - 'MODERATOR'.
<br>
<u>Иерархия конфликтов:</u> <br>
Конфликты могут наследоваться через общее событие. Для связывания конфликта с родительским конфликтом необходимо
в поле parent_event_id дочернего конфликта сохранить id события (event) родительского конфликта, в котором происходит ветвление.
После этого событие принадлежит обоим конфликтам, то есть при запросе
/api/all/event с фильтром conflict_ids в ответ будут попадать события, которые принадлежать конфликтам из conflict_ids напрямую,
либо через поле parent_event_id.
<br>
Запрос /api/all/conflict с фильтром ancestors_of выведет те конфликты, которые являются родителями (не только прямыми) переданного конфликта.
<br>
<u>Пуш-уведомления:</u> <br>
Используется FCM Cloud Messaging. На период тестирования будут использоваться топики (а-ля комнаты, группы...): admin, news_ru [en/es], event_ru [en/es].<br>
<br>
Уведомления содержат заголовок и тело, а ещё содержат объект с данными. По составу данных в объекте принимаю пожелания, <br>
но после согласования с Виталием (потому как я на правах бэкендера не разбираюсь в клиентских приложениях)
<ul>
    <li>
        Если создать событие/новость без публикации (это касается обычных пользователей, когда они предлагают новость/событие),
        то в топик 'admin' придёт уведомление с заголовком "ЗабастКом" и телом "На модерации новость от имя_автора".
        К уведомлению прикреплен объект с полями: id, creator_name, creator_id, type = 'admin'
    </li>
    <li>
        При публикации события/новости (это касается модераторов), а также при обновлении новости/события, если добавляется перевод,<br>
        в топики 'news_ru'/'event_ru' [en/es] придёт уведомление с заголовком "ЗабастКом" и телом, содержащим заголовок новости/события на нужном языке.<br>
        К уведомлению прикреплен объект с полями: id, title, creator_id, type = 'news' (независимо от сущности всегда 'news').<br>
        У событий в объекте будут два дополнительных поля: lat, lng <br><br>
        <u>Пример1:</u> существовала неопубликованная новость с title и content на двух языках (ru, es). Модератор публикует её - посылаются пуши <br>
        в топики news_ru, news_es. Cостав уведомлений сформирован с учётом локали (title новости на нужном языке, заголовок и тело пуша переведены)<br>
        <u>Пример2:</u> новость из первого примера нуждается в переводе на английский. Модератор обновляет её, добавляя title_en и title_es.
        Посылается пуш в топик news_en.
    </li>
    <li>
        При публикации события/новости посылается ещё один пуш.<br>
        Но уже не в топик, а персонально автору новости/события, если у него указан fcm-токен.<br>
        Придёт уведомление с заголовком "ЗабастКом" и телом 'Ваша новость прошла модерацию'/'Ваше событие прошло модерацию'.<br>
        К уведомлению прикреплен объект с полями id, message_ru/en/es, creator_id, type = 'moderated'<br>
        В предыдущем пункте этот пуш будет отсылаться в первом примере, но не во втором
    </li>
    <li>
        При удалении модераторами неопубликованных новостей/событий автору удаленной записи (при наличии у него fcm-токена)
        будет приходить уведомление с заголовком "ЗабастКом", телом 'Ваша новость не прошла модерацию и была удалена'.
        В объекте придут поля: id, message_ru/en/es (дублирует тело пуша, указанное в предыдущем предложении), creator_id, type ('moderated')
    </li>
</ul>
