<?php
/**
 * Отвечает за установку
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Sfcms_Installer
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Инсталлирует каталоги со статикой, если еще не инсталлированы
     */
    public function installationStatic()
    {
        $this->symlink( SF_PATH . self::DS . 'images', ROOT . self::DS . 'images' );
        $this->symlink( SF_PATH . self::DS . 'misc', ROOT . self::DS . 'misc' );

        if( ! is_dir( ROOT . self::DS. '_runtime' . self::DS . 'sxd' ) ) {
            $this->copyDir( SF_PATH . self::DS . 'vendors' . self::DS . 'sxd',
                ROOT . self::DS. '_runtime' . self::DS . 'sxd' );
        }
    }


    /**
     * Создание рекурсивной копии каталога
     * @param $from
     * @param $to
     *
     * @return void
     */
    public function copyDir( $from, $to )
    {
        if( ! is_dir( $to ) ) {
            @mkdir( $to, 0755, 1 );
        }
        $files = glob( $from . self::DS . '*' );
        foreach( $files as $file ) {
            if( is_dir( $file ) ) {
                $this->copyDir( $file, $to . self::DS . basename( $file ) );
            } elseif( is_file( $file ) ) {
                $this->copy( $file, $to . self::DS . basename( $file ) );
            }
        }
    }


    /**
     * Создает каталог
     * @param $dir
     * @param string $mode
     * @param bool $recursive
     * @return bool
     */
    public function mkdir( $dir, $mode = '0777', $recursive = true )
    {
        if ( ! is_dir( $dir ) ) {
            mkdir( $dir, $mode, $recursive );
            return true;
        }
        return false;
    }


    /**
     * Создает копию файла
     * @param $src
     * @param $dest
     * @return bool
     */
    public function copy( $src, $dest )
    {
        if ( file_exists( $src ) && ! file_exists( $dest ) ) {
            copy( $src, $dest );
            return true;
        }
        return false;
    }

    /**
     * Создает копию файла
     * @param $src
     * @param $dest
     * @return bool
     */
    public function copyReplaced( $src, $dest, $replaced )
    {
        if ( file_exists( $src ) && ! file_exists( $dest ) ) {
            $content = file_get_contents( $src );
            foreach ( $replaced as $key => $val ) {
                $content = str_replace( $key, $val, $content );
            }
            file_put_contents( $dest, $content );
            return true;
        }
        return false;
    }


    /**
     * Создает символическую ссылку на каталог
     * @param $src
     * @param $dest
     * @return bool
     */
    public function symlink( $src, $dest )
    {
        if ( ! is_dir( $dest ) && is_dir( $src ) ) {
            symlink( $src, $dest );
            return true;
        }
        return false;
    }
}
