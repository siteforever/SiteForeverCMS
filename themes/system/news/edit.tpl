<p><a {href url="admin/news"}>Категории материалов</a>
{if !is_null($cat)} &gt; <a {href controller="news" action="newslist" catid=$cat.id}>{$cat.name}</a>
<a {href controller="news" action="catedit" id=$cat.id}>{icon name="pencil"}</a>
{/if}
 &gt; Правка материала</p>

{$form->html()}
