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
        $request = $event->getRequest();
        $this->init($request);
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
        $targetFile = ROOT.'/static/admin.js';
        if (! $exists = file_exists($targetFile) || \App::isDebug()) {
            if ($exists) {
                $lastModTarget = filemtime($targetFile);
                $files = array(
                    '/admin/forms.js',
                    '/admin/jquery/jquery.dumper.js',
                    '/admin/jquery/jquery.filemanager.js',
                    '/admin/jquery/jquery.realias.js',
                    '/admin/admin.js',
                );
                $misc = $this->getMisc();
                $lastModSorce = array_reduce($files, function($modify, $file) use ($misc) {
                        $l = filemtime(SF_PATH.$misc.$file);
                        return $l > $modify ? $l : $modify;
                    }, 0);
            }
            if (!$exists || $lastModSorce > $lastModTarget) {
                // todo Разгресли эту хуйню со скриптами
                $adminJs = array('// DO NOT MODIFY THIS FILE. IS FILE WAS GENERATED. DATE '.date('d M Y, H:i'));
                foreach ($files as $file) {
                    $adminJs[] = file_get_contents(SF_PATH.$misc.$file);
                }
                file_put_contents( $targetFile, join("\n\n", $adminJs) );
            }
        }

        $this->_app->addScript('/static/admin.js');

        $this->_app->addStyle( $this->getMisc() . '/bootstrap/css/bootstrap.css' );
        $this->_app->addScript( $this->getMisc() . '/bootstrap/js/bootstrap.js' );

        $this->getTpl()->assign('response', $event->getResponse());
        $event->getResponse()->setContent($this->getTpl()->fetch(
            $request->get('resource') . $request->getTemplate()
        ));
        return $event;
    }
}
