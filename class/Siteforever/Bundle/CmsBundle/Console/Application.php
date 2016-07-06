<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Siteforever\Bundle\CmsBundle\Console;

use Sfcms\Module;
use Symfony\Bundle\FrameworkBundle\Console\Application as SymfonyApplication;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class Application extends SymfonyApplication
{
    protected function registerCommands()
    {
        $container = $this->getKernel()->getContainer();

        foreach ($this->getKernel()->getBundles() as $bundle) {
            if ($bundle instanceof Bundle) {
                $bundle->registerCommands($this);
            }
        }

        /** @var Module $module */
        foreach ($this->getKernel()->getModules() as $module) {
            $module->registerCommands($this);
        }

        if ($container->hasParameter('console.command.ids')) {
            foreach ($container->getParameter('console.command.ids') as $id) {
                $this->add($container->get($id));
            }
        }
    }
}
