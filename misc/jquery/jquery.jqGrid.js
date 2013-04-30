/**
 * Загружает jqGrid
 */
define('jquery/jquery.jqGrid',[
    "jquery",
    'jqgrid' // Этот модуль собирается вручную из файлов библиотеки
],function($){
    $(document).ready(function () {
        var $list      = $("table.sfcms-jqgrid"),
            config     = $list.data('sfcms-config');

        config.width = $('#workspace').width();
        $list.jqGrid(config)
            .jqGrid('navGrid',config.pager,{edit:false,add:false,del:false})
            .jqGrid('filterToolbar',{searchOnEnter : false});
    });
});
