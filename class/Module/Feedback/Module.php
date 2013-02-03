<?php
/**
 * Модуль обратной связи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\Feedback;

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    /**
     * @inherit
     */
    public static function relatedField()
    {
        return 'id';
    }

}
