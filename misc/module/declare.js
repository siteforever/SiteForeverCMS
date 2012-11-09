/**
 * Declare module for SiteForeverCMS
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define([
],function(){

    //var declare =
    return function( parents, object ) {
        if ( ! parents ) {
            object = parents;
            parents = null;
        }

        var base, i, j;

        if ( 'array' == typeof parents ) {
            base = parents[0];
            for( i in parents ) {
                if ( i == 0 ) continue;
                for ( j in parents.prototype[i] ) {
                    base.prototype[j] = parents.prototype[j];
                }
            }
        } else if( 'object' == typeof parents ) {
            base = parents;
        } else if( null === parents ) {
            base = {};
        } else {
            console.error('Undefined type of parents');
            return false;
        }

        return base;
    };

    //return declare;
});