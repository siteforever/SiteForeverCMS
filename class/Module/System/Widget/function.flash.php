<?php
/**
 * Output flash content from session
 *
 * Supported error, info, warning and success types
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
function smarty_function_flash($params, Smarty_Internal_Template $smarty)
{
    if (!isset($params['request']) && ! $params['request'] instanceof \Sfcms\Request) {
        throw new InvalidArgumentException('Required parameter request instanced Request');
    }
    /** @var \Sfcms\Request $request */
    $request = $params['request'];

    $flashBag = $request->getSession()->getFlashBag();
    $types = array('error', 'info', 'warning', 'success');
    $output = array();
    foreach ($types as $type) {
        if ($flashBag->has($type)) {
            $output[] = sprintf('<div class="alert alert-%s"><ul>', $type);
            foreach ($flashBag->get($type) as $flash) {
                $output[] = sprintf('<li>%s</li>', $flash);
            }
            $output[] = '</ul></div>';
        }
    }

    return join(PHP_EOL, $output);
}
