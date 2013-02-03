<?php
/**
 * Кэш страниц
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

use Sfcms\Component;

class Sfcms_Cache extends Component
{
    const LIFETIME = 5; // seconds

    /** @var string */
    protected $_cache = '';

    /**
     * @return bool
     */
    public function isAvaible()
    {
        if ( $this->app()->getConfig()->get('cache')
            && ! $this->app()->getRequest()->getAjax()
            && ! $this->app()->getRouter()->isSystem() ) {
            if ( $this->app()->getAuth()->currentUser()->get('perm') == USER_GUEST ) {
                if ( ! $this->app()->getBasket()->count() ) {
                    $this->log('Cache true');
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCache()
    {
        return $this->_cache;
    }


    /**
     * @param $content
     */
    public function setCache( $content )
    {
        $this->_cache = $content;
    }


    /**
     * @return mixed
     */
    private function generateKey()
    {
        return preg_replace( '/[^a-z0-9]+/ui', '-', App::getInstance()->getRouter()->getRoute());
    }


    /**
     * @return bool
     */
    public function isCached()
    {
        if ( $this->_cache ) {
            return true;
        }
        if ( $this->load() ) {
            return true;
        }
        return false;
    }


    /**
     * Сохранить кэш
     */
    public function save()
    {
        if ( $this->_cache )
            file_put_contents( $this->getFilename(), $this->_cache );
    }


    /**
     * Загрузит кэш из файла
     * @return bool
     */
    public function load()
    {
        if ( file_exists( $this->getFilename() ) && filemtime( $this->getFilename() ) + self::LIFETIME > time() ) {
            $this->_cache = file_get_contents( $this->getFilename() );
            return true;
        }
        return false;
    }


    /**
     * @return string
     */
    protected function getFilename()
    {
        $path   = SF_PATH . DIRECTORY_SEPARATOR . '_runtime'
                          . DIRECTORY_SEPARATOR . '_cache';
        if ( ! is_dir( $path ) ) {
            mkdir( $path, '0777', true );
        }
        $result = $path . DIRECTORY_SEPARATOR . $this->generateKey() . '.cache';
        return $result;
    }
}
