<?php
/**
 * Формируем лэйаут для админки
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View\Layout;

use Sfcms\View\Layout;

class Admin extends Layout
{
    /**
     * @param $result
     * @return string
     */
    public function view( $result )
    {
        $misc = $this->getMisc();
        // подключение админских стилей и скриптов
        $this->attachJUI();
        $this->_app->addStyle( $misc . '/admin/admin.css' );
        // jQuery
        $this->_app->addScript( $misc . '/jquery/jquery.form.js' );
        $this->_app->addScript( $misc . '/jquery/jquery.blockUI.js' );

//        $this->attachWysiwyg();

        $this->_app->addStyle( $misc . '/elfinder/css/elfinder.css' );
        $this->_app->addScript( $misc . '/elfinder/js/elfinder.full.js' );
        $this->_app->addScript( $misc . '/elfinder/js/i18n/elfinder.ru.js' );

        $this->_app->addScript( $misc . '/etc/modal.js' );

        $this->_app->addScript( $misc . '/admin/catalog.js' );

        // Собираем админский скрипт из библиотек
        $adminJs = array('// DO NOT MODIFY THIS FILE. IS FILE WAS GENERATED. DATE '.date('d M Y, H:i'));
        $adminJs[] = file_get_contents( SF_PATH.$misc.DS.'admin'.DS.'forms.js' );
        $adminJs[] = file_get_contents( SF_PATH.$misc.DS.'admin'.DS.'jquery'.DS.'jquery.dumper.js' );
        $adminJs[] = file_get_contents( SF_PATH.$misc.DS.'admin'.DS.'jquery'.DS.'jquery.filemanager.js' );
        $adminJs[] = file_get_contents( SF_PATH.$misc.DS.'admin'.DS.'jquery'.DS.'jquery.realias.js' );
        $adminJs[] = file_get_contents( SF_PATH.$misc.DS.'admin'.DS.'admin.js' );
        file_put_contents( ROOT.DS.'_runtime'.DS.'admin.js', join("\n\n", $adminJs) );

        $this->_app->addScript( '/_runtime/admin.js' );

        $this->_app->addStyle( $this->getMisc() . '/bootstrap/css/bootstrap.css' );
        $this->_app->addScript( $this->getMisc() . '/bootstrap/js/bootstrap.js' );


        $layout = $this->getTpl()->fetch(
            $this->getRequest()->get( 'resource' )
            . $this->getRequest()->get( 'template' )
        );
        return $layout;
    }
}
