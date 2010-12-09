<?php
/**
 * Created by PhpStorm.
 * User: keltanas
 * Date: 26.07.2010
 * Time: 16:44:06
 * To change this template use File | Settings | File Templates.
 */

class model_news extends Model
{
    private $cond = '';
    private $form = null;
    private $category_form = null;

    function createTables()
    {
        if ( ! $this->isExistTable( DBNEWS ) ) {
            $this->db->query("CREATE TABLE `".DBNEWS."` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `cat_id` int(11) NOT NULL DEFAULT '0',
              `author_id` int(11) NOT NULL DEFAULT '0',
              `name` varchar(250) NOT NULL DEFAULT 'noname',
              `notice` text,
              `text` text,
              `date` int(11) NOT NULL DEFAULT '0',
              `title` varchar(250) NOT NULL DEFAULT 'notitle',
              `keywords` varchar(250) NOT NULL,
              `description` varchar(250) NOT NULL,
              `hidden` tinyint(4) NOT NULL DEFAULT '0',
              `protected` tinyint(4) NOT NULL DEFAULT '0',
              `deleted` tinyint(4) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
        if ( ! $this->isExistTable( DBNEWSCATS ) ) {
            $this->db->query("CREATE TABLE `".DBNEWSCATS."` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(250) NOT NULL,
              `description` text,
              `show_content` tinyint(1) NOT NULL DEFAULT '1',
              `show_list` tinyint(1) NOT NULL DEFAULT '1',
              `type_list` tinyint(1) NOT NULL DEFAULT '1',
              `per_page` tinyint(4) NOT NULL DEFAULT '10',
              `hidden` tinyint(4) NOT NULL DEFAULT '0',
              `protected` tinyint(4) NOT NULL DEFAULT '0',
              `deleted` tinyint(4) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }

    function find($id)
    {
        $data = $this->db->fetch("SELECT * FROM ".DBNEWS." WHERE id = {$id} AND deleted = 0 LIMIT 1");
        if ( $data ) {
            return $data;
        }
    }

    /**
     * Установка условия
     * @param  $cond
     * @return void
     */
    function setCond( $cond )
    {
        $this->cond = $cond;
    }

    /**
     * Очистить условия
     * @return void
     */
    function clearCond()
    {
        $this->cond = '';
    }


    /**
     * Все новости по критерию
     * @param string $cond
     * @param string $limit
     * @return array
     */
    function findAll( $limit = '' )
    {
        $where = '';
        if ( $this->cond ) {
            $where = ' WHERE '.$this->cond;
        }

        if ( $limit ) {
            $limit = ' LIMIT '.$limit;
        }

        $data = $this->db->fetchAll(
            "SELECT * FROM ".DBNEWS." $where ORDER BY `date` DESC ".$limit
        );

        if ( $data ) {
            return $data;
        }
    }

    function findAllWithLinks($limit = '')
    {
        $where = '';
        if ( $this->cond ) {
            $where = ' WHERE '.$this->cond;
        }

        if ( $limit ) {
            $limit = ' LIMIT '.$limit;
        }

        $data = $this->db->fetchAll(
            "SELECT news.*, s.alias link
             FROM ".DBNEWS." news
            INNER JOIN ".DBSTRUCTURE." s ON news.cat_id = s.link AND s.alias != 'index' AND s.controller = 'news' ".
                $where.
            " ORDER BY news.date DESC ".$limit
        );
        if ( $data ) {
            return $data;
        }
    }

    /**
     * Количество строк
     * @return void
     */
    function count()
    {
        $where = '';
        if ( $this->cond ) {
            $where = ' WHERE '.$this->cond;
        }
        return $this->db->fetchOne(
            "SELECT COUNT(*) FROM ".DBNEWS.' news '.$where
        );
    }

    /**
     * Обновление информации
     * @return void
     */
    function update( $data = null )
    {
        if ( is_null($data) ) {
            $data = $this->data;
        }
        if ( !$data ) {
            throw new Exception("Ошибка обновления");
            return false;
        }
        return $this->db->insertUpdate(DBNEWS, $this->data);
    }

    /**
     * Обновление информации для категории
     * @return void
     */
    function updateCat( $data = null )
    {
        if ( is_null($data) && !is_null($this->data) ) {
            $data = $this->data;
        }
        if ( !$data ) {
            throw new Exception("Ошибка обновления");
            return false;
        }
        return $this->db->insertUpdate(DBNEWSCATS, $data);
    }

    /**
     * Категория новости
     * @param  $id
     * @return array
     */
    function findCat( $id )
    {
        $data = $this->db->fetch("SELECT * FROM ".DBNEWSCATS." WHERE id = {$id} AND deleted = 0 LIMIT 1");
        if ( $data ) {
            return $data;
        }
    }

    /**
     * Список категорий новостей
     * @return void
     */
    function findAllCats()
    {
        $data = $this->db->fetchAll(
            "SELECT cat.*, (SELECT COUNT(news.id) FROM ".DBNEWS." news WHERE news.cat_id = cat.id) news_count
            FROM ".DBNEWSCATS." cat
            WHERE cat.deleted = 0"
        );
        if ( $data ) {
            return $data;
        }
    }

    /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $user = $this->user->getData();

            $cats_data = $this->findAllCats();
            $cats   = array(0=>'Ничего не выбрано');
            foreach ( $cats_data as $_cd ) {
                $cats[$_cd['id']] = $_cd['name'];
            }

            $this->form = new form_Form(array(
                'name'      => 'news',
                'fields'    => array(
                        'id'        => array('type'=>'int', 'value'=>'0', 'hidden',),
                        'cat_id'    => array(
                                'type'      =>  'select',
                                'value'     =>  $this->request->get('cat'),
                                'variants'  =>  $cats,
                                'label'     =>  'Категория',
                                //'hidden',
                        ),
                        'author_id' => array('type'=>'text', 'value'=>$user['id'], 'label'=>'','hidden',),
                        'name'      => array('type'=>'text', 'value'=>'', 'label'=>'Название',),
                        'notice'    => array('type'=>'textarea', 'value'=>'', 'label'=>'Вступление',),
                        'text'      => array('type'=>'textarea', 'value'=>'', 'label'=>'Текст',),
                        'date'      => array('type'=>'date', 'label'=>'Дата',),
                        'title'     => array('type'=>'text', 'value'=>'', 'label'=>'Заголовок',),
                        'keywords'  => array('type'=>'text', 'value'=>'', 'label'=>'Ключевые слова',),
                        'description'=> array('type'=>'text', 'value'=>'','label'=>'Описание',),
                        'hidden'    => array(
                                'type'      => 'radio',
                                'label'     => 'Скрытое',
                                'value'     => '0',
                                'variants'  => array('Нет', 'Да'),
                        ),
                        'protected' => array(
                                'type'      => 'radio',
                                'label'     => 'Защита страницы',
                                'value'     => USER_GUEST,
                                'variants'  => App::$config->get('users.groups'),
                        ),

                        'deleted'   => array('type'=>'int', 'value'=>'0', 'hidden'),

                        'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
                ),
            ));
        }
        return $this->form;
    }

    /**
     * @return form_Form
     */
    function getCategoryForm()
    {
        if ( is_null( $this->category_form ) ) {
            $this->category_form = new form_Form(array(
                'name'      => 'news',
                'fields'    => array(
                        'id'        => array('type'=>'int', 'value'=>'0', 'hidden'),
                        'name'      => array('type'=>'text', 'label'=>'Наименование', 'required',),
                        'description'   => array('type'=>'text', 'label'=>'Описание',),
                        'show_content'  => array(
                                'type'      => 'radio',
                                'label'     => 'Отображать контент',
                                'value'     => '1',
                                'variants'  => array(1=>'Да',0=>'Нет',),    
                        ),
                        'show_list'     => array(
                                'type'      => 'radio',
                                'label'     => 'Отображать список',
                                'value'     => '1',
                                'variants'  => array(1=>'Да',0=>'Нет',),
                        ),
                        'type_list'     => array(
                                'type'      => 'select',
                                'label'     => 'Тип списка',
                                'value'     => '1',
                                'variants'  => array(
                                    1   => 'В виде ленты новостей',
                                    2   => 'В виде списка',
                                ),
                        ),
                        'per_page'     => array(
                                'type'      => 'select',
                                'label'     => 'Материалов на страницу',
                                'value'     => '1',
                                'variants'  => array(
                                     5   => '5',
                                    10   => '10',
                                    20   => '20',
                                    50   => '50',
                                ),
                        ),
                        'hidden'    => array(
                                'type'      => 'radio',
                                'label'     => 'Скрытое',
                                'value'     => '0',
                                'variants'  => array(1=>'Да',0=>'Нет',),
                        ),
                        'protected' => array(
                                'type'      => 'radio',
                                'label'     => 'Защита страницы',
                                'value'     => USER_GUEST,
                                'variants'  => App::$config->get('users.groups'),
                        ),

                        'deleted'   => array('type'=>'int', 'value'=>'0', 'hidden'),

                        'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
                ),
            ));
        }
        return $this->category_form;
    }
}
