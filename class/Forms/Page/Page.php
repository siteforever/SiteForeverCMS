<?php

use Sfcms\Model;

/**
 * Форма структуры сайта
 * @author keltanas
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */
class Forms_Page_Page extends \Sfcms\Form\Form
{
    public function __construct()
    {
        parent::__construct(
            array(
                'name'      => 'structure',
                'action'    => App::getInstance()->getRouter()->createServiceLink( 'page', 'save' ),
                'fields'    => array(
                    'id'         => array(
                        'type' => 'hidden',
                        'label'=> 'ID',
                        'value'=> null,
                    ),
                    'parent' => array(
                        'type' => 'select',
                        'label' => t('page', 'Parent'),
                        'variants' => Model::getModel('Page')->getSelectOptions(),
                        'value' => '0',
                    ),
                    'name'       => array(
                        'type' => 'text',
                        'label'=> 'Наименование',
                        'required'
                    ),
                    'template'   => array(
                        'type' => 'select',
                        'label'=> 'Шаблон',
                        'value'=> 'inner',
                        'variants' => $this->getTemplatesList(),
                        'required'
                    ),
                    //'uri'       => array('type'=>'text','label'=>'Псевдоним', 'value='=>'', 'hidden'),
                    'alias'      => array(
                        'type' => 'text',
                        'label'=> 'Адрес',
                        'required'
                    ),

                    'date'       => array(
                        'type' => 'date',
                        'label'=> 'Дата создания',
                        'value'=> time(),
                        'hidden'
                    ),
                    'update'     => array(
                        'type' => 'date',
                        'label'=> 'Дата обновления',
                        'value'=> time(),
                        'hidden'
                    ),

                    'pos'        => array(
                        'type' => 'int',
                        'label'=> 'Порядок сортировки',
                        'value'=> '0',
                        'readonly',
                        'hidden',
                    ),

                    'controller' => array(
                        'type'      => 'select',
                        'label'     => 'Контроллер',
                        'variants'  => \Sfcms\Model::getModel('Page')->getAvaibleModules(),
                        'required',
                    ),
                    'link'       => array(
                        'type' => 'int',
                        'label'=> 'Ссылка на раздел',
                        'value'=> '0',
//                        'hidden',
                    ),
                    'action'     => array(
                        'type' => 'text',
                        'label'=> 'Действие',
                        'required', 'readonly', 'hidden'
                    ),

                    'sort'       => array(
                        'type' => 'text',
                        'label'=> 'Сортировка',
                        'required', 'hidden'
                    ),

                    'title'      => array(
                        'type' => 'text',
                        'label'=> 'Заголовок'
                    ),
                    'keywords'   => array(
                        'type' => 'text',
                        'label'=> 'Ключевые слова'
                    ),
                    'description'=> array(
                        'type' => 'text',
                        'label'=> 'Описание'
                    ),
                    'nofollow'     => array(
                        'type'      => 'radio',
                        'label'     => 'Параметр NoFollow',
                        'value'     => '0',
                        'variants'  => array( 'Нет', 'Да' ),
                    ),

                    'notice'     => array(
                        'type' => 'textarea',
                        'label'=> 'Вступление',
                        'value'=> '',
//                        'hidden'
                    ),
                    'content'    => array(
                        'type' => 'textarea',
                        'label'=> 'Текст',
                    ),

                    'thumb'      => array(
                        'type' => 'text',
                        'label'=> 'Иконка',
                        'class'=> 'image',
                    ),
                    'image'      => array(
                        'type' => 'text',
                        'label'=> 'Изображение',
                        'class'=> 'image',
                    ),


                    'author'     => array(
                        'type' => 'hidden',
                        'label'=> 'Автор',
                        'value'=> '1'
                    ),

                    'hidden'     => array(
                        'type'      => 'radio',
                        'label'     => 'Скрытое',
                        'value'     => '0',
                        'variants'  => array( 'Нет', 'Да' ),
                    ),
                    'protected'  => array(
                        'type'      => 'radio',
                        'label'     => 'Защита страницы',
                        'value'     => USER_GUEST,
                        'variants'  => array(),
                    ),
                    'system'     => array(
                        'type'      => 'radio',
                        'label'     => 'Системный',
                        'value'     => '0',
                        'variants'  => array( 'Нет', 'Да' ),
                    ),

                    'submit'     => array(
                        'type' => 'submit',
                        'value'=> 'Сохранить'
                    ),

                ),
            )
        );
    }

    /**
     * Список шаблонов для нужной темы
     * @return array
     */
    protected function getTemplatesList()
    {
        $templates = array('index'=>t('Main'), 'inner'=>t('Inner'));

        $theme = App::getInstance()->getConfig('template.theme');

        $themePath = ROOT . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $theme;
        $themeXMLFile = $themePath . DIRECTORY_SEPARATOR . 'theme.xml';

        $logger = App::getInstance()->getLogger();

        if ( file_exists( $themeXMLFile ) ) {
            $themeXML = new SimpleXMLElement( file_get_contents( $themeXMLFile ) );
            if ( isset( $themeXML->templates ) ) {
                $templates = array();
                /** @var $tpl SimpleXMLElement */
                foreach ( $themeXML->templates->template as $tpl ) {
                    $templates[ (string) $tpl['value'] ] = t( (string) $tpl );
                }
            }
        }

        return $templates;
    }
}
