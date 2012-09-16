<?php
/**
 * Интерфейс Робокассы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

use Sfcms\Robokassa;

class Controller_Robokassa extends Sfcms_Controller
{
    public function init()
    {
        $this->request->setLayout('inner');
        $this->request->setTitle(t('Robokassa'));
        $this->getTpl()->getBreadcrumbs()->addPiece('index',t('Main'))->addPiece(null,t('Robokassa'));
    }

    /**
     * используется в случае успешного проведения платежа
     * @param int $InvId
     * @param float $OutSum
     * @param string $SignatureValue
     * @return string
     */
    public function successAction( $InvId, $OutSum, $SignatureValue )
    {
        /** @var $order Data_Object_Order  */
        $orderModel = $this->getModel('Order');
        $order = $orderModel->find( $InvId );

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
     * используется для оповещения о платеже, если метод отсылки - email, то email-адрес
     * @param float $OutSum
     * @param int $InvId
     * @param string $SignatureValue
     * @return string
     */
    public function resultAction( $OutSum, $InvId, $SignatureValue )
    {
        $this->request->setAjax(true);

        /** @var $order Data_Object_Order  */
        $orderModel = $this->getModel('Order');
        $order = $orderModel->find( $InvId );

        $positions = $order->Positions;
        $sum = $positions->sum('sum') + $order->Delivery->cost;

        if ( $sum != $OutSum ) {
            return 'Sum`s is not corresponded';
        }

        $robokassa = new Robokassa( $this->config->get('service.robokassa') );
        $robokassa->setInvId( $InvId );
        $robokassa->setOutSum( $OutSum );

        if ( $robokassa->isValidResult( $SignatureValue ) ) {
            $order->paid = 1;
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
     * используется в случае отказа проведения платежа
     * @param int $InvId
     * @param float $OutSum
     * @return array|string
     */
    public function failAction( $InvId, $OutSum )
    {
        $roboConfig = $this->config->get('service.robokassa');
        /** @var $order Data_Object_Order  */
        $orderModel = $this->getModel('Order');
        $order = $orderModel->find( $InvId );


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
