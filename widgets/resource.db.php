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
function smarty_resource_db_source( $tpl_name, $tpl_source, $smarty )
{
    // выполняем обращение к базе данных для получения шаблона
    // и занесения полученного результата в в $tpl_source
    if ( $tpl = App::$templates->findByName( $tpl_name )  )
    {
        $tpl_source = $tpl['template'];
        return true;
    }
    return false;
}

function smarty_resource_db_timestamp($tpl_name, $tpl_timestamp, $smarty)
{
    // выполняем обращение к базе данных для присвоения значения $tpl_timestamp.
    if ( $tpl = App::$templates->findByName( $tpl_name )  )
    {
        $tpl_timestamp = $tpl['update'];
        return true;
    }
    return false;
}

function smarty_resource_db_secure($tpl_name, $smarty)
{
    // предполагаем, что шаблоны безопасны
    return true;
}

function smarty_resource_db_trusted($tpl_name, $smarty)
{
    // не используется для шаблонов
}
?> 