<html>
<body>
<h1>{'letter.title'|trans:[]:'guestbook'}</h1>

<p>Вы оставили сообщение в гостевой на сайте {$sitemap}.</p>

<p>Мы в ближайшее время ответим Вам.</p>

<p>{$obj->message|escape|nl2br}</p>
</body>
</html>
