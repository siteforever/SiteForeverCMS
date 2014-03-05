<html><body>
{'Page'|trans}: <a href="{$request->getSchemeAndHttpHost()}{$request->getRequestUri()}#product_comments">Перейти</a><br>
{'Name'|trans}: {$object->name}<br>
{'Email'|trans}: {$object->email}<br>
{'Phone'|trans}: {$object->phone}<br>
{'Ip'|trans}: {$object->ip}<br>
{'Subject'|trans}: {$object->subject}<br>
{'Content'|trans}:<br>{$object->content|strip_tags|nl2br}<br>
</body></html>
