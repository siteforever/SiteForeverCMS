<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Page\Command;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class AssetsCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('page:assets')
            ->setDescription('Assetics page statics')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetFile = ROOT.'/static/admin.js';
        $misc = SF_PATH . DIRECTORY_SEPARATOR . 'misc';
        $exists = file_exists($targetFile);

        $files = array(
//            '/jquery/jquery.form.js',
//            '/jquery/jquery.blockUI.js',
//            '/elfinder/js/elfinder.full.js',
//            '/elfinder/js/i18n/elfinder.ru.js',
//            '/module/modal.js',
//            '/admin/catalog.js',
//            '/admin/forms.js',
            '/bootstrap/js/bootstrap.js',
            '/admin/jquery/jquery.dumper.js',
            '/admin/jquery/jquery.filemanager.js',
            '/admin/jquery/jquery.realias.js',
            '/admin/admin.js',
            '/admin/app.js',
        );
//        if ($exists) {
//            $lastModTarget = filemtime($targetFile);
//            $lastModSorce = array_reduce($files, function($modify, $file) use ($misc) {
//                    $l = filemtime($misc.$file);
//                    return $l > $modify ? $l : $modify;
//                }, 0);
//        }
//        if (!$exists || $lastModSorce > $lastModTarget) {
        $collection = new AssetCollection();
        foreach ($files as $file) {
            $collection->add(new FileAsset($misc . $file));
        }
        file_put_contents($targetFile, $collection->dump());
//        }
    }

}
