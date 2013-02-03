<?php
/**
 * Интерфейс Робокассы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Module\Market\Controller;

use Sfcms_Controller;
use Sfcms\Robokassa;
use Module\Market\Object\Order;

class RobokassaController extends Sfcms_Controller
{
    public function init()
    {
        $this->request->setLayout('inner');
        $this->request->setTitle(t('Robokassa'));
        $this->getTpl()->getBreadcrumbs()->addPiece('index',t('Main'))->addPiece(null,t('Robokassa'));
    }

    /**
     * Используется для перехода в случае успешного проведения платежа
     * @param int $InvId
     * @param float $OutSum
     * @param string $SignatureValue
     * @return string
     */
    public function successAction( $InvId, $OutSum, $SignatureValue )
    {
        if ( ! $InvId || ! $OutSum || ! $SignatureValue ) {
            return 'Params not defined';
        }
        /** @var $order Order  */
        $orderModel = $this->getModel('Order');
        $order = $orderModel->find( $InvId );

        if ( ! $order ) {
            throw new \Sfcms_Http_Exception('Order not found', 404);
        }

        $positions = $order->Positions;
        $sum = $positions->sum('sum') + $order->Delivery->cost;

        if ( $sum != $OutSum ) {
            return 'Sum`s is not corresponded!';
        }

        $robokassa = new Robokassa( $this->config->get('service.robokassa') );
        $robokassa->setInvId( $InvId );
        $robokassa->setOutSum( $OutSum );

        if ( ! $robokassa->isValidSuccess( $SignatureValue ) ) {
            return 'Payment error: Signature is not valid. '.$SignatureValue . ' <> '
                .strtolower( md5("{$robokassa->getOutSum()}:{$robokassa->getInvId()}:{$robokassa->getMerchantPass2()}") );
        }

        return array(
            'order' => $order,
        );
    }

    /**
     * Используется для оповещения сайта о платеже, если метод отсылки - email, то email-адрес
     * @param float $OutSum
     * @param int $InvId
     * @param string $SignatureValue
     * @return string
     */
    public function resultAction( $OutSum, $InvId, $SignatureValue )
    {
        if ( ! $InvId || ! $OutSum || ! $SignatureValue ) {
            return 'Params not defined';
        }
        $this->request->setAjax(true);

        /** @var $order Order  */
        $orderModel = $this->getModel('Order');
        $order = $orderModel->find( $InvId );

        if ( ! $order ) {
            throw new \Sfcms_Http_Exception('Order not found', 404);
        }

        $positions = $order->Positions;
        $sum = $positions->sum('sum') + $order->Delivery->cost;

        if ( $sum != $OutSum ) {
            return 'Sum`s is not corresponded';
        }

        $robokassa = new Robokassa( $this->config->get('service.robokassa') );
        $robokassa->setInvId( $InvId );
        $robokassa->setOutSum( $OutSum );

        if ( $robokassa->isValidResult( $SignatureValue ) ) {
            $order->paid = time();
            $this->getTpl()->assign('order',$order);
            sendmail(
                "{$order->fname} {$order->lname} <{$order->email}>",
                $this->config->get('admin'),
                'Оплачен заказ №'.$order->id,
                $this->getTpl()->fetch('robokassa.email.result')
            );
            return "OK$InvId\n";
        }
        return "bad sign\n";
    }

    /**
     * Используется для перехода в случае отказа проведения платежа
     * @param int $InvId
     * @param float $OutSum
     * @return array|string
     */
    public function failAction( $InvId, $OutSum )
    {
        if ( ! $InvId || ! $OutSum ) {
            return 'Params not defined';
        }
        $roboConfig = $this->config->get('service.robokassa');
        /** @var $order Order  */
        $orderModel = $this->getModel('Order');
        $order = $orderModel->find( $InvId );

        if ( ! $order ) {
            throw new \Sfcms_Http_Exception('Order not found', 404);
        }

        $positions = $order->Positions;
        $sum = $positions->sum('sum') + $order->Delivery->cost;

        if ( $sum != $OutSum ) {
            return 'Sum`s is not corresponded';
        }

        $robokassa = new Robokassa( $roboConfig );
        $robokassa->setInvId( $InvId );
        $robokassa->setOutSum( $OutSum );

        $this->getTpl()->assign('order',$order);
        sendmail(
            "{$order->fname} {$order->lname} <{$order->email}>",
            $this->config->get('admin'),
            'Сбой оплаты заказ №'.$order->id,
            $this->getTpl()->fetch('robokassa.email.fail')
        );

        return array(
            'order' => $order,
            'link'  => $robokassa->getLink( true ),
            'adminMail' => $this->config->get('admin'),
        );
    }
}
