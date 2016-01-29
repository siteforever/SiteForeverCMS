<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Plugin;

class AssetPlugin
{
    private $styleSheetTemplate = '<link rel="stylesheet" type="text/css" href="/%s">';

    private $javaScriptTemplate = '<script type="text/javascript" src="/%s"></script>';

    public function __construct()
    {
    }

    private function getThemePath(\Smarty_Internal_Template $smarty)
    {
        /** @var \Smarty_Variable $theme */
        $path = $smarty->tpl_vars['path'];
        return trim($path->value['theme'], ' \\/') . '/';
    }

    /**
     * style function plugin
     *
     * @param $params
     * @param \Smarty_Internal_Template $smarty
     * @return array
     */
    public function functionStyle($params, \Smarty_Internal_Template $smarty)
    {
        $return = [];

        $files = isset($params['file']) ? $params['file'] : null;
        if (is_string($files)) {
            $files = explode(',', $files);
        }

        if (!is_array($files)) {
            throw new \InvalidArgumentException('Parameter "file" must be string or array.');
        }

        foreach ($files as $file) {
            $file = str_replace(['@root:', '@theme:'], ['', $this->getThemePath($smarty)], $file);
            $return[] = sprintf($this->styleSheetTemplate, $file);
        }

        return join(PHP_EOL, $return);
    }

    /**
     * js function plugin
     *
     * @param $params
     * @param \Smarty_Internal_Template $smarty
     * @return string
     */
    public function functionJs($params, \Smarty_Internal_Template $smarty)
    {
        $return = [];

        $files = isset($params['file']) ? $params['file'] : null;
        if (is_string($files)) {
            $files = explode(',', $files);
        }

        if (!is_array($files)) {
            throw new \InvalidArgumentException('Parameter "file" must be string or array.');
        }

        foreach ($files as $file) {
            $file = str_replace(['@root:', '@theme:'], ['', $this->getThemePath($smarty)], $file);
            $return[] = sprintf($this->javaScriptTemplate, $file);
        }

        return join(PHP_EOL, $return);
    }
}
