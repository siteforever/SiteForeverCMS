<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\News\Listener;


use Sfcms\Kernel\KernelEvent;

class RssListener
{
    public function onKernelResponse(KernelEvent $event)
    {
        $content = $event->getResponse()->getContent();
        $content = str_replace(
            '</head>',
            sprintf('<link title="" type="application/rss+xml" rel="alternate" href="http://%s/rss">'.PHP_EOL.'</head>', $_SERVER['HTTP_HOST']),
            $content
        );
        $event->getResponse()->setContent($content);
    }
}
