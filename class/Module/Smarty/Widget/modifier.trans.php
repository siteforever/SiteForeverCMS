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
 * Language modifier
 * -------------------------------------------------------------
 * Файл:     modifier.trans.php
 * Тип:      modifier
 * Имя:      lang
 * Назначение:  Translate message
 * -------------------------------------------------------------
 */
function smarty_modifier_trans($id, $params = array(), $domain = null)
{
    return Sfcms::i18n()->trans($id, $params, $domain);
}
