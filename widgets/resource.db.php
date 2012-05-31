<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Файл:     resource.db.php
* Тип:      resource
* Имя:      db
* Назначение:  Получает шаблон из базы данных
* -------------------------------------------------------------
*/

class Smarty_Resource_Db extends Smarty_Resource_Custom
{
    /**
     * fetch template and its modification time from data source
     *
     * @param string  $name    template name
     * @param string  &$source template source
     * @param integer &$mtime  template modification timestamp (epoch)
     */
    protected function fetch( $name, &$source, &$mtime )
    {
        // выполняем обращение к базе данных для получения шаблона
        // и занесения полученного результата в в $tpl_source
        if ( $tpl = App::$templates->findByName( $name )  ) {
            $source = $tpl['template'];
            $mtime  = $tpl['update'];
        } else {
            $source = null;
            $mtime  = null;
        }
    }
}
