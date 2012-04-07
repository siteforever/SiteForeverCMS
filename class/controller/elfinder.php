<?php
/**
 * Файловый менеджер
 */
class Controller_Elfinder extends Sfcms_Controller
{
    /**
     * Initialize
     */
    public function init()
    {
        $this->request->setTitle('ElFinder');

        $this->request->addStyle( $this->request->get('path.misc') . '/elfinder/css/elfinder.css' );
        $this->request->addScript( $this->request->get('path.misc') . '/elfinder/js/elfinder.full.js' );
        $this->request->addScript( $this->request->get('path.misc') . '/elfinder/js/i18n/elfinder.ru.js' );
    }


    /**
     * Access rules
     * @return array
     */
    public function access()
    {
        return array(
            'system'    => array(
                'connector','finder',
            ),
        );
    }


    /**
     * Выводит поисковик
     */
    public function indexAction()
    {
        return 'Finder protected controller';
//        if ( $this->request->get('connector') ) {
//            return $this->getConnector();
//        }
//        if ( $this->request->get('finder') ) {
//            return $this->getFinder();
//        }
//        return t('Finder not runned');
    }


    /**
     * Вернет конфиг
     * @return
     */
    public function connectorAction()
    {
        $this->app()->getLogger()->log( $_REQUEST, 'Request' );
        $_POST  = $_POST + $_REQUEST;
        $_GET   = $_GET + $_REQUEST;
        $this->app()->getLogger()->log( $_POST, 'Post' );
        $this->app()->getLogger()->log( $_GET, 'Get' );

//        $this->setAjax();
        $opts = array(
            'root'            => ROOT.DIRECTORY_SEPARATOR.'files',  // path to root directory
            'URL'             => '/files/', // root directory URL
            'rootAlias'       => 'Мои файлы',       // display this instead of root directory name
            //'uploadAllow'   => array('images/*'),
            //'uploadDeny'    => array('all'),
            //'uploadOrder'   => 'deny,allow'
            // 'disabled'     => array(),      // list of not allowed commands
            'dotFiles'        => false,        // display dot files
            // 'dirSize'      => true,         // count total directories sizes
            // 'fileMode'     => 0666,         // new files mode
            // 'dirMode'      => 0777,         // new folders mode
            // 'mimeDetect'   => 'auto',       // files mimetypes detection method (finfo, mime_content_type, linux (file -ib), bsd (file -Ib), internal (by extensions))
            // 'uploadAllow'  => array(),      // mimetypes which allowed to upload
            // 'uploadDeny'   => array(),      // mimetypes which not allowed to upload
            // 'uploadOrder'  => 'deny,allow', // order to proccess uploadAllow and uploadAllow options
            'imgLib'          => 'gd',       // image manipulation library (imagick, mogrify, gd)
            // 'tmbDir'       => '.tmb',       // directory name for image thumbnails. Set to "" to avoid thumbnails generation
            // 'tmbCleanProb' => 1,            // how frequiently clean thumbnails dir (0 - never, 100 - every init request)
            // 'tmbAtOnce'    => 5,            // number of thumbnails to generate per request
            // 'tmbSize'      => 48,           // images thumbnails size (px)
            // 'fileURL'      => false,         // display file URL in "get info"
            // 'dateFormat'   => 'j M Y H:i',  // file modification date format
            // 'logger'       => null,         // object logger
            // 'defaults'     => array(        // default permisions
            //  'read'   => true,
            //  'write'  => true,
            //  'rm'     => true
            //  ),
            // 'perms'        => array(),      // individual folders/files permisions
//             'debug'        => true,         // send debug to client
//             'archiveMimes' => array(),      // allowed archive's mimetypes to create. Leave empty for all available types.
            'archivers' => false,
//             'archivers'    => array(),       // info about archivers to use. See example below. Leave empty for auto detect
//             'archivers' => array(
//                'create' => array(
//                    'application/x-gzip' => array(
//                        'cmd' => 'tar',
//                        'argc' => '-czf',
//                        'ext'  => 'tar.gz'
//                        )
//                    ),
//                'extract' => array(
//                    'application/x-gzip' => array(
//                        'cmd'  => 'tar',
//                        'argc' => '-xzf',
//                        'ext'  => 'tar.gz'
//                      ),
//                  'application/x-bzip2' => array(
//                      'cmd'  => 'tar',
//                      'argc' => '-xjf',
//                      'ext'  => 'tar.bz'
//                        )
//                    )
//                )
        );

        $fm = new elFinder($opts);
        $fm->run();
        return;
    }


    /**
     * Вернет страницу-контейнер
     */
    public function finderAction()
    {
        $this->request->set('resource', 'system:');
        $this->request->set('template', 'elfinder');
        return;
    }

}