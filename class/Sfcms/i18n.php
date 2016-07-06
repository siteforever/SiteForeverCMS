<?php
namespace Sfcms;

use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Translator;

/**
 * Translator
 */

class i18n
{
    private $_dictionary = null;
    private $_debug = false;
    private $_dest = '';
    private $logger = null;

    /** @var Translator */
    private $translator;

    /**
     * @var i18n
     */
    private static $_instance;

    protected $_table = array(
        'кон' => 'con',
        'ком' => 'com',
        'кат' => 'cat',
        'а'   => 'a',
        'б'   => 'b',
        'в'   => 'v',
        'г'   => 'g',
        'д'   => 'd',
        'е'   => 'e',
        'ё'   => 'e',
        'ж'   => 'zh',
        'з'   => 'z',
        'и'   => 'i',
        'й'   => 'i',
        'к'   => 'k',
        'л'   => 'l',
        'м'   => 'm',
        'н'   => 'n',
        'о'   => 'o',
        'п'   => 'p',
        'р'   => 'r',
        'с'   => 's',
        'т'   => 't',
        'у'   => 'u',
        'ф'   => 'f',
        'х'   => 'h',
        'ц'   => 'c',
        'ч'   => 'ch',
        'ш'   => 'sh',
        'щ'   => 'sch',
        'ъ'   => 'j',
        'ы'   => 'y',
        'ь'   => 'j',
        'э'   => 'e',
        'ю'   => 'yu',
        'я'   => 'ya',
        ' '   => '_',
    );

    public function __construct(Translator $translator, $dest = __DIR__, $debug = false)
    {
        $this->translator = $translator;
        $this->_debug = $debug;
        $this->_dest = $dest;
    }

    public function setLocale($category, $locale)
    {
        call_user_func_array('setlocale', func_get_args());
    }

    public function isDebug()
    {
        return $this->_debug;
    }

    /**
     * @return string
     */
    public function getDest()
    {
        return $this->_dest;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Translate message
     * @param $id
     * @param array $parameters
     * @param null $domain
     * @param null $locale
     *
     * @return string
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        if (is_array($id)) {
            switch (count($id)) {
                case 1:
                    list($id) = $id;
                    break;
                case 2:
                    $parameters = $id;
                    $id = array_shift($parameters);
                    break;
            }
        }
        if (null !== $domain) {
            $domain = strtolower($domain);
        }
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Return translated message
     *
     * @param string $message
     *
     * @return string
     */
    public function write($message)
    {
        switch (func_num_args()) {
            case 1:
                return $this->translator->trans($message);
            case 2:
                if (is_string(func_get_arg(1))) {
                    return $this->translator->trans(func_get_arg(1), array(), func_get_arg(0));
                }
                if (is_array(func_get_arg(1))) {
                    return $this->translator->trans(func_get_arg(0), func_get_arg(1));
                }
            case 3:
                return $this->translator->trans(func_get_arg(1), func_get_arg(2), func_get_arg(0));
        }

        return $message;
    }

    /**
     * Transliteration
     * @param string $str
     *
     * @return string
     */
    public function translit($str)
    {
        foreach ($this->_table as $rus => $eng) {
            $str = preg_replace('/' . $rus . '/ui', $eng, $str); // For uft8 support
        }
        $str = preg_replace('/[^a-z0-9]+/i', '-', $str);
        $str = trim($str, '-');

        return $str;
    }
}
