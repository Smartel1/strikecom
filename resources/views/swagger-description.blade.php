<h2>thebestcom</h2>
<h5>Варианты локалей (locale): all, ru, en, es.</h5>
<ul>
    <li>При передаче [all] выводятся все варианты перевода сущностей и справочников. [title_ru, title_en, title_es]</li>
    <li>При передаче [ru/en/es] выводится лишь одно поле [title], содержимое которого соответствует локали.</li>
    <li>При создании/обновлении сущностей локаль тоже влияет: если передана локаль [ru/en/es], то можно передавать [title], которое
        сохранится в базе в нужном поле.</li>
    <li>С любой локалью можно при сохранении/обновлении сущностей передавать поля с суффиксом локали [title_ru, title_en, title_es],
        но если передать локаль [ru] и [title] без суффикса, то он будет иметь приоритет над [title_ru]</li>
</ul>
<h5>События и новости могут быть опубликованными или нет</h5>
<ul>
    <li>Только модераторы могут смотреть неопубликованные записи</li>
    <li>Только модераторы могут публиковать или снимать с публикации записи</li>
</ul>
<h5>Методы на обновление конфликтов, событий и новостей работают как PATCH</h5>
<h2>это значит, что значения полей, которые не переданы в запросе на обновление, не изменятся</h2>
Например, можно передать только {"published":true} в запросе на обновление новости. Новость опубликуется, поля не изменятся
