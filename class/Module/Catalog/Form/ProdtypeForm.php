<?php
/**
 * Правка типа товара
 * @author: keltanas
 * @link  http://siteforever.ru
 */
namespace Module\Catalog\Form;

use Sfcms\Form\Form;

class ProdtypeForm extends Form
{
    public function __construct()
    {
        return parent::__construct(array(
            'name'   => 'ProdTypeEdit',
            'action' => \Sfcms::html()->url('prodtype/save'),
            'fields' => array(
                'id' => array(
                    'type' => 'hidden',
                ),
                'name' => array(
                    'type' => 'text',
                    'label' => 'Name',
                    'required',
                ),
            ),
        ));
    }
}
