<?php
/**
 * Unpack archive
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
//header('content-type: text/plain');



if ( isset($_SERVER['HTTP_HOST']) && preg_match( '/[^\.]+/', $_SERVER['HTTP_HOST'], $m ) )
{
    $name = $m[0];
} else {
    $name = 'backup';
}

$file = $name.date('Ymd_Hi').'.tgz';
print system("tar -czf ../{$file} .htaccess *");
