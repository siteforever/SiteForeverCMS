<?php
/**
 * Контроллер поиска
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Controller_Search extends Controller
{

    function indexAction()
    {
        $search = filter_var($this->request->get('query'));

        $search = urldecode( $search );

        $_GET['sword'] = trim( str_replace('%', ' ', $search) );


        $search = preg_replace('/\&\w+?\;/ui', '%', $search); // спецсимволы
        $search = preg_replace('/[^\wа-я]+/ui', '%', $search);// все, что не буквы и цифры
        $search = preg_replace('/[аийеёоуыъьэюя]+/ui', '%', $search);// все гласные
        $search = preg_replace('/%+/u', '%', $search); // повторяющиеся
        $search = str_replace(array('%34', '%39'), '', $search); // хвосты от кавычек
        $search = '%'.preg_replace('/^%+|%+$/u', '', $search).'%'; // формируем запрос
        $search  = trim( $search, ' ' );

        $protected  = $this->app()->getAuth()->currentUser()->perm;
        $search_list    = Model::getModel('Page')->findAll(
            array(
                'condition' => ' ( name LIKE :search OR title LIKE :search OR notice LIKE :search OR content LIKE :search ) '
                .' AND hidden = 0 AND protected <= :perm AND system = 0 AND deleted = 0 ',
                'params'    => array( ':search' => $search, ':perm' => $protected ),
                'limit'     => 50,
            )
        );

        $this->tpl->assign(array(
            'sword'   => $_GET['sword'],
            'list'    => $search_list,
        ));
        $this->request->setContent( $this->tpl->fetch('search.index') );

        $this->request->setTitle('Поиск');
        $this->request->set('template', 'inner');
    }

}