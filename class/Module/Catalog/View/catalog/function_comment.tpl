{**
 * Вывод списка комментариев из виджета
 *}

<h3 id="product_comments">{t cat="catalog"}Comments{/t}</h3>

{flash request=$request}

{$form->html()}

{foreach $comments as $comment}
    {if 0 == $comment->hidden}
    <div class="well">
        <ul>
            <li>{t cat="catalog"}You name{/t}: <strong>{$comment->name}</strong></li>
            <li>{t cat="catalog"}Subject{/t}: <strong>{$comment->subject}</strong></li>
            <li>{t cat="catalog"}CreatedAt{/t}: <strong>{$comment->createdAt->format('d.m.Y H:i')}</strong></li>
        </ul>
        {$comment->content}
    </div>
    {/if}
{/foreach}
