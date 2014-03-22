<?php
/**
 * Debugging translator
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Translator\Command;

use Module\Translator\Component\TranslatorComponent;
use Sfcms\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateCommand extends Command
{
    protected $dictionary;

    protected function getDest()
    {
        return ROOT . '/static/i18n';
    }

    protected function configure()
    {
        $this
            ->setName('translator:generate')
            ->setDescription('Generating translating file for client scripts')
            ->addOption('domain', 'd', InputOption::VALUE_OPTIONAL, 'Define custom domain (default "messages")')
            ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'Define custom locale')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var TranslatorComponent $translator */
        $translator = $this->getContainer()->get('translator');

        $domain = $input->getOption('domain');
        $locale = $input->getOption('locale');

        $locales = array_merge(array($translator->getLocale()), $translator->getFallbackLocales());

        $start = microtime(1);
        $fs = new Filesystem();

        if (!$fs->exists($this->getDest())) {
            $fs->mkdir($this->getDest(), 0777, true);
        }

        // Prepare dictionary for JS
        $jsI18nFile = __DIR__. '/../static/i18n.js';
        $output->writeln(sprintf('Source file <info>%s</info>', $jsI18nFile));

        foreach ($locales as $locale) {
            $this->dictionary = $translator->getCatalogues($domain, $locale);

            $jsDestFile = $this->getDest() . '/' . $locale . '.js';
            $output->writeln(sprintf('Dest file <info>%s</info>', $jsDestFile));

            $f = fopen($jsDestFile, 'a');
            flock($f, LOCK_EX);
            ftruncate($f, 0);

            $jsDict = "// RUNTIME DICTIONARY FILE\n\n" . file_get_contents($jsI18nFile);
            $dict = defined('JSON_UNESCAPED_UNICODE')
                ? json_encode($this->dictionary, JSON_UNESCAPED_UNICODE)
                : json_encode($this->dictionary);

            $jsDict = str_replace('/*:dictionary:*/', 'i18n._dict = ' . $dict . ';', $jsDict);

            fwrite($f, $jsDict, strlen($jsDict));
            flock($f, LOCK_UN);
            fclose($f);
        }

        $output->writeln(sprintf('i18n js generation time <info>%.3f</info> sec', round(microtime(1) - $start)));
    }
}