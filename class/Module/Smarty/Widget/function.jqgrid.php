<?php
/**
 * jqGrid widget
 */
use Sfcms\JqGrid\Provider;

function smarty_function_jqgrid($params, $smarty)
{
    if (!isset($params['provider']) || !$params['provider'] instanceof Provider) {
        return 'Provider not defined';
    }
    /** @var $provider Provider */
    $provider = $params['provider'];
    $name     = isset($params['name']) ? $params['name'] : 'jqgrid';

    $config = $provider->getConfig($name, $params);

    return sprintf(
        '<table id="%s_list" class="table table-striped sfcms-jqgrid" data-sfcms-module="jquery/jquery.jqGrid" data-sfcms-config=\'%s\'>'
            . '</table><div id="%s_pager"></div>',
        $name,
        json_encode($config),
        $name
    );
}
