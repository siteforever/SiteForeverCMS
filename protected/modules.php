<?php
/**
 * Controllers list
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

/**
 * Массив контроллеров
 *
 * В качестве ключей идут названия модулей. В качестве значений - массив с контроллерами в данном модуле.
 *
 * В качестве значений контроллеров - массив, который может содержать индивидуальные для контроллера данные.
 * Например, файл, в котором находится класс контроллера, название класса контроллера.
 *
 * Благодаря настройкам этого массива можно иерархично делить контроллеры по модулям, разбивать их на группы.
 *
 * Если для контроллер находится в модуле System, то он должен находится в папке controller, доступной для
 * операций include и request. Имя файла должно совпадать с именем контроллера и быть в нижнем регистре.
 *
 * Если указан ключ file, то Resolver попытается подключить этот файл, в надежде, что класс контроллера находится
 * в нем. Если его в этом файле не будет, то к классу будет применен стандартный автозагрузчик.
 * Этот прем можно использовать также, если для контроллера нужно подключить какой-то дополнительный файл.
 * Однако, такой необходимости еще не было.
 *
 * Если указан ключ class, то Resolver установит этот класс в качестве контроллера. Далее класс будет
 * преобразован по правилам PSR-0, тем добавлением, что имя пути и файла будут в нижнем регистре (Это важно
 * для использования на *nix серверах)
 *
 * Наконец, если в настройках указан ключ module, то класс будет запрошен в пространстве имен этого модуля,
 * которое определяется автоматически как:
 * - Если не указан ключ class: \Module\<ModuleName>\Controller\<Controller\Name>Controller
 * - Если указан ключ class: \Module\<ModuleName>\<Class\Name>Controller
 * Если в имени класса контроллера присутствует знак подчеркивания "_", то он будет заменен на знак пространства имен.
 *
 * В следствии устранения путаницы, не рекомендуется использовать директивы class и module совместно, но возможно.
 */
return array(
    'System' => array(
        'captcha'   => array(),
        'elfinder'  => array(),
        'error'     => array(),
        'feedback'  => array(),
        'generator' => array(),
        'routes'    => array(),
        'search'    => array(),
        'sitemap'   => array(),
        'system'    => array(),
        'users'     => array(),
        'setting'   => array(),
    ),

    'Banner' => array(
        'banner'    => array(),
    ),

    'Market'    => array(
        'Basket'    => array(),
        'Delivery'  => array(),
        'Manufacturers' => array( 'class'  => 'Controller\Manufacturer', ),
        'Material'  => array(),
        'Order'     => array(),
        'OrderAdmin'=> array(),
        'Orderpdf'  => array(),
        'Xmlprice'  => array(),
        'Payment'   => array(),
        'Producttype' => array(),
        'Robokassa' => array(),
    ),

    'Catalog'   => array(
        'Catalog'   => array(),
        'Cataloggallery' => array( 'class' => 'Controller\Gallery', ),
        'Goods'     => array(),
        'settings'  => array(
            'order_default' => array('type'=>'text','value'=>'name',),
            'order_list'    => array('type'=>'select','value'=>'list',
                                    'options' => array(
                                        ''          => 'Без сортировки',
                                        'name'      => 'По наименованию',
                                        'price1'    => 'По цене (0->макс)',
                                        'price1-d'  => 'По цене (макс->0)',
                                        'articul'   => 'По артикулу',
                                    ),
                                ),
            'onPage'        => 10,
            'level'         => 0,
        ),
    ),

    'Gallery'   => array(
        'Gallery'   => array(),
    ),

    'Guestbook' => array(
        'Guestbook' => array(),
    ),

    'Page' => array(
        'Page'  => array(),
    ),

    'News' => array(
        'News'  => array(),
        'Rss'   => array(),
    ),

    'Editor'    => array(
        'settings'  => array(
            'type' => array(
                'type'      => 'select',
                'value' => 'ckeditor',
                'options'   => array(
                    'tinymce'   => 'Tiny MCE',
                    'ckeditor'  => 'CKEditor',
                ),
            ),
       ),
    ),
);