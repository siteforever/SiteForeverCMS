<?php

class module_catalog_controller extends Controller
{
    function indexAction()
    {
        $this->request->setContent('Привет!');
    }
}
