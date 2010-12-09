<?
header('content-type: text/plain');

if ( preg_match( '/[^\.]+/', $_SERVER['HTTP_HOST'], $m ) )
{

	$name = $m[0].date('Ymd_Hi').'.tgz';

	print "creating archive $name\n";
	print system("tar -czf {$name} .htaccess *");
}
?>