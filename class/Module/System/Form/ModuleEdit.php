<?php
/**
 * Правка модуля
 * @author: keltanas
 * @link  http://siteforever.ru
 */
namespace Module\System\Form;

use Form_Form;

class ModuleEdit extends Form_Form
{
    public function __construct()
    {
        parent::__construct( array(
            'name' => 'ModuleEdit',
            'action' => \Sfcms::html()->url('system/module/save'),
        ) );
    }

}
