<p><a {href url="admin/news"}>Категории материалов</a>
{if !is_null($cat)} &gt; <a {href url='admin/news' catid=$cat.id}>{$cat.name}</a> {/if}
 &gt; Правка материала</p>

{$form->html()}
