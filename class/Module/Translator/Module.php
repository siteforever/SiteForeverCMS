<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Translator;

use Module\Translator\DependencyInjection\TranslatorExtension;
use Sfcms\Module as SfcmsModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends SfcmsModule
{
    public function build(ContainerBuilder $container)
    {
        $container->registerExtension(new TranslatorExtension());
    }
}
