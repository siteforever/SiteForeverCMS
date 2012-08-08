<p><a {href url="admin/news"}>{t}Category list{/t}</a>
{if !is_null($cat)} &gt; <a {href controller="news" action="list" id=$cat.id}>{$cat.name}</a>
<a {href controller="news" action="catedit" id=$cat.id}>{icon name="pencil"}</a>
{/if}
 &gt; {t}News edit{/t}</p>

{$form->html()}
