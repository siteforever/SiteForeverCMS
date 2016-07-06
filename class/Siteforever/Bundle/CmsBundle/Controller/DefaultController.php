<?php

namespace Siteforever\Bundle\CmsBundle\Controller;

use Siteforever\Sfcms\Controller\SfcmsController;
use Sfcms\Kernel\KernelEvent;
use Sfcms\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends SfcmsController
{
    public function indexAction(Request $request)
    {
        $this->getAuth()->setRequest($request);
        $acceptableContentTypes = $request->getAcceptableContentTypes();
        $format = null;
        if ($acceptableContentTypes) {
            $format = $request->getFormat($acceptableContentTypes[0]);
        }
        $request->setRequestFormat($format);
        $request->setDefaultLocale($this->getContainer()->getParameter('locale'));

        $this->getTpl()->assign([
            'sitename' => $this->container->getParameter('sitename'),
            'debug' => $this->container->getParameter('kernel.debug'),
        ]);

        /** @var Response $response */
        $response = null;
        $result = $this->getResolver()->dispatch($request);

        if (null === $response && is_string($result)) {
            $response = new Response($result);
        } elseif ($result instanceof Response) {
            $response = $result;
        } elseif (!$response) {
            $response = new Response();
        }

        $event = new KernelEvent($response, $request, $result);
        $this->getEventDispatcher()->dispatch(KernelEvent::KERNEL_RESPONSE, $event);

        return $response;
    }
}
