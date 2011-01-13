<?php
/**
 * Модель новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */


class model_news extends Model
{
    private $form = null;

    /**
     * @var model_NewsCategory
     */
    public $category;

    function Init()
    {
        $this->category = self::getModel( 'NewsCategory' );
    }

    function findAllWithLinks($crit = array())
    {
        $data_all   = $this->findAll( $crit );

        $list_id    = array();
        foreach ( $data_all as $d ) {
            $list_id[]  = $d['cat_id'];
        }

        $structure  = self::getModel('model_Structure');

        $s_data_all = $structure->findAll(array(
              'select'  => 'link, alias',
              'cond'    => "deleted = 0 AND alias != 'index' AND controller = 'news' AND link IN (".join(',', $list_id).")"
          ));

        foreach( $data_all as $i => $d ) {
            foreach ( $s_data_all as $s ) {
                if ( $d['cat_id'] == $s['link'] ) {
                    $data_all[$i]['alias'] = $s['alias'];
                    break;
                }
            }
        }

        return $data_all;
    }

    /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_news_Edit();
        }
        return $this->form;
    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_News';
    }
}
