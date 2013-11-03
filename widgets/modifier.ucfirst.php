<?php
/**
 * This file is part of the SiteForever package.
 * @author: Nikolay Ermin <keltanas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Smarty plugin
 * UCfirst modifier
 * -------------------------------------------------------------
 * Файл:     modifier.ucfirst.php
 * Тип:      modifier
 * Имя:      ucfirst
 * -------------------------------------------------------------
 */
function smarty_modifier_ucfirst($content)
{
    return mb_strtoupper(mb_substr($content, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($content, 1, mb_strlen($content), 'UTF-8');
}
