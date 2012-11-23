<?php
/**
 * Model Generator
 * @author: keltanas <keltanas@gmail.com>
 */
class Controller_Generator extends Sfcms_Controller
{
    /**
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array(
                'index','admin',
            ),
        );
    }

    public function init()
    {
        $this->request->setTitle(t('Generator'));
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->app()->addScript('/misc/admin/generator.js');

        $table_list = $this->getDB()->fetchAll("SHOW TABLES", false, PDO::FETCH_COLUMN);

        $this->tpl->assign('tables', $table_list);
    }


    /**
     * Генерирует модели по имени таблицы
     */
    public function generateAction()
    {
        $table    = $this->request->get( 'table' );
        $filename = str_replace( '_', '', strtolower( $table ) ) . '.php';
        $name     = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $table ) ) );

        $this->tpl->assign('name', $name );
        $this->tpl->assign('table', $table );

        $fields = $this->getDB()->fetchAll( "SHOW FIELDS FROM {$table}" );
        foreach( $fields as &$field ) {
            $field[ 'name' ]          = $field[ 'Field' ];
            preg_match( '/(\w+)/', $field[ 'Type' ], $m );
            $field[ 'type' ] = ucfirst( strtolower( $m[ 0 ] ) );
            switch ( $m[0] ) {
                case 'int':
                case 'longint':
                case 'tinyint':
                    $field[ 'vartype' ] = 'int';
                    break;
                case 'decimal':
                case 'float':
                    $field[ 'vartype' ] = 'float';
                    break;
                default:
                    $field[ 'vartype' ] = 'string';
            }
            preg_match( '/([\d\.]+)/', $field[ 'Type' ], $m );
            $field[ 'length' ]        = isset( $m[ 0 ] ) ? $m[ 0 ] : "11";
            $field[ 'notnull' ]       = $field[ 'Null' ] == 'YES' ? 'true' : 'false';
            $field[ 'default' ]       = $field[ 'Default' ] ? $field[ 'Default' ] : 'null';
            $field[ 'autoincrement' ] = $field[ 'Extra' ] == 'auto_increment' ? 'true' : 'false';
        }

        $this->tpl->assign('fields', $fields);

        $omodel  = $this->tpl->fetch('system:generator.tpl.model');
        $oobject = $this->tpl->fetch('system:generator.tpl.object');
        $otable  = $this->tpl->fetch('system:generator.tpl.table');

        $path   = ROOT.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR;
        $fmodel  = $path.'model'.DIRECTORY_SEPARATOR.$filename;
        $fobject  = $path."data".DIRECTORY_SEPARATOR."object".DIRECTORY_SEPARATOR.$filename;
        $ftable  = $path."data".DIRECTORY_SEPARATOR."table".DIRECTORY_SEPARATOR.$filename;

        if ( ! file_exists( $fmodel ) ) {
            file_put_contents( $fmodel, $omodel );
        }
        if ( ! file_exists( $fobject ) ) {
            file_put_contents( $fobject, $oobject );
        }
        file_put_contents( $ftable, $otable );

        return "Model: {$fmodel}\n"
            . "Object: {$fobject}\n"
            . "Table: {$ftable}\n";
    }
}
