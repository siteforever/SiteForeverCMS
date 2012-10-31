<?php
/**
 * Unpack archive
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
$project = $_GET['name'];

if ( ! file_exists( sprintf('%s.zip',$project) ) ) {
    die('archive not found');
}

system( sprintf('unzip %s.zip', $project) );
system( sprintf('rm %s.zip', $project) );
system( 'rm unpack.php' );