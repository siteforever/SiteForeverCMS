<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Catalog\Model;

use Sfcms\Model;

class CommentModel extends  Model
{
    public function relation()
    {
        return array(
            'Product'     => array(self::BELONGS, 'Catalog', 'product_id'),
        );
    }

}
