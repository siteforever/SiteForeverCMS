<p><strong>{$news.date|date_format:"%x"}</strong></p>
<h2>{$news.title|default:$news.name}</h2>
<div>
{$news.text}
</div>
