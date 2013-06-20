<?php
use Sfcms\i18n;

/**
 * Напечатать переведенный текст
 * @param string $cat
 * @param string $text
 * @param array $params
 * @deprecated
 * @return mixed
 */
function t( $cat, $text = '', $params = array() )
{
    return call_user_func_array(array(App::getInstance()->getContainer()->get('i18n'), 'write'), func_get_args());
}





