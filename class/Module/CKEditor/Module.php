<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\CKEditor;

use Module\CKEditor\DependencyInjection\CKEditorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Module extends \Sfcms\Module
{
    public function loadExtensions(ContainerBuilder $container)
    {
        $container->registerExtension(new CKEditorExtension());
    }
}
