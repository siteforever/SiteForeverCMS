<?php
/**
 * Вернет название месяца
 * @author: keltanas
 * @link http://siteforever.ru
 */
function smarty_function_month( $params )
{
    $month = isset( $params['n'] ) ? $params['n'] : strftime('%m');
    $months = array(
        '01' => 'Января',
        '02' => 'Февраля',
        '03' => 'Марта',
        '04' => 'Апреля',
        '05' => 'Мая',
        '06' => 'Июня',
        '07' => 'Июля',
        '08' => 'Августа',
        '09' => 'Сентября',
        '10' => 'Октября',
        '11' => 'Ноября',
        '12' => 'Декабря',
    );
    return $months[ $month ];
}