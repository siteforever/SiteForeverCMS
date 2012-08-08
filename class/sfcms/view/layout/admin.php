<?php
/**
 * Формируем лэйаут для админки
 * @author: keltanas <keltanas@gmail.com>
 */
class Sfcms_View_Layout_Admin extends Sfcms_View_Layout
{
    const JQ_UI_THEME = 'redmond';
    const JQ_UI_VERSION = '1.8.21';

    /**
     * @param $result
     * @return string
     */
    public function view( $result )
    {
        $misc = $this->getMisc();
        // подключение админских стилей и скриптов
        $this->_app->addStyle( $misc.'/jquery/'.self::JQ_UI_THEME.'/jquery-ui-'.self::JQ_UI_VERSION.'.custom.css' );
        $this->_app->addScript( $misc.'/jquery/jquery-ui-'.self::JQ_UI_VERSION.'.custom.min.js' );

        $this->_app->addStyle( $misc . '/admin/admin.css' );
        // jQuery
        $this->_app->addScript( $misc . '/jquery/jquery.form.js' );
        $this->_app->addScript( $misc . '/jquery/jquery.blockUI.js' );

        switch ( strtolower( $this->getSettings()->get( 'editor', 'type' ) ) ) {
            case 'tinymce':
                // TinyMCE
                $this->_app->addScript( $misc . '/tiny_mce/jquery.tinymce.js' );
                $this->_app->addScript( $misc . '/admin/editor/tinymce.js' );
                break;

            case 'ckeditor':
                // CKEditor
                $this->_app->addScript( $misc . '/ckeditor/ckeditor.js' );
                $this->_app->addScript( $misc . '/ckeditor/adapters/jquery.js' );
                $this->_app->addScript( $misc . '/admin/editor/ckeditor.js' );
                break;

            default: // plain
        }

        $this->_app->addStyle( $misc . '/elfinder/css/elfinder.css' );
        $this->_app->addScript( $misc . '/elfinder/js/elfinder.full.js' );
        $this->_app->addScript( $misc . '/elfinder/js/i18n/elfinder.ru.js' );

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

        $layout = $this->getTpl()->fetch(
            $this->getRequest()->get( 'resource' )
            . $this->getRequest()->get( 'template' )
        );
        return $layout;
    }
}
