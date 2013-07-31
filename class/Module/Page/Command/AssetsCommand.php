<?php
namespace Module\Page\Command;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Asset\StringAsset;
use Sfcms\View\Layout;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for composing static files
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
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

        $collection = new AssetCollection(array(
            new FileAsset($misc.'/bootstrap/js/bootstrap.js'),
            new StringAsset('define("twitter");'),
            new GlobAsset($misc.'/admin/jquery/*.js'),
            new GlobAsset($misc.'/admin/catalog/*.js'),
            new GlobAsset($misc.'/module/*.js'),
//            new FileAsset($misc . '/jquery/jquery.blockUI.js'),
//            new StringAsset('define("jquery/jquery.blockUI");'),
            new FileAsset($misc.'/jquery/jquery-ui-'.Layout::JQ_UI_VERSION.'.custom.min.js'),
            new StringAsset('define("jui");'),
//            new FileAsset($misc.'/elfinder/js/elfinder.min.js'),
//            new StringAsset('define("elfinder/js/elfinder.full.js");'),
//            new StringAsset('define("elfinder/js/elfinder.min.js");'),

            new FileAsset($misc.'/admin/admin.js'),
            new FileAsset($misc.'/admin/app.js'),
        ));

        file_put_contents($targetFile, $collection->dump());
        $output->writeln('<info>Admin js created.</info>');

        $requirejs = new FileAsset($misc . '/require-jquery-min.js');
        file_put_contents(ROOT . '/static/require-jquery-min.js', $requirejs->dump());
    }

}
