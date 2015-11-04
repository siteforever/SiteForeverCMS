<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\ElFinder;

use ErrorException;
use Sfcms;
use Exception;
use Composer\Script\Event;
use Composer\Package\RootPackage;

class ComposerHandler
{

    static $source;

    static $dest;

    public static function installAssets( Event $event )
    {
        /** @var $pack RootPackage */
        $pack = $event->getComposer()->getPackage();

        $extra = $pack->getExtra();
        $outDir = 'sfcms-static-dir';
        if ( ! isset( $extra[ $outDir ] ) ) {
            throw new Exception(sprintf('Param "%s" not defined',  $outDir ));
        }

//        print_r($_SERVER);

        $rootDir = getenv('PWD');

        self::$source = $rootDir . '/vendor/studio-42/elfinder';
        self::$dest   = $rootDir . '/'.$extra[ $outDir ].'/admin/jquery/elfinder';

        try {
            mkdir(self::$dest, 0755, true);
        } catch(Exception $e) {};

        $files = array(
            'css/*',
            'img/*',
            'js/*',
        );

        foreach ($files as $file) {
            print "Copy: ".self::$source."/$file\n";
            static::copy( $file, self::$source, self::$dest);
        }

    }

    /**
     * Copy files by mask
     * @param $expression
     * @param $source
     * @param $dest
     *
     * @return bool
     */
    private static function copy($expression, $source, $dest)
    {
        $dest_path = $dest . '/' . $expression;
        $src_path  = $source . '/' . $expression;
        if ( false === strrpos($expression, '*') ) {
            return copy(rtrim($src_path, '/\\'), rtrim($dest_path, '/\\'));
        }
        if ( ! is_dir(rtrim($dest_path, '/*')) ) {
            mkdir(rtrim($dest_path, '/*'), 0755, true);
        }
        foreach(glob($src_path) as $file) {
            $file = str_replace($source, '', $file);
            self::copy(trim($file . (is_dir($source.'/'.$file) ? '/*' : ''), '/'), $source, $dest);
        }
        return true;
    }
}
