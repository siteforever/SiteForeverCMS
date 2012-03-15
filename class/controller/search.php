<?php
/**
 * Контроллер поиска
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Controller_Search extends Sfcms_Controller
{
    private $_selected = null;


    function indexAction()
    {
        $search = filter_var($this->request->get('query'));

        $search = urldecode( $search );

        $query  = $search;

        $_GET['sword'] = trim( str_replace('%', ' ', $search) );

        $search = $this->filterSearchQuery( $search );

        $page   = $this->request->get('page', FILTER_VALIDATE_INT, 1);
        $perpage= 10;
        $offset = ( $page-1 ) * $perpage;

        $protected  = $this->app()->getAuth()->currentUser()->perm;
        $this->app()->getLogger()->log( $search );
        $search_list    = Sfcms_Model::getModel('Page')->findAll(
            array(
                'condition' => ' ( name LIKE :search OR title LIKE :search '
                                .' OR notice LIKE :search OR content LIKE :search ) '
                            .' AND hidden = 0 AND protected <= :perm AND system = 0 AND deleted = 0 ',
                'params'    => array( ':search' => $search, ':perm' => $protected ),
                'limit'     => " {$offset}, {$perpage} ",
            )
        );

        $page_list  = array();
        $max_page   = $search_list ? $page + 1 : $page;
        if ( $page > 1 ) {
            $page_list[] = "<a href='/search?query={$query}&page=".($page-1)."'>Пред.</a>";
        }
        for ( $i = 1; $i <= $max_page; $i++ ) {
            if ( $page == $i ) {
                $page_list[] = $i;
            } else {
                $page_list[] = "<a href='/search?query={$query}&page=".($i)."'>$i</a>";
            }
        }
        if ( $page != $max_page ) {
            $page_list[] = "<a href='/search?query={$query}&page=".($page+1)."'>След.</a>";;
        }

        $this->tpl->assign(array(
            'page_list' => 'Страницы: '.implode(' ', $page_list),
            'hl'        => $this->_selected,
            'sword'     => $_GET['sword'],
            'list'      => $search_list,
            'offset'    => $offset,
        ));

        $content    = $this->tpl->fetch('search.index');
        $this->request->setContent( $content );

        $this->request->setTitle('Поиск');
        $this->request->set('template', 'inner');
    }


    /**
     * Отфильтрует символы в строке, установит св-во $_selected
     * @param $search
     * @return mixed|string
     */
    private function filterSearchQuery( $search )
    {
        $search = preg_replace('/\&\w+?\;/ui', '%', $search); // спецсимволы
        $search = preg_replace('/[^a-zA-Zа-яА-Я0-9]+/u', '%', $search);// все, что не буквы и цифры
        $search = str_replace(array('%34', '%39'), '', $search); // хвосты от кавычек
        $this->_selected = explode( '|', preg_replace('/%+/u', '|', $search) );
        $oldsearch  = $search;
        $search = preg_replace('/[аийеёоуыъьэюя]+$/ui', '', $search);// все гласные
        $this->app()->getLogger()->log( mb_strlen( $search ) );
        if ( mb_strlen( $search ) < 3 ) {
            $search = $oldsearch;
        }
        $search = preg_replace('/%+/u', '%', $search); // повторяющиеся
        $search = '%'.preg_replace('/^%+|%+$/u', '', $search).'%'; // формируем запрос
        $search  = trim( $search, ' ' );
        return $search;
    }

}