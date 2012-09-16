<?php
/**
 * Рисует карту сайта по средствам модели страницы
 */
class Controller_Sitemap extends Sfcms_Controller
{
    public function indexAction()
    {
        $this->request->setTitle('Карта сайта');
        $this->request->setTemplate('inner');

        $bc = $this->tpl->getBreadcrumbs();
        $bc->addPiece('index', 'Главная');
        $bc->addPiece(null, $this->request->getTitle());

        $tree   = $this->getModel('Page')->getMenu(1, 5);

        return '<div class="sitemap">' . $tree . '</div>';
    }
}
