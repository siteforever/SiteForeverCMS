<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 15.09.11
 * Time: 13:02
 * To change this template use File | Settings | File Templates.
 * Таблица Баннеров
 * @author bear1988
 */
 
class Data_Table_Banner extends Data_Table
{
    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'banner';
    }

    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Int('cat_id'),
            new Data_Field_Varchar('name', 255),
            new Data_Field_Varchar('url', 255),
            new Data_Field_Varchar('path', 255),
            new Data_Field_Int('count_show'),
            new Data_Field_Int('count_click'),
            new Data_Field_Varchar('target', 255),
        );
    }

}
