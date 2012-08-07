<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link http://siteforever.ru
*/
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     function.dataset.php
* Type:     function
* Name:     dataset
* Purpose:  Выводит таблицу
* -------------------------------------------------------------
*/
function smarty_function_dataset($params, Smarty_Internal_Template $template)
{
    if ( ! isset( $params['data'] ) ) {
        return t('Param "data" not defined');
    }
    if ( ! isset( $params['cols'] ) ) {
        return t('Param "cols" not defined');
    }
    if ( ! isset( $params['hcols'] ) ) {
        return t('Param "hcols" not defined');
    }
    $data = $params['data'];
    $cols = explode(',', $params['cols']);
    $hcols = explode(',', $params['hcols']);

    if ( count( $cols ) != count( $hcols ) ) {
        return t('"cols" not corresponds to "hcols"');
    }

    $dataset = array('<table class="dataset">');
    $dataset[] = '<tr>';
    foreach ( $hcols as $h ) {
        $dataset[] = '<th>'.t($h).'</th>';
    }
    $dataset[] = '</tr>';
    foreach( $data as $d ) {
        $dataset[] = '<tr>';
        foreach( $cols as $col ) {
            if ( '' === $d[$col] ) {
                $dataset[] = '<td>&mdash;</td>';
            } else if ( $d[$col] ) {
                $dataset[] = '<td>'.$d[$col].'</td>';
            }/* else if ( isset( $d->{$col} ) ) {
                $dataset[] = '<td>2:'.$d->{$col}.'</td>';
            } else {
                $dataset[] = '<td>3:'.get_class( $d ).'->'.$col.'</td>';
            }*/
        }
        $dataset[] = '</tr>';
    }
    $dataset[] = '</table>';
    return join("\n", $dataset);
}