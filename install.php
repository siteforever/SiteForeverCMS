<?php
/**
 * Установить новую копию
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
header("content-type: text/html; charset=cp866");


error_reporting( 0 );
define('DS', DIRECTORY_SEPARATOR);
$location = trim( realpath( __DIR__.DS.'..'.DS.'..' ), DS ) . DS;

$dest = isset($_POST['dest']) && $_POST['dest'] ? $_POST['dest'] : "";
$name = isset($_POST['name']) && $_POST['name'] ? $_POST['name'] : "";



?>
<html>
<body>
<form action="/install.php" method="post">
    <p>Name project <input name="name" value="<?=$name?>" /></p>
    <p>Destenation path <?=$location?><input name="dest" value="<?=$dest?>" /></p>
    <input type="submit" value="Create" />
</form>
    <pre><?php
//    print_r( $_SERVER );
    ?></pre>
</body>
</html>
<?php
if ( ! $dest || ! $name ) {
    die();
}

$location .= $dest;

$src = __DIR__;

print 'Source: '.$src."<br>\n";

$location = str_replace(array('\\','/'), DS, $location );

print 'Destenation path: '.$location."<br>\n";

//    if ( file_exists( $location ) ) {
//        unlink( $location );
//    }
@mkdir( $location, 0755, true );

//system("mkdir $location");


symlink( $src.DS.'misc', $location.DS.'misc' );
//    system("mklink /D {$location}\\misc {$src}\\misc");
print "<br>\n";
//    system("mklink /D {$location}\\images {$src}\\images");
symlink( $src.DS.'images', $location.DS.'images' );
print "<br>\n";

copy( $src.DS.'modules.php', $location.DS.'modules.php' );
copy( $src.DS.'index.php', $location.DS.'index.php' );

copy( $src.DS.'.htaccess', $location.DS.'.htaccess' );

mkdir($location.DS.'files', 0777, true);
mkdir($location.DS.'themes'.DS.$name, 0777, true);
mkdir($location.DS.'themes'.DS.$name.DS.'css', 0777, true);
copy( $src.DS.'themes'.DS.'basic'.DS.'css'.DS.'style.css', $location.DS.'themes'.DS.$name.DS.'css'.DS.'style.css' );
mkdir($location.DS.'themes'.DS.$name.DS.'js', 0777, true);
copy( $src.DS.'themes'.DS.'basic'.DS.'js'.DS.'script.js', $location.DS.'themes'.DS.$name.DS.'js'.DS.'script.js' );
mkdir($location.DS.'themes'.DS.$name.DS.'images', 0777, true);
mkdir($location.DS.'themes'.DS.$name.DS.'templates', 0777, true);
copy( $src.DS.'themes'.DS.'basic'.DS.'templates'.DS.'index.tpl', $location.DS.'themes'.DS.$name.DS.'templates'.DS.'index.tpl' );
copy( $src.DS.'themes'.DS.'basic'.DS.'templates'.DS.'inner.tpl', $location.DS.'themes'.DS.$name.DS.'templates'.DS.'inner.tpl' );
//    system("mkdir {$location}\\files");
//    system("mkdir {$location}\\themes");
//    system("xcopy themes {$location}\\themes /S/E/Y");
//system("mkdir {$location}\\protected");
//system("mkdir {$location}\\protected\\_runtime");

//    mkdir("{$location}\\protected\\_runtime\\_cache", 0777, true);
mkdir($location.DS."protected".DS."_runtime".DS."_templates_c", 0777, true);
mkdir($location.DS."protected".DS."config", 0777, true);

//    system("mkdir {$location}\\protected\\_runtime\\_cache");
//    system("mkdir {$location}\\protected\\_runtime\\_templates_c");
//    system("mkdir {$location}\\protected\\config");
//    system("mklink /D {$location}\\protected\\lang {$src}\\protected\\lang");

copy( $src.DS.'protected'.DS.'config'.DS.'main.php', $location.DS.'protected'.DS.'config'.DS.$name.'.php' );