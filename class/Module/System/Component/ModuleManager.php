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

class ModuleManager
{
    private $root;

    private $sfPath;

    private $availableModules;

    /**
     * @var TranslatorComponent
     */
    private $translator;

    function __construct($root, $sfPath, TranslatorComponent $translator)
    {
        $this->root = $root;
        $this->sfPath = $sfPath;
        $this->translator = $translator;
    }

    /**
     * Вернет список доступных модулей
     * Нужны для составления списка создания страницы в админке
     * @return array|null
     */
    public function getAvailableModules()
    {
        if (null === $this->availableModules) {
            $locator = new FileLocator([$this->root, $this->sfPath]);

            $controllersFile = $locator->locate('app/controllers.xml');
            $content = file_get_contents($controllersFile);

            if (!$content) {
                return array();
            }

            $xmlControllers = new \SimpleXMLElement($content);

            $this->availableModules = array();

            foreach ($xmlControllers->children() as $child) {
                $this->availableModules[(string)$child['name']] = array('label' => (string)$child->label);
            }
        }

        $ret = array();
        foreach ($this->availableModules as $key => $mod) {
            $ret[$key] = $this->translator->trans($mod['label']);
        }

        return $ret;
    }
}
