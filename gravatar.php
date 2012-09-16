<?php
/**
 * Gravatar request example
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('exception_error_handler');

if( isset( $_GET['email'] ) ) {
    $email = $_GET['email'];
} else {
    die('You must specify email');
}


$hash = md5( strtolower( trim( $email ) ) );


try {
    $gravatar = unserialize( file_get_contents( "http://www.gravatar.com/{$hash}.php" ) );

    print "<h4>Avatar {$gravatar['entry'][0]['displayName']}</h4>";
    print "<img src=\"{$gravatar['entry'][0]['thumbnailUrl']}\" alt=\"{$email}\">";

    print '<h4>Has accounts</h4>';
    print '<ul>';
    foreach ( $gravatar['entry'][0]['accounts'] as $account ) {
        print "<li><a href='{$account['url']}' target='_blank'>{$account['domain']}</a></li>";
    }
    print '</ul>';

} catch ( Exception $e ) {
    print "Fail gravatar request\n";
}