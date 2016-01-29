/**
 * Загружает jqGrid
 */
define('system/jquery/jquery.jqGrid',[
    "jquery",
    "jqgrid/i18n/grid.locale-ru",
    "jqgrid/grid.filter",
    "jqgrid/grid.formedit"
],function($){
    $(document).ready(function () {
        $("table.sfcms-jqgrid").each(function(){
            var $list      = $(this),
                config     = $list.data('sfcms-config');
            config.width = $('#workspace').width();
            config.height = 'auto';
            $list.jqGrid(config)
                .jqGrid('navGrid',config.pager,{edit:false,add:false,del:false})
                .jqGrid('filterToolbar',{searchOnEnter : false});
        });
    });
});
