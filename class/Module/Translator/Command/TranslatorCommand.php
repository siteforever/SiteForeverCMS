<?php
/**
 * Debugging translator
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Translator\Command;

use Module\Translator\Component\TranslatorComponent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TranslatorCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('debug:translator')
            ->setDescription('Print all translate phrases')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message for translation')
            ->addOption('domain', 'd', InputOption::VALUE_OPTIONAL, 'Define custom domain (default "messages")')
            ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'Define custom locale')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var TranslatorComponent $translator */
        $translator = \App::cms()->getContainer()->get('translator');

        $message = $input->getArgument('message');
        $domain = $input->getOption('domain');
        $locale = $input->getOption('locale');

        if ($message) {
            $output->writeln(sprintf('<info>%s</info>', $translator->trans($message, array(), $domain, $locale)));
        } else {
            $catalogues = $translator->getCatalogues($domain, $locale);

            if ($catalogues) {
                foreach ($catalogues as $key => $value) {
                    $output->writeln(sprintf('%-30s <info>\'%s\'</info>', $key, $value));
                }
            } else {
                $output->writeln(sprintf('<error>Not found for domain "%s" and locale "%s"</error>', $domain, $locale));
            }
        }
    }
}
