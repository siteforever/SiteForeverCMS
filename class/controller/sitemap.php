<?php
/**
 * Рисует карту сайта по средствам модели страницы
 */
class Controller_Sitemap extends Controller
{
    function indexAction()
    {
        $this->request->setTitle('Карта сайта');
        $this->request->setTemplate('inner');
        $bc = $this->tpl->getBreadcrumbs();
        $bc->addPiece('index', 'Главная');
        $bc->addPiece('sitemap', $this->request->getTitle());


        $tree   = $this->getModel('Page')->getMenu(0, 3);

        $this->request->setContent( '<div class="sitemap">' . $tree . '</div>' );
    }
}
