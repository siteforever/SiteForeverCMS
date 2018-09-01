<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 * @version: @version@
 */

namespace Module\System\Component;

use Module\Translator\Component\TranslatorComponent;
use Symfony\Component\Config\FileLocator;
use function var_dump;

class ModuleManager
{
    private $root;

    private $sfPath;

    private $availableModules;

    private $systemParams;

    /**
     * @var TranslatorComponent
     */
    private $translator;

    public function __construct($root, $sfPath, $systemParams, TranslatorComponent $translator)
    {
        $this->root = $root;
        $this->sfPath = $sfPath;
        $this->translator = $translator;
        $this->systemParams = $systemParams;
    }

    /**
     * Вернет список доступных модулей
     * Нужны для составления списка создания страницы в админке
     * @return array|null
     */
    public function getAvailableModules()
    {
        if (null === $this->availableModules) {
            $adminControllers = $this->systemParams['admin_controllers'];
            $this->availableModules = [];

            foreach ($adminControllers as $child) {
                $this->availableModules[(string)$child['name']] = ['label' => (string)$child['label']];
            }
        }

        $ret = array();
        foreach ($this->availableModules as $key => $mod) {
            $ret[$key] = $this->translator->trans($mod['label']);
        }

        return $ret;
    }
}
