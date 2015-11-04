<?php
/**
 * Controller for elFinder file manager
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Module\Elfinder\Controller;

use Sfcms\Controller;
use Symfony\Component\HttpFoundation\Response;

require_once 'vendor/studio-42/elfinder/php/elFinderConnector.class.php';
require_once 'vendor/studio-42/elfinder/php/elFinder.class.php';
require_once 'vendor/studio-42/elfinder/php/elFinderVolumeDriver.class.php';
require_once 'vendor/studio-42/elfinder/php/elFinderVolumeLocalFileSystem.class.php';

class ElfinderController extends Controller
{
    public function access()
    {
        return array(
            USER_ADMIN => array('finder', 'connector'),
        );
    }


    /**
     * Create layout for elFinder
     * @return Response
     */
    public function finderAction()
    {
        $this->request->setTitle('ElFinder');
        $this->request->setTemplate('elfinder.index');
        return new Response();
    }

    /**
     * Safety connector for elFinder
     */
    public function connectorAction()
    {
        // Documentation for connector options:
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        $opts = array(
            // 'debug' => true,
            'roots' => array(
                array(
                    'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
                    'path'          => ROOT . '/files/',         // path to files (REQUIRED)
                    'URL'           => '/files/', // URL to files (REQUIRED)
                    'accessControl' => array($this, 'elAccess'),  // disable and hide dot starting files (OPTIONAL)
                )
            )
        );

        $connector = new \elFinderConnector(new \elFinder($opts));
        $connector->run();
    }

    public function elAccess($attr, $path, $data, $volume) {
        return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
            ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
            :  null;                                    // else elFinder decide it itself
    }


}
