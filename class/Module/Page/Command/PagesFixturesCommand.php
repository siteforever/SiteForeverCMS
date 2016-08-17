<?php
/**
 * Created by PhpStorm.
 * User: keltanas
 * Date: 04.08.16
 * Time: 0:50
 */

namespace Module\Page\Command;


use Module\Page\Model\PageModel;
use Sfcms\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PagesFixturesCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('fixture:pages')
            ->setDescription('Load pages into database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PageModel $pageModel */
        $pageModel = $this->getContainer()->get('data.manager')->getModel('Page');

        $mainPage = $pageModel->createObject([
            'name' => 'Главная',
            'parent' => 0,
            'template' => 'index',
            'alias' => 'index',
            'date' => time(),
            'controller' => 'page',
            'action' => 'index',
            'title' => 'SiteForeverCMS',
            'content' => '<p>Информационная страница</p>',
        ]);

        $pageModel->save($mainPage);

        $page = $pageModel->createObject([
            'name' => 'О компании',
            'parent' => $mainPage->getId(),
            'template' => 'inner',
            'alias' => 'about',
            'date' => time(),
            'controller' => 'page',
            'action' => 'index',
            'content' => '<p>Информационная страница</p>',
        ]);

        $pageModel->save($page);

        $page = $pageModel->createObject([
            'name' => 'Контакты',
            'parent' => $mainPage->getId(),
            'template' => 'inner',
            'alias' => 'contacts',
            'date' => time(),
            'controller' => 'page',
            'action' => 'index',
            'content' => '<p>Информационная страница</p>',
        ]);

        $pageModel->save($page);


    }
}