<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Файл:     resource.theme.php
* Тип:      resource
* Имя:      theme
* Назначение:  Получает шаблон из директории шаблонов темы
* -------------------------------------------------------------
*/
function smarty_resource_theme_source($tpl_name, &$tpl_source, &$smarty)
{
    // выполняем обращение для получения шаблона
    // и занесения полученного результата в в $tpl_source
    $theme = App::$config->get('template.theme');
    $path = 'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'templates';
    
    if ( file_exists( $path.DIRECTORY_SEPARATOR.$tpl_name ) ) { 
        $tpl_source = file_get_contents( $path.DIRECTORY_SEPARATOR.$tpl_name );
        return true;
    }
    return false;
}

function smarty_resource_theme_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    // выполняем обращение для присвоения значения $tpl_timestamp.
    $theme = App::$config->get('template.theme');
    $path = 'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'templates';
        
    if ( file_exists( $path.DIRECTORY_SEPARATOR.$tpl_name ) ) { 
        $tpl_timestamp = filemtime ( $path.DIRECTORY_SEPARATOR.$tpl_name );
        return true;
    }
    return false;
}

function smarty_resource_theme_secure($tpl_name, &$smarty)
{
    // предполагаем, что шаблоны безопасны
    return true;
}

function smarty_resource_theme_trusted($tpl_name, &$smarty)
{
    // не используется для шаблонов
}
?> 