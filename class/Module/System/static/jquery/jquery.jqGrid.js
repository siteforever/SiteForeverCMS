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
        $.jgrid.defaults.width = null;
        $.jgrid.defaults.height = null;
        $.jgrid.defaults.autowidth = true;
        $.jgrid.defaults.autoheight = true;
        $.jgrid.defaults.responsive = true;
        $.jgrid.defaults.styleUI = 'Bootstrap';
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
