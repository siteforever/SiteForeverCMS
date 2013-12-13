<?php
/**
 * Event for dashboard building
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Dashboard\Event;

use Symfony\Component\EventDispatcher\Event;

class DashboardEvent extends Event
{
    const EVENT_BUILD = 'dashboard.build';

    private $panels = array();

    public function setPanel($name, $title, $content)
    {
        $this->panels[$name] = array(
            'title' => $title,
            'content' => $content,
        );
    }

    public function removePanel($name)
    {
        unset($this->panels[$name]);
    }

    public function getPanels()
    {
        return $this->panels;
    }
}
