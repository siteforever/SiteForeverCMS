<html>
<body>
<h1>{'letter.title'|trans:[]:'guestbook'}</h1>
<ul>
    <li>{'Name'|trans:[]:'guestbook'}: {$obj->name}</li>
    <li>{'Email'|trans:[]:'guestbook'}: {$obj->email}</li>
    <li>{'Date'|trans:[]:'guestbook'}: {$obj->date|date_format}</li>
    <li>{'Ip'|trans:[]:'guestbook'}: {$obj->ip}</li>
</ul>
{$obj->message|escape|nl2br}
</body>
</html>
