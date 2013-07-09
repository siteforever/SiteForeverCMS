<?php
/**
 *
 * @author: keltanas
 */

namespace Sfcms\JqGrid;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\AssetWriter;
use ErrorException;
use Sfcms;
use Exception;
use Composer\Script\Event;
use Composer\Package\RootPackage;

class ComposerHandler {
    /**
     * Этот метод нужен для композера. Он делает сборку библиотек jqGrig в удобоваримый для require-js файл
     */
    public static function installAssets(Event $event)
    {
        /** @var $pack RootPackage */
        $pack = $event->getComposer()->getPackage();

        $extra = $pack->getExtra();
        $outDir = 'sfcms-static-dir';
        if (!isset($extra[$outDir])) {
            throw new Exception(sprintf('Param "%s" not defined', $outDir));
        }

        $rootDir = getcwd();

        $source = $rootDir . '/vendor/tonytomov';
        $out    = $rootDir . '/' . $extra[$outDir] . '/admin/jquery/jqgrid';

        $modules = array(
            new FileAsset($source . '/jqGrid/js/i18n/grid.locale-ru.js'),
            new FileAsset($source . '/jqGrid/js/grid.base.js'),
            new FileAsset($source . '/jqGrid/js/grid.common.js'),
            new FileAsset($source . '/jqGrid/js/grid.formedit.js'),
            new FileAsset($source . '/jqGrid/js/grid.inlinedit.js'),
            new FileAsset($source . '/jqGrid/js/grid.celledit.js'),
            new FileAsset($source . '/jqGrid/js/grid.subgrid.js'),
            new FileAsset($source . '/jqGrid/js/grid.treegrid.js'),
            new FileAsset($source . '/jqGrid/js/grid.grouping.js'),
            new FileAsset($source . '/jqGrid/js/grid.custom.js'),
            new FileAsset($source . '/jqGrid/js/grid.tbltogrid.js'),
            new FileAsset($source . '/jqGrid/js/grid.import.js'),
            new FileAsset($source . '/jqGrid/js/jquery.fmatter.js'),
            new FileAsset($source . '/jqGrid/js/JsonXml.js'),
            new FileAsset($source . '/jqGrid/js/grid.jqueryui.js'),
            new FileAsset($source . '/jqGrid/js/grid.filter.js'),
        );
        $jsAsset = new AssetCollection($modules, array(), $source);
        /** @var FileAsset $asset */
//        foreach ($jsAsset as $asset) {
//            var_dump($asset->getTargetPath());
//            $writer->writeAsset($asset);
//        }
        $jsAsset->setTargetPath($extra[$outDir] . '/admin/jquery/jqgrid/jqgrid.js');

        $writer = new AssetWriter(getcwd());
        $writer->writeAsset($jsAsset);

        $cssAsset = new FileAsset($source . '/jqGrid/css/ui.jqgrid.css');
        $cssAsset->setTargetPath($extra[$outDir] . '/admin/jquery/jqgrid/ui.jqgrid.css');
        $writer->writeAsset($cssAsset);

//        $i18nAsset = new GlobAsset($source . '/jqGrid/js/i18n/*.js');
        /** @var FileAsset $asset */
//        foreach ($i18nAsset as $asset) {
//            $asset->setTargetPath('' . $asset->getTargetPath());
//            var_dump($asset->getTargetPath());
//            $writer->writeAsset($asset);
//        }

        //            'jqGrid/js/i18n/grid.locale-ru.js',


        print sprintf("Lib %s/jqgrid.js was updated\n", $jsAsset->getTargetPath());
        print sprintf("Css %s/ui.jqgrid.css was updated\n", $cssAsset->getTargetPath());
    }
}
