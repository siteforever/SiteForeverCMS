<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 15.09.11
 * Time: 13:07
 * To change this template use File | Settings | File Templates.
 * Таблица категорий баннеров
 * @author bear1988
 */
 
class Data_Table_CategoryBanner extends Data_Table
{
    /**
     * Вернет имя таблицы
     * @return string
     */
    protected function getTable()
    {
        return 'category_banner';
    }

    /**
     * Создаст список полей
     * @return array
     */
    protected function doGetFields()
    {
        return array(
            new Data_Field_Int('id', 11, true, null, true),
            new Data_Field_Varchar('name', 255),
        );
    }
}
