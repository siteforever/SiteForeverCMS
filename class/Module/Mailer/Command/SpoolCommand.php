<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\Mailer\Command;

use Sfcms\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SpoolCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mailer:spool:send')
            ->setDescription('Sends emails from the spool')
            ->addOption('message-limit', 0, InputOption::VALUE_OPTIONAL, 'The maximum number of messages to send.')
            ->addOption('time-limit', 0, InputOption::VALUE_OPTIONAL, 'The time limit for sending messages (in seconds).')
            ->addOption('recover-timeout', 0, InputOption::VALUE_OPTIONAL, 'The timeout for recovering messages that have taken too long to send (in seconds).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->processMailer($input, $output);
    }

    private function processMailer(InputInterface $input, OutputInterface $output)
    {
        $output->write(sprintf('Processing mailer... '));

        if ($this->getContainer()->getParameter('mailer.spool')) {
            $mailer = $this->getContainer()->get('mailer');
            $transport = $mailer->getTransport();
            if ($transport instanceof \Swift_Transport_SpoolTransport) {
                $spool = $transport->getSpool();
                if ($spool instanceof \Swift_ConfigurableSpool) {
                    $spool->setMessageLimit($input->getOption('message-limit'));
                    $spool->setTimeLimit($input->getOption('time-limit'));
                }
                if ($spool instanceof \Swift_FileSpool) {
                    if (null !== $input->getOption('recover-timeout')) {
                        $spool->recover($input->getOption('recover-timeout'));
                    } else {
                        $spool->recover();
                    }
                }
                $sent = $spool->flushQueue($this->getContainer()->get('mailer_transport_real'));

                $output->writeln(sprintf('<comment>%d</comment> emails sent', $sent));
            }
        } else {
            $output->writeln('No email to send as the spool is disabled.');
        }
    }
}
