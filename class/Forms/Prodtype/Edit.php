<?php
/**
 * Правка типа товара
 * @author: keltanas
 * @link  http://siteforever.ru
 */
namespace Forms\Prodtype;

use Sfcms\Form\Form;

class Edit extends Form
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
                    'label' => t('Name'),
                    'required',
                ),
            ),
        ));
    }
}
