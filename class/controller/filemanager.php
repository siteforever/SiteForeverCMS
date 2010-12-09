<?php
/**
 * Контроллер управления файлами пользователя
 * @author KelTanas
 */
class controller_FileManager extends Controller
{
    
    function init()
    {
        App::$request->set('tpldata.page.title', 'Менеджер файлов');
    }
    
    
    function indexAction()
    {
        //App::$request->debug();
        
        App::$request->set('getcontent', 1);
        
        if ( App::$request->get('CKEditor') ) {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'CKEditor';
        }
        
        
        $model = Model::getModel('model_Filemanager');

        $model->createFolder();
        $model->uploadFile();
        $model->delete();

        $path       = str_replace( '.', '/', trim( App::$request->get('path'), '.' ) );
        $path       = $path ? '/'.$path.'/' : '/';
        
        /*if ( mb_check_encoding($dir, 'UTF-8') ) {
            $dir = mb_convert_encoding( $dir, mb_internal_encoding(), 'UTF8' );
        }*/
        $files = $model->findAll( $path );
        
        //$view = 'tile';
        $view = App::$config->get('files.manager_view');
        if ( App::$request->get('view') ) {
            $view = App::$request->get('view');
        }
        
        App::$tpl->assign(array(
            'files'     => $files,
            'path'      => str_replace('/', '.', trim($path,'/') ),
            'filedir'   => $path,
        ));
        App::$request->set('tpldata.page.content', App::$tpl->fetch('system:filemanager.'.$view));
        
        //print App::$tpl->fetch('system:filemanager.'.$view);
    }
    
}