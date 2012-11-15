/**
 * Загружает jqGrid
 */
define('jquery/jquery.jqGrid',["jquery"],function($){
    $(document).ready(function () {
        var $list      = $("table.sfcms-jqgrid"),
            config     = $list.data('sfcms-config');

        config.width = $('#workspace').width();
        $list.jqGrid( config );
    });
});
