<?php
/**
 * Формируем лэйаут для админки
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View\Layout;

use Sfcms\View\Layout;
use Sfcms\Kernel\KernelEvent;

class Admin extends Layout
{
    /**
     * @param KernelEvent $event
     * @return KernelEvent
     */
    public function view(KernelEvent $event)
    {
        $this->init();
        // подключение админских стилей и скриптов
        $this->attachJUI();
        $this->_app->addStyle( $this->getMisc() . '/admin/admin.css' );
        // jQuery
        $this->_app->addScript( $this->getMisc() . '/jquery/jquery.form.js' );
        $this->_app->addScript( $this->getMisc() . '/jquery/jquery.blockUI.js' );

//        $this->attachWysiwyg();

        $this->_app->addStyle( $this->getMisc() . '/elfinder/css/elfinder.css' );
        $this->_app->addScript( $this->getMisc() . '/elfinder/js/elfinder.full.js' );
        $this->_app->addScript( $this->getMisc() . '/elfinder/js/i18n/elfinder.ru.js' );

        $this->_app->addScript( $this->getMisc() . '/etc/modal.js' );

        $this->_app->addScript( $this->getMisc() . '/admin/catalog.js' );

        // Собираем админский скрипт из библиотек
        // todo Разгресли эту хуйню со скриптами
        $adminJs = array('// DO NOT MODIFY THIS FILE. IS FILE WAS GENERATED. DATE '.date('d M Y, H:i'));
        $adminJs[] = file_get_contents( SF_PATH.$this->getMisc().'/admin/forms.js' );
        $adminJs[] = file_get_contents( SF_PATH.$this->getMisc().'/admin/jquery/jquery.dumper.js' );
        $adminJs[] = file_get_contents( SF_PATH.$this->getMisc().'/admin/jquery/jquery.filemanager.js' );
        $adminJs[] = file_get_contents( SF_PATH.$this->getMisc().'/admin/jquery/jquery.realias.js' );
        $adminJs[] = file_get_contents( SF_PATH.$this->getMisc().'/admin/admin.js' );
        file_put_contents( ROOT.'/static/admin.js', join("\n\n", $adminJs) );

        $this->_app->addScript('/static/admin.js');

        $this->_app->addStyle( $this->getMisc() . '/bootstrap/css/bootstrap.css' );
        $this->_app->addScript( $this->getMisc() . '/bootstrap/js/bootstrap.js' );

        $this->getTpl()->assign('response', $event->getResponse());
        $event->getResponse()->setContent($this->getTpl()->fetch(
            $this->getRequest()->get('resource') . $this->getRequest()->get('template')
        ));
        return $event;
    }
}
