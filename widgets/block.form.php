<?php
/**
 * Блок для создания формы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
 
function smarty_block_form( $params, $content, $smarty )
{
    $app    = App::getInstance();

    if ( null !== $content ) { // Закрытие
        if ( isset( $params['form'] ) ) {
            /** @var Form_Form $form */
            $form   = new $params['form']();
            return $form->htmlStart().$content.$form->htmlEnd();
        }

        $method = isset( $params['method'] ) ? $params['method'] : 'GET';
        $action = $app->getRouter()->createLink( isset( $params['action'] ) ? $params['action'] : null );

        return "<form action='{$action}' method='{$method}'>{$content}</form>";
    }
}
