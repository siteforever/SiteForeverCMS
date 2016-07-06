<?php
/**
 * Event for kernel
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Kernel;

use Symfony\Component\EventDispatcher\Event;
//use Symfony\Component\HttpFoundation\Request;
use Sfcms\Request;
use Symfony\Component\HttpFoundation\Response;

class KernelEvent extends Event
{
    const KERNEL_RESPONSE = 'kernel.sfcms.response';

    const KERNEL_RESPONSE_SENT = 'kernel.sfcms.terminate';

    /** @var Response */
    protected $response;
    /** @var Request */
    protected $request;
    /** @var mixed */
    protected $result;

    public function __construct(Response $response, Request $request, $result = null)
    {
        $this->response = $response;
        $this->request = $request;
        $this->result = $result;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
