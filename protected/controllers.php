<?php
/**
 * Controllers list
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

/**
 * Массив контроллеров
 *
 * В качестве ключей идут сами контроллеры. В качестве значений - массив, который может содержать
 * индивидуальные для контроллера данные. Например, файл, в котором находится класс контроллера,
 * название класса контроллера, модуль, которому принадлежит контроллер.
 *
 * Благодаря настройкам этого массива можно иерархично делить контроллеры по модулям, разбивать их на группы.
 *
 * Необходимость плясать от контроллеров, а не от модулей, продиктована необходимостью поддержки строй версии
 * формата базы данных.
 *
 * Если для контроллера не указан никакой конфиг, то он должен находится в папке controller, доступной для
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
    'captcha'   => array(),
    'elfinder'  => array(),
    'error'     => array(),
    'feedback'  => array(),
    'generator' => array(),
    'routes'    => array(),
    'search'    => array(),
    'settings'  => array(),
    'sitemap'   => array(),
    'system'    => array(),
    'users'     => array(),

    'banner' => array(
        'module' => 'Banner',
    ),

    'basket' => array(
        'module' => 'Market',
    ),
    'delivery' => array(
        'module' => 'Market',
    ),
    'manufacturers' => array(
        'class'  => 'Controller\Manufacturer',
        'module' => 'Market',
    ),
    'order' => array(
        'module' => 'Market',
    ),
    'orderpdf' => array(
        'module' => 'Market',
    ),
    'xmlprice' => array(
        'module' => 'Market',
    ),

    'payment' => array(
        'module' => 'Market',
    ),
    'producttype' => array(
        'module' => 'Market',
    ),
    'robokassa' => array(
        'module' => 'Market',
    ),

    'catalog' => array(
        'module' => 'Catalog',
    ),
    'cataloggallery' => array(
        'class'  => 'Controller\Gallery',
        'module' => 'Catalog',
    ),
    'goods' => array(
        'module' => 'Catalog',
    ),

    'gallery' => array(
        'module' => 'Gallery',
    ),

    'guestbook' => array(
        'module' => 'Guestbook',
    ),

    'page' => array(
        'module' => 'Page',
    ),

    'news' => array(
        'module' => 'News',
    ),
    'rss' => array(
        'module' => 'News',
    ),
);