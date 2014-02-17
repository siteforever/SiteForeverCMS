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
    const JQ_UI_THEME = 'flick';

    /**
     * @inheritdoc
     */
    public function view(KernelEvent $event)
    {
        /** @var AssetManager $am */
        $am = $this->_app->getContainer()->get('asset.manager');
        /** @var AssetWriter $writer */
        $writer = $this->_app->getContainer()->get('asset.writer');


        $this->_app->addStyle($this->getMisc().'/jquery/'.self::JQ_UI_THEME.'/jquery-ui.min.css');
//        $this->_app->addScript( $this->getMisc().'/jquery/jquery-ui-'.self::JQ_UI_VERSION.'.custom.min.js' );

        $request = $event->getRequest();
        $this->init($request);

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
            $this->getTpl()->fetch($request->getTemplate())
        );

        return $event;
    }
}
