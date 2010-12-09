<?php
/**
 * Модель менеджера файлов
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class model_Filemanager extends Model
{
    public $breadcrumbs = array();

    function find( $id )
    {
        return;
    }

    private function getUserdir()
    {
        $root       = rtrim(  $_SERVER['DOCUMENT_ROOT'], '/\\' );
        $userdir    = str_replace(array('/', '\\'), '/', $root.App::$config->get('users.userdir') );
        return $userdir;
    }

    /**
     * Искать файлы и папки по пути
     * @param  $path
     * @return array
     */
    function findAll( $path )
    {
        $root       = rtrim(  $_SERVER['DOCUMENT_ROOT'], '/\\' );
        $userdir    = $this->getUserdir();

        // директория на сервере
        $dir = $userdir.$path;

        // список файлов
        $files = array();
        $filelist = scandir( $dir );


        $this->createBreadcrumbs( $dir, $userdir );

        
        foreach( $filelist as $file )
        {
            if ( preg_match( '/^[\.].*/i', $file ) ) {
                continue;
            }

            $file = $dir.$file;

            $info = pathinfo( $file );

            // перекодировка для винды
            $name = $info['basename'];
            if ( ! mb_check_encoding( $name, 'UTF-8' ) ) {
                $name = mb_convert_encoding( $name, 'UTF8' );
            }

            $cmp    = strcmp($file, $userdir);
            $path   = str_replace( array('\\', '/'), '.', trim( substr( $file, -$cmp, $cmp ), '\\/' ) );

            $cmpfile= strcmp($file, $root);
            $link   = substr( $file, -$cmpfile, $cmpfile );

            //print $root.' __ '.$link.'<br>';

            $filecard = array(
                    'name'  => $name,
                    'file'  => $file,
                    'link'  => $link,
                    'path'   => $path,
            );

            if ( is_dir( $file ) ) {

                $filecard['type'] = 'folder';
                $filecard['open'] = str_replace( array('\\', '/'), '.', trim($name, '/\\' ) );
            }
            elseif ( in_array( strtolower( $info['extension'] ), array('jpg','gif','png') ) )
            {
                $filecard['type']       = 'img';
                $filecard['imagesize']  = getimagesize($file);
                $filecard['filedir']    = $filedir;
            }
            else {
                $filecard['type'] = 'file';
            }

            $filecard['size'] = round( filesize( $file )/1024, 2).' kb';

            $files[] = $filecard;
        }

        usort( $files, array('model_FileManager', 'cards_compare') );

        //printVar( $files );

        return $files;
    }


    /**
     * Создает хлебные крошки и созраняет в шаблоне
     * @param  $dir
     * @param  $userdir
     * @return void
     */
    function createBreadcrumbs( $dir, $userdir )
    {
        // breadcrumbs
        $bc_cmp = strcmp( $dir, $userdir );
        $bc_str = trim( substr( $dir, -$bc_cmp, $bc_cmp ), '/');
        $bc = explode('/', $bc_str );
        $accumulation_path = '/';
        $bc_path = '/ <a '.href('admin/filemanager',array('path' => '')).' path=".">files</a> ';

        $bc_countback = count( $bc );

        foreach( $bc as $i => $bcitem) 
        {
            $bc_countback--;
            
            $bc_link = trim( str_replace( '/', '.', $accumulation_path).$bcitem.'.', '.' );

            // выбираем последний элемент
            if ( $bc_countback ) {
                $bc_path .= '/ <a '.href('admin/filemanager',array('path' => $bc_link)).' path="'.$bc_link.'">'.$bcitem.'</a> ';
            } else {
                $bc_path .= '/ ' . $bcitem;
            }
            $accumulation_path .= $bcitem.'/';
        };

        App::$tpl->assign('path_bc', $bc_path);
    }


    /**
     * Загрузка файла
     * @return void
     */
    function uploadFile()
    {
        if ( count($_FILES) > 0 ) {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'UPLOAD_FILE';
        } else {
            return;
        }
        
        $file = $_FILES['upload'];

        if ( $file['error'] != UPLOAD_ERR_OK ) {
            switch ( $file['error'] ) {
                
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    App::$request->addFeedback('Размер принятого файла превысил максимально допустимый размер');
                    break;
                
                case UPLOAD_ERR_PARTIAL:
                    App::$request->addFeedback('Загружаемый файл был получен только частично');
                    break;
                
                case UPLOAD_ERR_NO_FILE:
                    App::$request->addFeedback('Загружаемый файл был получен только частично');
                    break;
                
                default:
                    App::$request->addFeedback('Неизвестная ошибка загрузки');
            }
            return;
        }
        
        //printVar($file);
        //print $path;
        
        if ( ! in_array( $file['type'], App::$config->get('files.include_types') ) ) {
            App::$request->addFeedback('Недопустимый тип файла');
            return;
        }
        
        if ( $file['size'] > App::$config->get('files.max_size') ) {
            App::$request->addFeedback('Слишком большой размер');
            return;            
        }
        
        $userdir    = $this->getUserdir();

        $current_dir = App::$request->get('current_dir');
        
        $file['name'] = translit( strtolower( $file['name'] ) );
        
        // директория на сервере
        $path = $userdir.$current_dir.$file['name'];
                
        
        if ( move_uploaded_file( $file['tmp_name'], $path ) ) {
            App::$request->addFeedback('Файл загружен успешно');
        }
        
    }

    /**
     * Удалить файл/каталог
     * @return void
     */
    function delete()
    {
        $path = App::$request->get('delete');

        if ( $path ) {
            
            $root = rtrim(  $_SERVER['DOCUMENT_ROOT'], '/\\' );
            $path = $root.$path;
            
            if ( file_exists( $path ) ) {
                
                if ( is_dir( $path ) ) {
                    if ( @rmdir( $path ) ) {
                        App::$request->addFeedback('Каталог удален');
                    } else {
                        App::$request->addFeedback('Каталог нельзя удалить');
                    }
                }
                else {
                    if ( @unlink( $path ) ) {
                        App::$request->addFeedback('Файл удален');
                    } else {
                        App::$request->addFeedback('Файл нельзя удалить');
                    }
                }
            }
        }
    }

    /**
     * Создание каталога
     * @return void
     */
    function createFolder()
    {
        $current_dir = App::$request->get('current_dir');
        $new_dir    = App::$request->get('new_dir');

        if ( !$current_dir || !$new_dir ) {
            return;
        }

        $userdir    = $this->getUserdir();

        $new_dir = strtolower( trim($new_dir) );
        $new_dir = str_replace(array(' '), '_', $new_dir);
        $new_dir = preg_replace('/[^a-z0-9\._-]/', '', $new_dir);


        $dir = $userdir.$current_dir.$new_dir;

        if ( strlen( $dir ) < 2 ) {
            App::$request->addFeedback('Слишком короткое название');
            return;
        }

        if ( file_exists( $dir ) ) {
            App::$request->addFeedback('Каталог уже существует');
            return;
        }

        if ( mkdir( $dir, 0755, true ) ) {
            App::$request->addFeedback('Каталог успешно создан');
            return;
        }

        App::$request->addFeedback('Ошибка создания каталога');

        //print $new_dir;
    }


    /**
     * Дополнительная сортировка для списка файлов и вывод каталогов на передние позиции
     * @param $a
     * @param $b
     */
    static function cards_compare( $a, $b )
    {
        if ( $a['type'] == $b['type'] ) {
            return strnatcasecmp ( $a['name'], $b['name'] );
        }

        if ( $a['type'] == 'folder' ) {
            return -1;
        }
        elseif ( $b['type'] == 'folder' ) {
            return +1;
        }
        return 0;
    }    
}
