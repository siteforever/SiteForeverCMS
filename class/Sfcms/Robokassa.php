<?php
/**
 * Класс работы с Робокассой
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

class Robokassa
{
    private $MrchLogin;

    private $MerchantPass1;

    private $MerchantPass2;

    private $OutSum;

    private $InvId;

    private $Desc = 'Robokassa+payment!';

    private $Culture = 'ru';

    private $IncCurrLabel = 'BANKOCEANMR';

    private $Encoding = 'utf-8';

    private $server = 'https://merchant.roboxchange.com/Index.aspx';

    private $serverTest = 'http://test.robokassa.ru';


    /**
     * @param array $config
     * @throws \RuntimeException
     */
    public function __construct( $config = array() )
    {
        $require = array('MrchLogin', 'MerchantPass1', 'MerchantPass2');
        foreach ( $require as $param ) {
            if ( ! isset( $config[$param] ) )
                throw new \RuntimeException('Undefined required param: '.$param);
        }

        $this->setMrchLogin( $config['MrchLogin'] );
        $this->setMerchantPass1( $config['MerchantPass1'] );
        $this->setMerchantPass2( $config['MerchantPass2'] );
    }


    public function setCulture( $Culture )
    {
        $this->Culture = $Culture;
    }

    public function getCulture()
    {
        return $this->Culture;
    }

    public function setDesc( $Desc )
    {
        $this->Desc = $Desc;
    }

    public function getDesc()
    {
        return $this->Desc;
    }

    public function setEncoding( $Encoding )
    {
        $this->Encoding = $Encoding;
    }

    public function getEncoding()
    {
        return $this->Encoding;
    }

    public function setIncCurrLabel( $IncCurrLabel )
    {
        $this->IncCurrLabel = $IncCurrLabel;
    }

    public function getIncCurrLabel()
    {
        return $this->IncCurrLabel;
    }

    public function setMerchantPass1( $MerchantPass1 )
    {
        $this->MerchantPass1 = $MerchantPass1;
    }

    public function getMerchantPass1()
    {
        return $this->MerchantPass1;
    }

    public function setMerchantPass2( $MerchantPass2 )
    {
        $this->MerchantPass2 = $MerchantPass2;
    }

    public function getMerchantPass2()
    {
        return $this->MerchantPass2;
    }

    public function setMrchLogin( $MrchLogin )
    {
        $this->MrchLogin = $MrchLogin;
    }

    public function getMrchLogin()
    {
        return $this->MrchLogin;
    }

    public function setOutSum( $OutSum )
    {
        $this->OutSum = $OutSum;
    }

    public function getOutSum()
    {
        return number_format( round( $this->OutSum, 2 ), 2, '.', '' );
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getSignatureValue()
    {
        if ( ! ( $this->MrchLogin && $this->OutSum && $this->InvId && $this->MerchantPass1 ) ) {
            throw new \RuntimeException('Undefined required entities');
        }
        return md5("{$this->getMrchLogin()}:{$this->getOutSum()}:{$this->getInvId()}:{$this->getMerchantPass1()}");
    }

    public function setInvId( $invId )
    {
        if ( ! filter_var( $invId, FILTER_VALIDATE_INT ) ) {
            throw new \RuntimeException('InvId is not INT');
        }
        $this->InvId = $invId;
    }

    public function getInvId()
    {
        return $this->InvId;
    }

    /**
     * Payment link
     * @param bool $test
     * @return string
     */
    public function getLink( $test = false )
    {
        return $this->getServer($test)
            . "?MrchLogin={$this->getMrchLogin()}"
            . "&OutSum={$this->getOutSum()}"
            . "&InvId={$this->getInvId()}"
            . "&IncCurLabel={$this->getIncCurrLabel()}"
            . "&Desc={$this->getDesc()}"
            . "&SignatureValue={$this->getSignatureValue()}"
            . "&Encoding={$this->getEncoding()}";
    }

    /**
     * @param bool $test
     * @return string
     */
    private function getServer( $test )
    {
        return $test ? $this->serverTest : $this->server;
    }

    /**
     * Validate payment by signature
     * @param $signature
     * @return bool
     */
    public function isValidResult( $signature )
    {
        $signature = strtolower( $signature );
        $currentSignature = strtolower( md5("{$this->getOutSum()}:{$this->getInvId()}:{$this->getMerchantPass2()}") );
        return $signature == $currentSignature;
    }

    /**
     * Validate payment by signature
     * @param $signature
     * @return bool
     */
    public function isValidSuccess( $signature )
    {
        $signature = strtolower( $signature );
        $currentSignature = strtolower( md5("{$this->getOutSum()}:{$this->getInvId()}:{$this->getMerchantPass1()}") );
        return $signature == $currentSignature;
    }

}
