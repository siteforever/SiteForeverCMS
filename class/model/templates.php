<?php
/**
 * Модель шаблонов
 */
class model_Templates extends Model
{
    /**
     * Форма редактирования
     * @var form_Form
     */
    private $form;

    protected $table;

    function createTables()
    {
        $this->table = DBPREFIX.'templates';

        if ( ! $this->isExistTable($this->table) ) {
            $this->db->query("
                CREATE TABLE `{$this->table}` (
                  `name` varchar(100) NOT NULL,
                  `description` varchar(250) default NULL,
                  `template` text,
                  `update` int(11) default NULL,
                  PRIMARY KEY  (`name`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8
            ");
        }
    }

    /**
     * Искать шаблон по $id
     * @param int $id
     */
    function find( $id )
    {
        if ( !isset( $this->data[ $id ] ) )
        {
            $this->data[ $id ] = $this->db->fetch("SELECT * FROM ".DBTEMPLATES." WHERE id = '{$id}' LIMIT 1");
        }
        return $this->data[ $id ];
    }

    /**
     * Искать шаблон по названию
     * @param string $name
     */
    function findByName( $name )
    {
        $data = $this->db->fetch("SELECT * FROM ".DBTEMPLATES." WHERE name = '{$name}' LIMIT 1");
        if ($data) {
            $this->data[ $data['id'] ]  = $data;
        }

        return $data;
    }

    
    /**
     * Поиск всех разделов сайта по условию
     * @param string $cond Условие
     * @return model_Templates
     */
    function findAll()
    {
        $alldata = $this->db->fetchAll("SELECT * FROM ".DBTEMPLATES." ORDER BY name");
        return $alldata;
    }
    
    
    /**
     * Обновить или добавить массив в базу
     * @return bool
     */
    function update()
    {
        $this->data['update'] = time(); // чтобы контролировать время устаревания шаблона
        $ret = $this->db->insertUpdate( DBTEMPLATES, $this->data );
        return $ret;
    }
    
    
    /**
     * Вернет объект формы
     * @return form_Form
     */
    function getForm()
    {
        if ( !isset($this->form) ) 
        {
            $this->form = new form_Form(array(
                'name'      => 'temlates',
                'class'     => 'standart',
                'fields'    => array(
                    'id'        => array('type'=>'int', 'hidden'),
                    'name'      => array('type'=>'text','label'=>'Наименование', 'required'),
                    'description'=> array('type'=>'text','label'=>'Описание', 'value'=>'', 'required'),
                    'template'  => array('type'=>'textarea','label'=>'Шаблон', 'class'=>'plain', 'required'),
                    'update'    => array('type'=>'date','label'=>'Дата обновления', 'value'=>time(), 'hidden'),

                    'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
                ),
            ));
        }
        return $this->form;
    }    
}