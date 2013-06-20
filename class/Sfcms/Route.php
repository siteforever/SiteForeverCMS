<?php
/**
 * Route interface
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

abstract class Route
{
    /**
     * @abstract
     *
     * @param $request
     * @param $route
     *
     * @return mixed
     */
    abstract public function route(Request $request, $route);

    /**
     * Из массива [ id, 10, page, 5 ] создаст параметры [ id: 10, page: 5 ]
     *
     * @param array $params
     *
     * @return array
     */
    protected function extractAsParams($params = array())
    {
        $result = array();
        if (0 == count($params) % 2) {
            $key = '';
            foreach ($params as $i => $r) {
                if ($i % 2) {
                    $result[$key] = $r;
                } else {
                    $key = $r;
                }
            }
        }

        return $result;
    }
}
