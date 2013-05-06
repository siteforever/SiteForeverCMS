<?php
/**
 * Контроллер поиска
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Search\Controller;

use Sfcms_Controller;

class SearchController extends Sfcms_Controller
{
    public function indexAction()
    {
        $query = $this->request->query->get('query');
    }
}