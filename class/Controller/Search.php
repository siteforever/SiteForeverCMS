<?php
use Sfcms\db;

/**
 * Контроллер поиска
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Controller_Search extends Sfcms_Controller
{
    private $_selected = null;

    public function init()
    {
        parent::init();
        $this->request->setTemplate('inner');
    }


    public function access()
    {
        return array(
            'system' => array('admin','indexing'),
        );
    }

    public function indexAction()
    {
        $this->tpl->getBreadcrumbs()->addPiece('index',t('Main'))->addPiece(null,t('Search'));

        $search = filter_var($this->request->get('query'));
        $search = urldecode( $search );
        $this->tpl->assign('query', $search);

        $this->request->setTitle(t('Search').': '.$search);

        if ( strlen( $search ) <= 3 ) {
            return array( 'error' => 'Поисковая фраза слишком короткая' );
        }

        $search = $this->filterFulltext( $search );

        $model = $this->getModel('Search');
        $result = $model->findAll(array(
            'crit' => 'MATCH(`title`,`keywords`,`content`) AGAINST (? IN BOOLEAN MODE)',
            'params' => array($search),
            'limit' => 20,
        ));
//        $result = $model->getDB()->fetchAll(join(" ", array(
//                "SELECT *",
//                "FROM `{$model->getTable()}`",
//                "WHERE ",
//                "LIMIT 20",
//            )),
//            false, db::F_ASSOC, array()
//        );

        return array(
            'search' => $search,
            'result' => $result,
        );
    }


//    function indexAction()
//    {
//        $search = filter_var($this->request->get('query'));
//
//        $search = urldecode( $search );
//
//        $query  = $search;
//
//        $_GET['sword'] = trim( str_replace('%', ' ', $search) );
//
//        $search = $this->filterSearchQuery( $search );
//
//        $page   = $this->request->get('page', FILTER_VALIDATE_INT, 1);
//        $perpage= 10;
//        $offset = ( $page-1 ) * $perpage;
//
//        $protected  = $this->app()->getAuth()->currentUser()->perm;
//        $this->log( $search );
//        $search_list    = Sfcms_Model::getModel('Page')->findAll(
//            array(
//                'condition' => ' ( name LIKE :search OR title LIKE :search '
//                                .' OR notice LIKE :search OR content LIKE :search ) '
//                            .' AND hidden = 0 AND protected <= :perm AND system = 0 AND deleted = 0 ',
//                'params'    => array( ':search' => $search, ':perm' => $protected ),
//                'limit'     => " {$offset}, {$perpage} ",
//            )
//        );
//
//        $page_list  = array();
//        $max_page   = $search_list ? $page + 1 : $page;
//        if ( $page > 1 ) {
//            $page_list[] = "<a href='/search?query={$query}&page=".($page-1)."'>Пред.</a>";
//        }
//        for ( $i = 1; $i <= $max_page; $i++ ) {
//            if ( $page == $i ) {
//                $page_list[] = $i;
//            } else {
//                $page_list[] = "<a href='/search?query={$query}&page=".($i)."'>$i</a>";
//            }
//        }
//        if ( $page != $max_page ) {
//            $page_list[] = "<a href='/search?query={$query}&page=".($page+1)."'>След.</a>";;
//        }
//
//        $this->tpl->assign(array(
//            'page_list' => 'Страницы: '.implode(' ', $page_list),
//            'hl'        => $this->_selected,
//            'sword'     => $_GET['sword'],
//            'list'      => $search_list,
//            'offset'    => $offset,
//        ));
//
//        $content    = $this->tpl->fetch('search.index');
//
//        $this->request->setTitle('Поиск');
//        $this->request->set('template', 'inner');
//        return $content;
//    }

    private function filterFulltext( $search )
    {
        $words = explode( ' ', $search );
        foreach ( $words as &$word ) {
            $word = preg_replace('/\&\w+?\;/ui', '%', $word); // спецсимволы
            $word = preg_replace('/[^a-zA-Zа-яА-Я0-9]+/u', '%', $word);// все, что не буквы и цифры
            $word = str_replace(array('%34', '%39'), '', $word); // хвосты от кавычек
            $word = preg_replace('/([аяуюиыоёэейАЯУЮИЫОЁЭЕЙ]+)$/ui', '', $word);

            $word = $word . '*';
        }
        return implode(' ', $words);
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
        if ( mb_strlen( $search ) < 3 ) {
            $search = $oldsearch;
        }
        $search = preg_replace('/%+/u', '%', $search); // повторяющиеся
        $search = '%'.preg_replace('/^%+|%+$/u', '', $search).'%'; // формируем запрос
        $search  = trim( $search, ' ' );
        return $search;
    }


    public function adminAction()
    {

    }


    public function indexingAction()
    {
        $this->request->setTitle('Индексация сайта');

        $result = array();

        // Pages
        $pageModel = $this->getModel('Page');
        $pages = $pageModel->findAll('hidden = 0 AND protected = 0 AND deleted = 0');
        /** @var $pageObj \Module\Page\Object\Page */
        foreach ( $pages as $pageObj ) {
            $pageModel->getDB()->insertUpdate( 'search', array(
                'alias' => $pageObj->url,
                'object' => 'page',
                'module' => 'default',
                'controller' => 'page',
                'action' => 'index',
                'title' => $pageObj->title,
                'keywords' => $pageObj->keywords,
                'content' => strip_tags( $pageObj->notice . " " . $pageObj->text ),
            ));
            $result[] = 'Add page #'.$pageObj->url;
        }

        // News
        $newsModel = $this->getModel('News');
        $news = $newsModel->findAll('hidden = 0 AND protected = 0 AND deleted = 0');
        /** @var $newsObj \Module\News\Object\News */
        foreach ( $news as $newsObj ) {
            $this->getDB()->insertUpdate( 'search', array(
                'alias' => $newsObj->url,
                'object' => 'news',
                'module' => 'default',
                'controller' => 'news',
                'action' => 'index',
                'title' => $newsObj->title,
                'keywords' => $newsObj->keywords,
                'content' => strip_tags( $newsObj->notice . " " . $newsObj->text ),
            ));
            $result[] = 'Add news #'.$newsObj->url;
        }

        // Trades
        $catModel = $this->getModel('Catalog');
        $trades = $catModel->findAll('cat = 0 AND hidden = 0 AND protected = 0 AND deleted = 0');
        /** @var $catObj \Module\Catalog\Object\Catalog */
        foreach ( $trades as $catObj ) {
            $this->getDB()->insertUpdate( 'search', array(
                'alias' => $catObj->url,
                'object' => 'catalog',
                'module' => 'default',
                'controller' => 'catalog',
                'action' => 'index',
                'title' => $catObj->title,
                'keywords' => '',
                'content' => strip_tags( $catObj->text ),
            ));
            $result[] = 'Add trade #'.$catObj->url;
        }

        return implode('<br>', $result);
    }

}