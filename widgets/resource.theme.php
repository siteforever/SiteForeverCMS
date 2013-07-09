<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Файл:     resource.theme.php
 * Тип:      resource
 * Имя:      theme
 * Назначение:  Получает шаблон из директории шаблонов темы
 * -------------------------------------------------------------
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

class Smarty_Resource_Theme extends Smarty_Resource_Custom
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
        $theme = App::getInstance()->getConfig()->get('template.theme');
        $path = 'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'templates';

        if ( file_exists( $path.DIRECTORY_SEPARATOR.$name ) ) {
            $source = file_get_contents( $path.DIRECTORY_SEPARATOR.$name );
            $mtime = filemtime ( $path.DIRECTORY_SEPARATOR.$name );
        } else {
            $source = null;
            $mtime = null;
        }
    }
}
