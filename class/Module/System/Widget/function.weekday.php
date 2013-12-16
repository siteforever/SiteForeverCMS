<?php
/**
 * Вернет название дня недели
 * @author: keltanas
 * @link http://siteforever.ru
 */
function smarty_function_weekday( $params )
{
    $day = isset( $params['day'] ) ? $params['day'] : strftime('%w');
    $days = array(
        'Воскресенье',
        'Понедельник',
        'Вторник',
        'Среда',
        'Четверг',
        'Пятница',
        'Суббота',
    );
    return $days[ $day ];
}