<?php
/**
 * Command Admicons
 * @generator SiteForeverGenerator
 */

namespace Module\System\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AdmiconsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('system:admicons')
            ->setDescription('Generate CSS base64 sprite for admin icons')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../static/icons')->name('*.png');

        $css = array('.sfcms-icon {'
            .'display: inline-block;'
            .'width: 16px;'
            .'height: 16px;'
            .'line-height: 16px;'
            .'margin-top: 1px;'
            .'vertical-align: text-top;'
            .'}'
        );

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $is = getimagesize($file->getRealPath());
            $css[] = '.sfcms-icon-'.strtolower(preg_replace(array('/_/', '/\..*?$/'), array('-',''), $file->getBasename()))
                   . '{background:'
                   . 'url("data:'.$is['mime'].';base64,'.base64_encode(file_get_contents($file->getRealPath())).'")'
                   . ' no-repeat 0 0;}';
        }
        file_put_contents(__DIR__ . '/../static/icons.css', join("\n", $css));
        $output->writeln("<info>done</info>");
    }
}
