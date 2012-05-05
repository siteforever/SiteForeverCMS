<?php
/**
 * Формируем лэйаут для админки
 * @author: keltanas <keltanas@gmail.com>
 */
class Sfcms_View_Layout_Admin extends Sfcms_View_Layout
{
    /**
     * @param $result
     * @return string
     */
    public function view( $result )
    {
        // подключение админских стилей и скриптов
        $this->getRequest()->addStyle( $this->getMisc() . '/jquery/smoothness/jquery-ui.css' );
        $this->getRequest()->addStyle( $this->getMisc() . '/admin/admin.css' );
        // jQuery
        $this->getRequest()->addScript( $this->getMisc() . '/jquery/jquery-ui-1.8.18.custom.min.js' );
        $this->getRequest()->addScript( $this->getMisc() . '/jquery/jquery.form.js' );
        $this->getRequest()->addScript( $this->getMisc() . '/jquery/jquery.blockUI.js' );

        switch ( strtolower( $this->getSettings()->get( 'editor', 'type' ) ) ) {
            case 'tinymce':
                // TinyMCE
                $this->getRequest()->addScript( $this->getMisc() . '/tiny_mce/jquery.tinymce.js' );
                $this->getRequest()->addScript( $this->getMisc() . '/admin/editor/tinymce.js' );
                break;

            case 'ckeditor':
                // CKEditor
                $this->getRequest()->addScript( $this->getMisc() . '/ckeditor/ckeditor.js' );
                $this->getRequest()->addScript( $this->getMisc() . '/ckeditor/adapters/jquery.js' );
                $this->getRequest()->addScript( $this->getMisc() . '/admin/editor/ckeditor.js' );
                break;

            default: // plain
        }

        $this->getRequest()->addStyle( $this->getMisc() . '/elfinder/css/elfinder.css' );
        $this->getRequest()->addScript( $this->getMisc() . '/elfinder/js/elfinder.full.js' );
        $this->getRequest()->addScript( $this->getMisc() . '/elfinder/js/i18n/elfinder.ru.js' );

        $this->getRequest()->addScript( $this->getMisc() . '/forms.js' );
        $this->getRequest()->addScript( $this->getMisc() . '/admin/catalog.js' );
        $this->getRequest()->addScript( $this->getMisc() . '/admin/admin.js' );

        $layout = $this->getTpl()->fetch(
            $this->getRequest()->get( 'resource' )
            . $this->getRequest()->get( 'template' )
        );
        return $layout;
    }
}
