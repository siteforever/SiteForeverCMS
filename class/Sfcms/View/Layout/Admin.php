<?php
/**
 * Формируем лэйаут для админки
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View\Layout;

use Assetic\Asset\FileAsset;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Sfcms\View\Layout;
use Sfcms\Kernel\KernelEvent;

class Admin extends Layout
{
    /**
     * @inheritdoc
     */
    public function view(KernelEvent $event)
    {
        /** @var AssetManager $am */
        $am = $this->_app->getContainer()->get('assetManager');
        /** @var AssetWriter $writer */
        $writer = $this->_app->getContainer()->get('assetWriter');

        $request = $event->getRequest();
        $this->init($request);
        // подключение админских стилей и скриптов
        $this->attachJUI();
        // jQuery

//        $this->attachWysiwyg();

        $this->_app->getAssets()->addStyle('/static/admin/jquery/elfinder/elfinder.css');
        $this->_app->getAssets()->addScript('/static/admin.js');
        $this->_app->getAssets()->addStyle($this->getMisc() . '/bootstrap/css/bootstrap.css');
        $this->_app->getAssets()->addStyle('/static/admin/jquery/jqgrid/ui.jqgrid.css');
        $am->set('admIcons', new FileAsset(realpath(__DIR__ . '/../../../Module/System/Static/icons.css')));
        $am->get('admIcons')->setTargetPath('admIcons.css');
        $this->_app->getAssets()->addStyle('/static/admIcons.css');
        $this->_app->getAssets()->addStyle('/misc/bootstrap/css/bootstrap-datetimepicker.min.css');
        $this->_app->getAssets()->addStyle($this->getMisc() . '/admin/admin.css');

        $this->getTpl()->assign('response', $event->getResponse());
        $event->getResponse()->setContent(
            $this->getTpl()->fetch(
                $request->get('resource') . $request->getTemplate()
            )
        );

        return $event;
    }
}
