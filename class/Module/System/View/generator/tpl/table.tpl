<?php
/**
 * DB Table {$name}
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

class Data_Table_{$name} extends Data_Table
{
    /**
     * Create field list
     * @return array
     */
    protected function doGetFields()
    {
        return array(
{foreach from=$fields item="f"}
            new Data_Field_{$f.type}( '{$f.name}', {$f.length}, {$f.notnull}, {$f.default}, {$f.autoincrement} ),
{/foreach}
        );
    }

    /**
     * DB table name
     * @return string
     */
    protected function getTable()
    {
        return '{$table}';
    }
}