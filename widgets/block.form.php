<?php
/**
 * Блок для создания формы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
function smarty_block_form( $params, $content, $smarty )
{
    $app    = App::getInstance();

    if ( is_null( $content ) ) { // Открытие

    }
    else { // Закрытие

        if ( isset( $params['byclass'] ) ) {
            /**
             * @var Form_Form $form
             */
            $form   = new $params['byclass'];
            return $form->html();
        }

        $method = isset( $params['method'] ) ? $params['method'] : 'GET';
        $action = $app->getRouter()->createLink( isset( $params['action'] ) ? $params['action'] : '' );

        return "<form action='{$action}' method='{$method}'>{$content}</form>";

    }
}
