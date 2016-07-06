<?php
/**
 * Комментарий для каталога
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

use Module\Catalog\Model\CommentModel;
use Sfcms\Model;
use Module\Catalog\Form\CommentForm;
use Module\Catalog\Object\Catalog;

function smarty_function_comment($params, Smarty_Internal_Template $smarty)
{
    if (!isset($params['product'])) {
        throw new InvalidArgumentException('Not defined `product` parameter');
    }
    $product = $params['product'];

    if (!$product instanceof Catalog) {
        throw new InvalidArgumentException('`product` must be Module\Catalog\Object\Catalog instance');
    }

    /** @var CommentModel $commentModel */
    $commentModel = App::cms()->getContainer()->get('data.manager')->getModel('CatalogComment');

    $form = new CommentForm([], App::cms()->get('i18n'));

    /** @var \Sfcms\Request $request */
    $request = $smarty->tpl_vars['request']->value;
    if ($form->handleRequest($request)) {
        if ($form->validate()) {
            $comment = $commentModel->createObject($form->getData());
            $comment->ip = $smarty->tpl_vars['request']->value->getClientIp();
            $comment->createdAt = new DateTime();
            $comment->updatedAt = $comment->createdAt;
            $comment->save();
            $form->getChild('content')->setValue('');
            $smarty->assign('ok', true);
            $request->getSession()->getFlashBag()->add('success', 'Комментарий добавлен успешно');
        }
    }

    $comments = $product->Comments;
    $form->product_id = $product->id;
    $smarty->assign(array(
        'comments' => $comments,
        'form'     => $form,
    ));

    return $smarty->fetch('catalog/function_comment.tpl');
}
