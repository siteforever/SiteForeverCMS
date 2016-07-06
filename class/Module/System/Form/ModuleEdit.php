<?php
/**
 * Правка модуля
 * @author: keltanas
 * @link  http://siteforever.ru
 */
namespace Module\System\Form;

use Sfcms\Form\Form;

class ModuleEdit extends Form
{
    public function __construct()
    {
        parent::__construct( array(
            'name' => 'ModuleEdit',
            'action' => \App::cms()->get('router')->generate('system/module/save'),
        ) );
    }

}
