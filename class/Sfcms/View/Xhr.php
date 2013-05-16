<?php
/**
 * Отображение для XMLHttpRequest
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View;

use Sfcms\View\ViewAbstract;
use Exception;
use Sfcms\Request;
use Sfcms\Kernel\KernelEvent;

class Xhr extends ViewAbstract
{
    /**
     * @param KernelEvent $event
     * @throws Exception
     * @return string
     */
    public function view(KernelEvent $event)
    {
        $response = $event->getResponse();
        $request  = $event->getRequest();
        $content = $event->getResult() && $event->getResult() !== $event->getResponse()
            ? $event->getResult() : $response->getContent();
//        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
//        $response->headers->set('Cache-Control', 'post-check=0, pre-check=0', false);
//        $response->headers->set('Pragma', 'no-cache');

        $response->setCharset('utf-8');

        $types = $request->getAcceptableContentTypes();
        switch ($types[0]) {
            case 'application/json':
                $response->headers->set('Content-type', 'application/json');
                if (is_object($content) || is_array($content)) {
                    $response->setContent(json_encode($content));
                }
                break;

            case 'text/xml':
                $response->headers->set('Content-type', 'text/xml');
                break;

            default:
                if (count( $request->getFeedback())) {
                    $result = '<div class="feedback">' . $request->getFeedbackString() . '</div>';
                    $response->setContent($result . $response->getContent());
                }
        }
        return $event;
    }
}
