<?php
/**
 * Установить новую копию
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */


$location = "X:\\home\\";

$dest = isset($_POST['dest']) ? $_POST['dest'] : false;


if ( ! $dest ) {
    ?>
    <html>
    <body>
    <form action="/install.php" method="post">
        Destenation path <?=$location?> <input name="dest" value="gal/test" />
        <input type="submit" value="Create" />
    </form>
    </body>
    </html>
    <?php
    die();
}

header("content-type: text/plain; charset=cp866");

$location .= $dest;

$src = dirname(__FILE__);

print 'Source: '.$src."\r\n";

$location = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, $location );

print 'Destenation path: '.$location."\r\n";

system("mkdir $location");


system("mklink /D {$location}\\class {$src}\\class");
system("mklink /D {$location}\\misc {$src}\\misc");
system("mklink /D {$location}\\widgets {$src}\\widgets");
system("mklink /D {$location}\\images {$src}\\images");
system("mklink /H {$location}\\.htaccess {$src}\\.htaccess");
system("mklink /H {$location}\\bootstrap.php {$src}\\bootstrap.php");
system("mklink /H {$location}\\functions.php {$src}\\functions.php");
system("mklink /H {$location}\\config.php {$src}\\config.php");
system("mklink /H {$location}\\modules.php {$src}\\modules.php");

system("copy {$src}\\index.php {$location}\\index.php /Y");
system("copy {$src}\\modules.php {$location}\\modules.php /Y");

system("mkdir {$location}\\files");
system("mkdir {$location}\\themes");
system("xcopy themes {$location}\\themes /S/E/Y");
//system("mkdir {$location}\\protected");
//system("mkdir {$location}\\protected\\_runtime");
system("mkdir {$location}\\protected\\_runtime\\_cache");
system("mkdir {$location}\\protected\\_runtime\\_templates_c");
system("mkdir {$location}\\protected\\config");
system("mklink /D {$location}\\protected\\lang {$src}\\protected\\lang");

system("copy {$src}\\protected\\config\\main.php {$location}\\protected\\config\\main.php /Y");
