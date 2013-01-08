<?php
/**
 * Контроллер для отображения конфигурации системы окружения
 * @author KelTanas
 */
class Controller_System extends Sfcms_Controller
{
    public function access()
    {
        return array(
            'system' => array('index','assembly','jqgrid'),
        );
    }

    public function indexAction()
    {
        $this->request->setTitle('Конфигурация системы');
        $this->request->setTemplate('index');

        $modules = get_loaded_extensions();

        $msys   = array();

        foreach( $modules as $i => $module ) {
            if ( $msys[$i] = @ini_get_all($module, false) ) {

            } else {
                unset( $modules[$i] );
            }
        }

        $sys = ini_get_all(null, false);

        $this->tpl->assign(
            array(
                'sys'     => $sys,
                'msys'    => $msys,
                'modules' => $modules,
                'ver'     => phpversion(),
            )
        );

        $this->request->setContent( $this->tpl->fetch('system:system.index') );
    }


    /**
     * Сборка
     */
    public function assemblyAction()
    {
        $this->request->setTitle(t('Assembly'));
    }


    /**
     * Сборка jqgrid модуля
     */
    public function jqgridAction()
    {
        $modules = array(
            'jqGrid/js/i18n/grid.locale-ru',
            'jqGrid/js/grid.base',
            'jqGrid/js/grid.common',
            'jqGrid/js/grid.formedit',
            'jqGrid/js/grid.inlinedit',
            'jqGrid/js/grid.celledit',
            'jqGrid/js/grid.subgrid',
            'jqGrid/js/grid.treegrid',
            'jqGrid/js/grid.grouping',
            'jqGrid/js/grid.custom',
            'jqGrid/js/grid.tbltogrid',
            'jqGrid/js/grid.import',
            'jqGrid/js/jquery.fmatter',
            'jqGrid/js/JsonXml',
            'jqGrid/js/grid.jqueryui',
            'jqGrid/js/grid.filter',
//            'jquery/jquery.jqGrid',
        );
        $assemble = array_map(function($mod){
            return file_get_contents( SF_PATH .'/misc/'.$mod.'.js' )."\n"
                .('jquery/jquery.jqGrid' != $mod ? sprintf('define("%s",function(){});',$mod)."\n" : '' );
        },$modules);

        $content = join("\n",$assemble);
//        $content = Sfcms::html()->jsMin( $content );
        file_put_contents(SF_PATH.'/misc/admin/jquery/jqgrid.js', $content);
        return 'done';
    }
}
