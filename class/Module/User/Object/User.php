<?php
/**
 * Объект пользователя
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
namespace Module\User\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;
use App;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Class User
 * @package Module\User\Object
 *
 * @property $id
 * @property $login
 * @property $password
 * @property $solt
 * @property $name
 * @property $fname
 * @property $lname
 * @property $email
 * @property $phone
 * @property $basket
 * @property $last
 * @property $address
 */
class User extends Object
{
    /**
     * Поменять пароль пользователя
     * @param string $password
     * @return User
     */
    public function changePassword( $password )
    {
        $solt = $this->generateString(8);
        $hash = $this->generatePasswordHash($password, $solt);
        $this->set('solt', $solt);
        $this->set('password', $hash);
        return $this;
    }

    /**
     * Генерирует случайную строку
     * @param string $len Length generated string
     * @return string
     */
    public function generateString($len)
    {
        $generator = new SecureRandom();
        return substr(bin2hex($generator->nextBytes($len)), rand(0, $len), $len);
    }

    /**
     * Генерирует хэш пароля
     * @param $password
     * @param $solt
     * @return string
     */
    public function generatePasswordHash( $password, $solt )
    {
        return md5( md5($solt) . md5($password) );
    }


    /**
     * Активировать пользователя
     * @return void
     */
    public function active()
    {
        $this->set('status', 1);
    }

    /**
     * Деактивировать пользователя
     * @return void
     */
    public function deactive()
    {
        $this->set('status', 0);
    }


    /**
     * Возвращает права пользователя
     * @return int
     */
    public function getPermission()
    {
        return $this->data['perm'];
    }

    /**
     * Проверит наличие прав у пользователя
     * @param int $perm
     *
     * @return bool
     */
    public function hasPermission($perm)
    {
        if ($this->data['perm'] >= $perm) {
            return 1;
        }

        return 0;
    }

    /**
     * Проверит, равны ли права значению
     * @param int $perm
     * @return bool
     */
    public function eqPermission( $perm )
    {
        return $this->data['perm'] == $perm ? true : false;
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'users';
    }

    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\IntField('id', 11, true, null, true),
            new Field\VarcharField('login', 50),
            new Field\VarcharField('password', 40),
            new Field\VarcharField('solt', 8),
            new Field\VarcharField('fname', 20),
            new Field\VarcharField('lname', 20),
            new Field\VarcharField('email', 50),
            new Field\VarcharField('name', 250),
            new Field\VarcharField('phone', 50),
            new Field\VarcharField('fax', 50),
            new Field\VarcharField('inn', 20),
            new Field\VarcharField('kpp', 20),
            new Field\TextField('address'),
            new Field\TinyintField('status', 4, true, '0'),
            new Field\IntField('date'),
            new Field\IntField('last'),
            new Field\IntField('perm'),
            new Field\VarcharField('confirm', 32),
            new Field\TextField('basket'),
        );
    }
}
