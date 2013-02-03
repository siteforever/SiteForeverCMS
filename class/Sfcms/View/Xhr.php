<?php
/**
 * Отображение для XMLHttpRequest
 * @author: keltanas <keltanas@gmail.com>
 */
namespace Sfcms\View;

use Sfcms\View\IView;
use Exception;
use Sfcms\Request;

class Xhr extends IView
{
    /**
     * @param $result
     * @throws Exception
     * @return string
     */
    public function view( $result  )
    {
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );

        $return = '';

        switch ( $this->getRequest()->getAjaxType() ) {

            case Request::TYPE_JSON:
                header( 'Content-type: text/json; charset=utf-8' );
                if( $result ) {
                    if( is_object( $result ) || is_array( $result ) ) {
                        $result = json_encode( $result );
                    }
                    $return = $result;
                } else {
                    $return = $this->getRequest()->getResponseAsJson();
                }
                break;

            case Request::TYPE_XML:
                header( 'Content-type: text/xml; charset=utf-8' );
                $return = $this->getRequest()->getContent();
                break;

            default:
                if( count( $this->getRequest()->getFeedback() ) ) {
                    $return = '<div class="feedback">' . $this->getRequest()->getFeedbackString() . '</div>';
                }
                if( $this->getRequest()->getContent() ) {
                    $return = $this->getRequest()->getContent();
                }
        }
        return $return;
    }
}