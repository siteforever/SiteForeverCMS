<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Command;

use Sfcms\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UuidCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('catalog:uuid')
            ->setDescription('test generate uuid')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        print_r($_SERVER);
        foreach (range(0, 20) as $i) {
//            $output->writeln(uniqid("", true));
//            $uid = bin2hex(uniqid("", true) | hex2bin(md5(__DIR__)));
//            $uuid = preg_replace('/^(?:\w{4})(\w{12})(\w{4})(\w{4})(\w{4})(\w{8}).*/', '$5-$4-$3-$2-$1', $uid);
//            $output->writeln($uuid . ' (' . strlen($uuid) . ') <= ' . $uid);
            $output->writeln(UUID::v5(md5(__DIR__), bin2hex(uniqid())));
        }
    }
}
