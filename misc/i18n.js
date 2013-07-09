/**
 * Internatianalisation module
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 * @file   /misc/siteforever/i18n.js
 */

define('i18n',function(){
    var i18n = function( cat, phrase ) {
        if ( cat && ! phrase ) {
            phrase = cat;
            cat    = false;
        }
        if ( cat && i18n._dict[ 'cat_' + cat ] && i18n._dict[ 'cat_' + cat ][ phrase ] ) {
            return i18n._dict[ 'cat_' + cat ][ phrase ];
        }
        if ( i18n._dict[ phrase ] ) {
            return i18n._dict[ phrase ];
        }
        return phrase;
    };

    /*:dictionary:*/

    return i18n;
});

