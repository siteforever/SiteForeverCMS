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
 * Назначение:  Translate phrase
 * Alias for smarty_modifier_lang
 * -------------------------------------------------------------
 */
function smarty_modifier_trans($content, $cat = "", $params = array())
{
    return Sfcms::i18n()->write($cat, $content, $params);
}
