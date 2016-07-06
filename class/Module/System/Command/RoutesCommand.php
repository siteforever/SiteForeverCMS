<?php
/**
 * Command Admicons
 * @generator SiteForeverGenerator
 */

namespace Module\System\Command;

use Module\Page\Model\PageModel;
use Sfcms\Model;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class RoutesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('system:routes')
            ->setDescription('Print all routes')
        ;
    }

    /**
     * @return PageModel
     */
    protected function getPageModel()
    {
        return \App::cms()->getDataManager()->getModel('Page');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(1);
        /** @var Router $router */
        /** @var Route $route */
        $router = \App::cms()->getContainer()->get('symfony_router');

        $this->getPageModel()->fillRoutes($router->getRouteCollection());

        $routes = $router->getRouteCollection()->all();
        $maxNameLength = array_reduce(array_keys($routes), function($total, $el){
                return $total = $total > strlen($el) ? $total : strlen($el);
            }, 4);

        $maxMethodLength = array_reduce($routes, function($total, Route $el){
                $len = strlen(join('|', $el->getMethods()));
                return $total = $total > $len ? $total : $len;
            }, 6);

        $maxControllerLength = array_reduce($routes, function($total, Route $el){
                $len = strlen($el->getDefault('_controller').':'.$el->getDefault('_action'));
                return $total = $total > $len ? $total : $len;
            }, 10);

        $output->writeln(sprintf(
                '<comment>% -'.$maxNameLength.'s</comment>   '
                .'<comment>% -'.$maxMethodLength.'s</comment>   '
                .'<comment>% -'.$maxControllerLength.'s</comment>   '
                .'<comment>%s</comment>',
                'Name', 'Method', 'Controller', 'Route'));

        ksort($routes);
        foreach ($routes as $i => $route) {
            $output->writeln(sprintf(
                '% -'.$maxNameLength.'s   '
                .'% -'.$maxMethodLength.'s   '
                .'% -'.$maxControllerLength.'s   '
                .'%s',
                $i,
                join('|', $route->getMethods() ?: array('ANY')),
                $route->getDefault('_controller').':'.$route->getDefault('_action'),
                $route->getPath()
            ));
        }

        $output->writeln('');
        $output->writeln(sprintf("<info>Total routes: %d</info>", count($routes)));
        $output->writeln(sprintf("<info>Execution time: %.3F sec</info>", microtime(1) - $start));
    }
}
