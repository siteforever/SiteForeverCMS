<?php
/**
 * Контроллер поиска
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class controller_Search extends Controller
{

    function indexAction()
    {
        $search = filter_var($this->request->get('sword'), FILTER_SANITIZE_STRING);

        $search = preg_replace('/\&\w+?\;/ui', '%', $search); // спецсимволы
        $search = preg_replace('/[^\wа-я]+/ui', '%', $search);// все, что не буквы и цифры
        $search = preg_replace('/%+/u', '%', $search); // повторяющиеся
        $search = str_replace(array('%34', '%39'), '', $search); // хвосты от кавычек
        $search = '%'.preg_replace('/^%+|%+$/u', '', $search).'%'; // формируем запрос
        $search  = trim( $search, ' ' );

        $search_list = App::$db->fetchAll(
            "SELECT *
            FROM ".DBCATALOG."
            WHERE
                ( name LIKE '{$search}'
                OR  articul LIKE '{$search}' )
                AND cat = 0 AND hidden = 0 AND deleted = 0 AND protected = 0
            ORDER BY id DESC
            LIMIT 50");

        $_GET['sword'] = trim( str_replace('%', ' ', $search) );

        $this->tpl->assign(array(
            'sword'   => $_GET['sword'],
            'list'    => $search_list,
        ));
        $this->request->setContent( $this->tpl->fetch('system:search.index') );

        $this->request->setTitle('Поиск');
        $this->request->set('template', 'inner');
    }

}