<?php
/**
 * Контроллер поиска
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Search\Controller;

use Sfcms\Controller;

class SearchController extends Controller
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
            USER_ADMIN => array('admin','indexing'),
        );
    }

    public function indexAction()
    {
        $this->request->setTitle($this->t('Search'));
        $this->tpl->getBreadcrumbs()->addPiece('index',$this->t('Main'))->addPiece(null,$this->request->getTitle());

        $query = $this->request->query->get('query');
        if (!$query) {
            return $this->render('search.index', array('query' => $query));
        }
        $search = urldecode($query);
        $this->tpl->assign('query', $search);



        if (mb_strlen($search, 'utf-8') <= 3) {
            return $this->render('search.index', array('error' => $this->t('page', 'Search phrase is too short')));
        }

        $search = $this->filterFulltext( $search );

        $modelPage = $this->getModel('Page');
        $crit = $modelPage->createCriteria(array(
//                'cond' => 'MATCH(`title`,`keywords`,`content`) AGAINST (? IN BOOLEAN MODE)',
            'cond' => '(`title` LIKE :s OR `keywords` LIKE :s OR `content` LIKE :s) AND deleted = 0 AND hidden = 0',
            'params' => array(':s'=>$search),
        ));
        $count  = $modelPage->count($crit);
        $paging = $this->paging($count, 10, $this->router->generate('search', array('query'=>$query)));
        $crit->limit = $paging->limit;
        $result = $modelPage->findAll($crit);

        return $this->render('search.index', array(
            'search' => $search,
            'result' => $result,
            'paging' => $paging,
            'request' => $this->request,
        ));
    }

    private function filterFulltext( $search )
    {
        $words = explode( ' ', $search );
        foreach ( $words as &$word ) {
            $word = preg_replace('/\&\w+?\;/ui', '%', $word); // спецсимволы
            $word = preg_replace('/[^a-zA-Zа-яА-Я0-9]+/u', '%', $word);// все, что не буквы и цифры
            $word = str_replace(array('%34', '%39'), '', $word); // хвосты от кавычек
            $word = preg_replace('/([аяуюиыоёэейАЯУЮИЫОЁЭЕЙ]+)$/ui', '', $word);
        }
        return '%'.implode('%', $words).'%';
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
