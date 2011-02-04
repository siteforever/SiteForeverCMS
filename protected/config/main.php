<?php
/**
 * Конфиг для системы
 */
return array(

    // отладка
    'debug' => array(
        'profile'   => true,
    ),

    'logger'    => 'html',

    'sitename'  => 'SiteForeverCMS',
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],
    'admin'     => 'admin@ermin.ru',

    // база данных
    'db' => array(
        'login'     => 'siteforever',
        'password'  => 'siteforever',
        'host'      => 'localhost',
        'database'  => 'siteforever',
    ),
    // тема
    'template' => array(
        'theme'     => 'basic',
        // драйвер шаблонизатора
        // это класс, поддерживающий интерфейс TPL_Driver
        'driver'    => 'TPL_Smarty',
        'widgets'   => SF_PATH.DS.'widgets',
        'ext'       => 'tpl', // расширение шаблонов
        'admin'     => SF_PATH.DS.'themes'.DS.'system', // каталог шаблонов админки
        '404'       => 'error404', // шаблон страницы 404
    ),

    // настройки пользователей
    'users' => array(
        'groups' => array(
            USER_GUEST  => 'Гость',
            USER_USER   => 'Пользователь',
            USER_WHOLE  => 'Оптовый покупатель',
            USER_ADMIN  => 'Админ',
        ),
        'userdir' => DS.'files',
    ),

    'catalog' => array(
        'gallery_dir' => DS.'files'.DS.'catalog'.DS.'gallery',
        'gallery_max_file_size' => 1000000,
        'gallery_thumb_prefix'  => 'thumb_',
        'gallery_thumb_h'   => 100,
        'gallery_thumb_w'   => 100,
        'gallery_thumb_prefix'  => 'middle_',
        'gallery_thumb_h'   => 200,
        'gallery_thumb_w'   => 200,
        'gallery_thumb_method' => 1,
            // 1 - добавление полей
            // 2 - обрезание лишнего
        // сортировка товаров
        'order_list'    => array(
            ''      => 'Без сортировки',
            'name'  => 'По наименованию',
            'price1'=> 'По цене',
            'articul'=>'По артикулу',
        ),
    ),

    // Реквизиты фирмы
    'firm'  => array(
        'name'      => 'Наименование фирмы',
        'inn'       => 'номер инн',
        'kpp'       => 'номер кпп',
        'nch'       => 'номер счета',
        'address'   => 'адрес фирмы',
        'phone'     => '(812) 555-55-55',
        'fax'       => '(812) 555-55-55',
        'contact'   => 'Юрий Пронин',
        'buh'       => 'Снежана Денисовна',
        'gendir'    => 'Галина Георгиевна',
        'bank'  => array(
            'name'      => 'Наименование банка',
            'bik'       => 'номер бик',
            'ks'        => 'номер кор.счета',
        ),
    ),

    'files' => array(
        'include_types' => array( // разрешенные типы файлов
            'application/x-tgz',
            'application/zip',
            'application/x-rar-compressed',
            'image/jpeg',
            'image/png',
            'image/gif',
        ),
        'max_size'      => 11000000, // максимальный размер файла
        // вид для менеджера файлов
        'manager_view'  => 'table', // table || tile
    ),
);