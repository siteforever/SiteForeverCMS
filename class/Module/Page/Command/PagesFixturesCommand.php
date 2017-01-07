<?php
/**
 * This file is part of the @package@.
 *
 * @author : Nikolay Ermin <nikolay.ermin@sperasoft.com>
 * @version: @version@
 */

namespace Module\Page\Command;

use Module\Page\Model\PageModel;
use Module\Page\Object\Page;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PagesFixturesCommand extends ContainerAwareCommand
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

        /** @var Page $mainPage */
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
            'author' => 0,
            'nofollow' => 0,
            'hidden' => 0,
            'protected' => 0,
            'system' => 0,
            'deleted' => 0,
        ]);
        $pageModel->save($mainPage);
        $output->writeln(sprintf('Created page <info>%s</info>', $mainPage->title));

        $page = $pageModel->createObject([
            'name' => 'О компании',
            'parent' => $mainPage->getId(),
            'template' => 'inner',
            'alias' => 'about',
            'date' => time(),
            'controller' => 'page',
            'action' => 'index',
            'content' => '<p>Информационная страница</p>',
            'author' => 0,
            'nofollow' => 0,
            'hidden' => 0,
            'protected' => 0,
            'system' => 0,
            'deleted' => 0,
        ]);
        $pageModel->save($page);
        $output->writeln(sprintf('Created page <info>%s</info>', $page->title));

        $page = $pageModel->createObject([
            'name' => 'Контакты',
            'parent' => $mainPage->getId(),
            'template' => 'inner',
            'alias' => 'contacts',
            'date' => time(),
            'controller' => 'page',
            'action' => 'index',
            'content' => '<p>Информационная страница</p>',
            'author' => 0,
            'nofollow' => 0,
            'hidden' => 0,
            'protected' => 0,
            'system' => 0,
            'deleted' => 0,
        ]);
        $pageModel->save($page);
        $output->writeln(sprintf('Created page <info>%s</info>', $page->title));
    }
}