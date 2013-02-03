<?php
/**
 * 
 * @author: keltanas
 */

namespace Sfcms\JqGrid;

use ErrorException;
use Sfcms;

class ComposerHandler {
    /**
     * Этот метод нужен для композера. Он делает сборку библиотек jqGrig в удобоваримый для require-js файл
     */
    public static function installAssets( $event )
    {
//        var_dump( get_class_methods( $event->getComposer()->getPackage()->getExtra('sfcms-jqgrid-build') ) );
        $source = realpath( __DIR__.'/../../..' ) . '/vendor/tonytomov';
        $out    = realpath( __DIR__.'/../../..' ) . '/static/admin/jquery/jqgrid';

        $modules = array(
            'jqGrid/js/i18n/grid.locale-ru',
            'jqGrid/js/grid.base',
            'jqGrid/js/grid.common',
            'jqGrid/js/grid.formedit',
            'jqGrid/js/grid.inlinedit',
            'jqGrid/js/grid.celledit',
            'jqGrid/js/grid.subgrid',
            'jqGrid/js/grid.treegrid',
            'jqGrid/js/grid.grouping',
            'jqGrid/js/grid.custom',
            'jqGrid/js/grid.tbltogrid',
            'jqGrid/js/grid.import',
            'jqGrid/js/jquery.fmatter',
            'jqGrid/js/JsonXml',
            'jqGrid/js/grid.jqueryui',
            'jqGrid/js/grid.filter',
            //            'jquery/jquery.jqGrid',
        );
        $assemble = array_map(function( $mod ) use ( $source ) {
            return file_get_contents( $source . '/' . $mod . '.js' )."\n"
                . ( 'jquery/jquery.jqGrid' != $mod ? sprintf( 'define("%s",function(){});', $mod ) . "\n" : '' );
        },$modules);

        $content = join( "\n", $assemble );
        $content = Sfcms::html()->jsMin( $content );

        try {
            mkdir( rtrim( $out, '/' ), 0777, true );
        } catch( ErrorException $e ) {
            print sprintf("Catalog %s not created. %s\n", rtrim( $out, '/' ), $e->getMessage());
        }

        file_put_contents($out.'/jqgrid.js', $content);
        copy( $source.'/jqGrid/css/ui.jqgrid.css', $out.'/ui.jqgrid.css' );

        print sprintf("Lib %s/jqgrid.js was updated\n", $out);
        print sprintf("Css %s/ui.jqgrid.css was updated\n", $out);
    }
}