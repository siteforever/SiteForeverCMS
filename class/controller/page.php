<?php
/**
 * Контроллер страниц
 * @author keltanas aka Nikolay Ermin
 */

class controller_Page extends Controller
{
    function indexAction()
    {
        if ( ! $this->user->hasPermission( $this->page['protected'] ) )
        {
            $this->request->setContent(t('Access denied'));
            return;
        }
        
        // создаем замыкание страниц
        while ( $this->page['link'] != 0 )
        {
            $page = $this->getModel('Structure')->find( $this->page['link'] );

            if ( ! $this->user->hasPermission( $page['protected'] ) ) {
                $this->request->setContent(t('Access denied'));
                return;
            }
            $this->page['content']  = $page['content'];
            $this->page['link']     = $page['link'];
        }
    }

    function errorAction()
    {
        $this->request->set('template', 'index');
        $this->request->setTitle('Ошибка 404. Страница не найдена');
        $this->request->setContent('Ошибка 404.<br />Страница не найдена.');
    }
}
