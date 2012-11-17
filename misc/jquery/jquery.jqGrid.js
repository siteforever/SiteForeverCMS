/**
 * Загружает jqGrid
 */
define('jquery/jquery.jqGrid',[
    "jquery",
    'jqGrid/js/i18n/grid.locale-ru',
    'jqGrid/js/grid.base',
    'jqGrid/js/grid.common',
//    'jqGrid/js/grid.formedit',
//    'jqGrid/js/grid.inlinedit',
//    'jqGrid/js/grid.celledit',
//    'jqGrid/js/grid.subgrid',
//    'jqGrid/js/grid.treegrid',
//    'jqGrid/js/grid.grouping',
//    'jqGrid/js/grid.custom',
    'jqGrid/js/grid.tbltogrid',
//    'jqGrid/js/grid.import',
    'jqGrid/js/jquery.fmatter',
    'jqGrid/js/JsonXml',
    'jqGrid/js/grid.jqueryui',
    'jqGrid/js/grid.filter'
],function($){
    $(document).ready(function () {
        var $list      = $("table.sfcms-jqgrid"),
            config     = $list.data('sfcms-config');

        config.width = $('#workspace').width();
        $list.jqGrid( config );//.jqGrid('navGrid',config.pager,{edit:false,add:false,del:false});
    });
});
