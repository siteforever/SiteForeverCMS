<?php
/**
 * Комментарий для каталога
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

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

    $productModel = Model::getModel('Catalog');
    $commentModel = Model::getModel('CatalogComment');

    $form = new CommentForm();

    if ($form->getPost()) {
        if ($form->validate()) {
            $comment = $commentModel->createObject($form->getData());
            $comment->ip = $smarty->tpl_vars['request']->value->getClientIp();
            $comment->createdAt = new DateTime();
            $comment->updatedAt = $comment->createdAt;
            $comment->save();
            $smarty->assign('ok', true);
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
