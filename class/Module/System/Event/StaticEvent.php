<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Event;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

class StaticEvent extends Event
{
    const STATIC_INSTALL = 'static.install';

    /** @var string */
    private $staticDir;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    public function __construct($staticDir, InputInterface $input, OutputInterface $output)
    {
        $this->staticDir = $staticDir;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getStaticDir()
    {
        return $this->staticDir;
    }
}
