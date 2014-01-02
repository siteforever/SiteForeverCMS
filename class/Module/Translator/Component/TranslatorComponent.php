<?php
/**
 * Make showed catalogue property
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Translator\Component;

use Symfony\Component\Translation\Translator;

class TranslatorComponent extends Translator
{
    /**
     * @param null $domain
     * @param null $locale
     * @return array
     */
    public function getCatalogues($domain = null, $locale = null)
    {
        if (null === $locale) {
            $locale = $this->getLocale();
        }

        if (null === $domain) {
            $domain = 'messages';
        }

        if (!isset($this->catalogues[$locale])) {
            $this->loadCatalogue($locale);
        }

        $catalogue = $this->catalogues[$locale]->all($domain);

        return $catalogue;
    }
}
