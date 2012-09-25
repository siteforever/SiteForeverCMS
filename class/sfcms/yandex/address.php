<?php
/**
 * Дешифрует адрес от яндекса
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Sfcms\Yandex;
use \Sfcms\Component;

/**
 * @property $title
 * @property $zip
 * @property $city
 * @property $country
 * @property $street
 * @property $building
 * @property $suite
 * @property $flat
 * @property $entrance
 * @property $floor
 * @property $intercom
 * @property $metro
 * @property $cargolift
 * @property $firstname
 * @property $lastname
 * @property $fathersname
 * @property $phone
 * @property $email
 * @property $comment
 * @property $phone-extra
 *
 * @property $address
 */
class Address extends Component
{
    private $_data = array();

    private $_fields = array();

    public function __construct()
    {
        $this->_fields = array(
//            'title' => 'название адреса',
//            'zip' => '',//Индекс
//            'city' => '',// Город
//            'country' => '', // страна
            'street' => '', //ул.
            'building' => 'д.',
            'suite' => 'к.',
            'flat' => 'кв.',
            'entrance' => 'подъезд',
            'floor' => 'эт.',
            'intercom' => 'домофон',
            'metro' => 'Станция метро',
//            'cargolift' => 'наличие грузового лифта',
//            'firstname' => 'имя',
//            'lastname' => 'фамилия',
//            'fathersname' => 'отчество',
//            'phone' => 'телефон',
//            'phone-extra' => 'дополнительный телефон',
//            'email' => 'электронный адрес для связи',
//            'comment' => 'комментарий к адресу',
        );
    }

    /**
     * Вернет адрес в виде строки
     * @return string
     */
    public function getAddress()
    {
        $result = array();
        foreach ( $this->_fields as $key => $name ) {
            if ( ! empty( $this->_data->$key ) ) {
                $result[] = $name . ' ' . $this->_data->$key;
            }
        }
        return trim( implode(', ', $result) );
    }

    public function setJsonData( $json )
    {
        $this->_data = json_decode( urldecode( $json ) );
    }

    public function __get( $key )
    {
        if ( isset( $this->_data->$key ) ) {
            return $this->_data->$key;
        }
        return null;
    }
}