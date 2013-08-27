<?php
/**
 * Testing module form
 */

namespace Module\Test\Controller;

use Module\Test\Form\TestFileForm;
use Sfcms\Controller;
use Sfcms\Request;
use Symfony\Component\HttpFoundation\Response;

class FormController extends Controller
{
    public function fileAction()
    {
        $form = new TestFileForm();
        var_dump($_POST, $_FILES, $this->request->request->all(), $this->request->files->all());
        return new Response($form->html());
    }
}
