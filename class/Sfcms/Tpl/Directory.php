<?php
/**
 * Directory
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Tpl;

class Directory
{
    protected $widgets = array();

    protected $templates = array();

    public function addTplDir($dir)
    {
        $this->templates[] = $dir;
    }

    public function getTplAll()
    {
        return $this->templates;
    }

    public function clearTpl()
    {
        $this->templates = array();
    }

    public function addWidgetsDir($dir)
    {
        $this->widgets[] = $dir;
    }

    public function getWidgetsAll()
    {
        return $this->widgets;
    }

    public function clearWidgets()
    {
        $this->widgets = array();
    }
}
