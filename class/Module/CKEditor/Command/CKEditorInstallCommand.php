<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\CKEditor\Command;


use Sfcms\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CKEditorInstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ckeditor:install')
            ->setDescription('Installing CKEditor for usage')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $source = SF_PATH . '/vendor/ckeditor/ckeditor';
        $target = ROOT . '/static/ckeditor';
        if ($fs->exists($target)) {
            $fs->remove($target);
        }
        $fs->symlink($source, $target);
        $output->writeln('Installing complete');
    }
}
