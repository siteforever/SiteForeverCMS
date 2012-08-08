/**
 * Internatianalisation module
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 * @file   /misc/siteforever/i18n.js
 */

siteforever.i18n = function( phrase ) {
    if ( siteforever.i18n._dict[ phrase ] ) {
        return siteforever.i18n._dict[ phrase ];
    }
    return phrase;
};