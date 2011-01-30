<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Файл:     resource.system.php
* Тип:      resource
* Имя:      system
* Назначение:  Получает шаблон из системной директории
* -------------------------------------------------------------
*/
function smarty_resource_system_source($tpl_name, $tpl_source, $smarty)
{
    // выполняем обращение к базе данных для получения шаблона
    // и занесения полученного результата в в $tpl_source
    $path = App::$config->get('template.admin');
    if ( file_exists( $path.DIRECTORY_SEPARATOR.$tpl_name ) ) { 
        $tpl_source = file_get_contents( $path.DIRECTORY_SEPARATOR.$tpl_name );
        return true;
    }
    return false;
}

function smarty_resource_system_timestamp($tpl_name, $tpl_timestamp, $smarty)
{
    // выполняем обращение к базе данных для присвоения значения $tpl_timestamp.
    $path = App::$config->get('template.admin');
    if ( file_exists( $path.DIRECTORY_SEPARATOR.$tpl_name ) ) { 
        $tpl_timestamp = filemtime ( $path.DIRECTORY_SEPARATOR.$tpl_name );
        return true;
    }
    return false;
}

function smarty_resource_system_secure($tpl_name, $smarty)
{
    // предполагаем, что шаблоны безопасны
    return true;
}

function smarty_resource_system_trusted($tpl_name, $smarty)
{
    // не используется для шаблонов
}
?> 