<?php
/**
 * Блок для создания формы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */

function smarty_block_form($params, $content, $smarty)
{
    $app = App::getInstance();

    if (null !== $content) { // Закрытие
        if (isset($params['form'])) {
            /** @var \Sfcms\Form\Form $form */
//            $form = new $params['form']();
            $form = $params['form'];

            return $form->htmlStart() . $content . $form->htmlEnd();
        }

        $method = isset($params['method']) ? $params['method'] : 'GET';
        $class  = isset($params['class']) ? $params['class'] : '';
        $action = $app->getRouter()->createLink(isset($params['action']) ? $params['action'] : null);

        if (null !== $content) {
            return "<form action='{$action}' method='{$method}' class='{$class}'>{$content}</form>";
        }
    }
}
