<?php
/**
 * Mysql Dumper Class
 * Created by PhpStorm.
 * User: keltanas
 * Date: 10.09.2010
 * Time: 13:18:37
 * To change this template use File | Settings | File Templates.
 */
 
class dumper {

    protected   $db;
    protected   $request;

    function __construct( Request $request )
    {
        $this->db   = App::$db;
        $this->request  = $request;
    }

    function &create()
    {
        $sql    = array();

        $tables = $this->db->fetchAll("SHOW TABLES");
        if ( $tables ) {
            foreach ( $tables as $table ) {
                $table  = array_pop( $table );

                $this->request->addFeedback(t('Successfull dumped table').' '.$table);
                $cr_tbl = $this->db->fetch("SHOW CREATE TABLE `{$table}`");

                $sql[]  = "DROP TABLE IF EXISTS `{$table}`;";
                $sql[]  = preg_replace("/[\n\r]/", '', $cr_tbl['Create Table'].';');

                $columns_config    = $this->db->fetchAll("SHOW COLUMNS FROM $table");
                $columns    = array();
                foreach ( $columns_config as $col ) {
                    $columns[]  = $col['Field'];
                }

                $ins    = array();

                // получение данных
                $total_rows = $this->db->count($table);
                if ( $total_rows > 0 ) {
                    //$ins[]  = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $columns).'`) VALUES ';
                    $counter    = 0;
                    while( $counter < $total_rows ) {
                        $rows   = $this->db->fetchAll('SELECT * FROM `'.$table.'` LIMIT '.$counter.', 1000');
                        $counter    += 1000;
                        foreach ( $rows as $row ) {
                            //printVar($row);
                            foreach( $row as $irow => $vrow ) {
                                $row[$irow] = mysql_real_escape_string( $vrow );
                            }
                            $ins[]  = '(\''.implode('\',\'', $row).'\')';
                        }
                    }
                }
                $sql[]  = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $columns).'`) VALUES '.implode(',', $ins).';';
            }
        }
        return $sql;
    }
}
