<?php
/**
 * Бэкапит БД
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class controller_dumper extends Controller
{
    private $backup_dir = 'backup';

    function init()
    {
        $this->request->setTitle( t('Dumper of database') );
        if ( ! file_exists($this->backup_dir) ) {
            mkdir($this->backup_dir,'0666');
        }
    }

    function indexAction()
    {
        if ( $this->request->get('dump') == 'create' ) {
            $dumper = new dumper( $this->request );
            $sql    = $dumper->create();
            $json_sql   = json_encode( $sql );
            //file_put_contents($this->backup_dir.DIRECTORY_SEPARATOR.date('Ynd_His').'.sql.json', $json_sql);
            file_put_contents($this->backup_dir.DIRECTORY_SEPARATOR.date('Ymd_His').'.sql', implode("\n",$sql));
            //file_put_contents($this->backup_dir.DIRECTORY_SEPARATOR.date('Ynd_His').'.sql.ser', serialize($sql));
            $this->request->addFeedback( t('Dump successfully created') );
            $this->request->setContent($this->tpl->fetch('dumper.create'));
            return;
        }

        if ( $this->request->get('dump') == 'restore' ) {

            $this->request->setTitle(t('Restore DB from dump'));
            $sql_files  = glob($this->backup_dir.DIRECTORY_SEPARATOR.'*.sql');
            sort( $sql_files );
            $this->tpl->files   = $sql_files;
            $this->request->setContent( $this->tpl->fetch('dumper.restore') );

            return;
        }

        $this->request->setContent($this->tpl->fetch('dumper.index'));
    }

}



    /*if ( $tables ) {
        foreach ( $tables as $table ) {
            $table  = array_pop( $table );

            $columns    = $db->fetchAll("SHOW COLUMNS FROM {$table}");
            $indexes    = $db->fetchAll("SHOW INDEX FROM {$table}");

            $keys   = array();
            $cols   = array();
            foreach( $columns as $col ) {
                $cols[]  = "`{$col['Field']}` {$col['Type']} ".
                        ($col['Null'] == 'NO' ? "NOT NULL" : "").
                        ($col['Default'] && $col['Default'] !== '' ? " DEFAULT '{$col['Default']}'" : ($col['Null'] == 'NO'?"":" DEFAULT NULL ")).
                        ($col['Extra'] == 'auto_increment' ? " AUTO_INCREMENT " : "");

            }
            foreach ( $indexes as $ind ) {
                $keys[ $ind['Key_name'] ][$ind['Seq_in_index']] = $ind['Column_name'];
            }

            foreach ( $keys as $key_name => $ind ) {
                if ( $key_name == 'PRIMARY' ) {
                    $cols[] = "PRIMARY KEY (`".implode('`,`', $ind)."`)";
                }
                else {
                    $cols[] = "KEY `{$key_name}` (`".implode('`,`', $ind)."`)";
                }
            }

            $sql[$table]    = "CREATE TABLE `{$table}` (\n\t".implode(",\n\t", $cols ).")";

        }
    }*/

