<?php
/**
 * Контроллер страниц
 * @author keltanas aka Nikolay Ermin
 */

class controller_Page extends Controller
{
    function indexAction()
    {
        // создаем замыкание страниц
        while ( $this->page['link'] != 0 )
        {
            $page = App::$structure->find( $this->page['link'] );
            //$this->page['title']    = $page['title'];
            $this->page['content']  = $page['content'];
            $this->page['link']     = $page['link'];
        }
        $this->request->set('tpldata.page', $this->page);
    }

    function errorAction()
    {
        $this->request->set('template', 'index');
        $this->request->setTitle('Ошибка 404. Страница не найдена');
        $this->request->setContent('Ошибка 404.<br />Страница не найдена.');
    }
}
