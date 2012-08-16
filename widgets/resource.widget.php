<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Файл:     resource.widget.php
* Тип:      resource
* Имя:      widget
* Назначение:  Получает шаблон из директории шаблонов биджетов
* -------------------------------------------------------------
*/

class Smarty_Resource_Widget extends Smarty_Resource_Custom
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
        // выполняем обращение для получения шаблона
        // и занесения полученного результата в в $tpl_source
        $path = __DIR__.DIRECTORY_SEPARATOR.'tpl';

        if ( file_exists( $path.DIRECTORY_SEPARATOR.$name ) ) {
            $source = file_get_contents( $path.DIRECTORY_SEPARATOR.$name );
            $mtime = filemtime ( $path.DIRECTORY_SEPARATOR.$name );
        } else {
            $source = null;
            $mtime = null;
        }
    }
}
