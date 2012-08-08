/**
 * Internatianalisation module
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 * @file   /misc/siteforever/i18n.js
 */

(function($s){
    $s.i18n = function( cat, phrase ) {
        if ( cat && ! phrase ) {
            phrase = cat;
            cat    = false;
        }
        if ( cat && $s.i18n._dict[ 'cat_' + cat ] && $s.i18n._dict[ 'cat_' + cat ][ phrase ] ) {
            return $s.i18n._dict[ 'cat_' + cat ][ phrase ];
        }
        if ( $s.i18n._dict[ phrase ] ) {
            return $s.i18n._dict[ phrase ];
        }
        return phrase;
    };
})(siteforever);

