<?php
/**
 *  Класс для работы с загрузкой изображений
 *  @author Keltanas http://ermin.ru
 */
class Images
{
	/**
	 * Создает миниатюру картинки из файла с именем $newfile в файл $thumbfile
	 *
	 * @param string $newfile
	 * @param string $thumbfile
	 * @return bool
	 */
	function createThumb($newfile, $thumbfile, $method = 1)
	{
		/*
		* Создание миниатюр
		*/
		/*if (! is_dir ( $_SERVER ['DOCUMENT_ROOT'] . CATALOG_IMAGE_DIR . 'thumb' )) {
			mkdir ( $_SERVER ['DOCUMENT_ROOT'] . CATALOG_IMAGE_DIR . 'thumb', 0777 );
			Module::error ( "Создан каталог thumb" );
		}*/

		$im = imageCreateFromJpeg ( $_SERVER ['DOCUMENT_ROOT'] . CATALOG_IMAGE_DIR . $newfile );

		$method = defined('THUMB_METHOD') ? THUMB_METHOD : $method;

		// размеры исходного изображения
		$isize = getimagesize ( $_SERVER ['DOCUMENT_ROOT'] . CATALOG_IMAGE_DIR . $newfile );
		$iw = $isize [0];
		$ih = $isize [1];
		// 1. пропорции
		$kh = $ih / THUMB_H;
		$kw = $iw / THUMB_W;
		// 2. выбираем коэффициент
		if ($method == 1) { // добавление полей
			$k = $kw < $kh ? $kh : $kw;
		} elseif ($method == 2) { // обрезание лишнего
			$k = $kw > $kh ? $kh : $kw;
		}
		// вычисляем размеры миниатюры
		$th = round( $ih / $k );
		$tw = round( $iw / $k );



		// 3. вычисляем координаты
		$m = array( // матрица распределения значений
			1 => array(
				'x' => array( 0 , -1 ),
				'y' => array( 1 , 0 ),
			),
			2 => array(
				'x' => array( -0.5 , 0 ),
				'y' => array( 0 , 0.5 ),
			)
		);

		$otrez = round ( ($tw - $th) / 2 );
		$variant = $tw > $th ? 0 : 1;

		$ix = intval( $m[ $method ]['x'][ $variant ] * $otrez );
		$iy = intval( $m[ $method ]['y'][ $variant ] * $otrez );

		//printVar(array( $variant, $ix, $iy));

		// 4.
		$newim = imagecreatetruecolor ( THUMB_W, THUMB_H );
		//$bgcolor = imagecolorallocate($newim, 255, 255, 255);
		$bgcolor = imagecolorat($im, 0, 0);
		imagefill( $newim, 0, 0, $bgcolor ); // заливаем белым
		if ($newim && $im) {
			imagecopyresampled ( $newim, $im, $ix, $iy, 0, 0, $tw, $th, $iw, $ih );
		}

		$return = @imageJpeg ( $newim, $_SERVER ['DOCUMENT_ROOT'] . CATALOG_IMAGE_DIR . $thumbfile, 80 );

		if ($im)
			imagedestroy ( $im );
		if ($newim)
			imagedestroy ( $newim );

		/*
		 * Миниатюра создана
		 */
		return $return;
	}

	/**
	 * Загрузка JPeg файла на сервер
	 *
	 * @param array $files
	 * @param integer $i
	 * @param string $filename
	 * @return string||false
	 */
	function uploadImage($files, $i, $filename = '')
	{
		$return = false;
		if ($files['error'][$i] == UPLOAD_ERR_OK && is_uploaded_file ( $files ['tmp_name'] [$i] )) {
			if ($files['size'][$i] < MAX_FILE_SIZE) {
				if ($files['type'][$i] == 'image/jpeg') {

					$newfile = $filename ? $filename : uniqid () . '.jpg';

					if (move_uploaded_file ( $files ['tmp_name'] [$i], $_SERVER ['DOCUMENT_ROOT'] . CATALOG_IMAGE_DIR . $newfile )) {
						$return = $newfile; // Ok
					} else {
						Module::error ( "Ошибка перемещения файла {$files['name'][$i]} => " . $_SERVER ['DOCUMENT_ROOT'] . CATALOG_IMAGE_DIR . $newfile );
					}

				} else {
					Module::error ( "Тип файла {$files['name'][$i]} не соответствует разрешенному" );
				}
			} else {
				Module::error ( "Размер файла {$files['name'][$i]} слишком велик" );
			}
		} else {
			switch ($files['error'][$i]) {
				case UPLOAD_ERR_INI_SIZE :
				case UPLOAD_ERR_FORM_SIZE :
					Module::error ( "Ошибка загрузки файла {$files['name'][$i]}: Слишком большой размер" );
					break;
				case UPLOAD_ERR_PARTIAL :
					Module::error ( "Ошибка загрузки файла {$files['name'][$i]}: Загружен частично" );
			}

		}
		return $return;
	}


	/**
	 * Работа с галлереей картинок
	 * @return void
	 */
	function pageGallery( $id )
	{
	   if (PAGE_GALLERY) {
	       
	       $db = db::getInstance();

            $files      = isset ( $_FILES ['picture'] ) ? $_FILES ['picture'] : array ();
            $thumbs     = isset ( $_FILES ['thumb'] ) ? $_FILES ['thumb'] : array ();
            $picname    = post::arr ( 'picname' );
            $picdesc    = post::arr ( 'picdesc' );

            if (count ( $files ['error'] ))
            {
                $ins_pict = array ();
                // список имен файлов
                $dir_name = $module.'/'.$id.'/';
                // если каталога не существует, то создать
                $check_dir = trim( CATALOG_IMAGE_DIR.$dir_name, '/' );
                if ( !is_dir( $check_dir ) ) {
                    mkdir( $check_dir, 0775, true );
                }
                $file_list = glob($dir_name.'*.jpg');
                // выбираем кол-во...
                // след имя будет max + 1
                $count_list = count($file_list);
                if ( $count_list ) {
                    $max = $file_list[ $count_list - 1 ];
                } else {
                    $max = 0;
                }


                foreach ( $files ['error'] as $i => $ef ) {
                    // Загрузка картинки
                    $max++;
                    $next_file = substr('0000'.$max, -4, 4).'.jpg';

                    // загрузить большую картинку
                    $newfile = $this->uploadImage ( $files, $i, $dir_name.$next_file );
                    if ($newfile) { // если загрузил
                        // Создание миниатюр
                        $thumb = $this->uploadImage ( $thumbs, $i, $dir_name.'thumb_'.$next_file );
                        if ( !$thumb ) {

                            if( $this->createThumb ( $newfile, $dir_name.'thumb_'.$next_file ) ) {
                                $thumb = $dir_name.'thumb_'.$next_file;
                            } else {
                                $thumb = false;
                            }
                        }
                        if ($thumb) {
                            $ins_pict [] = "('','$id', '" . CATALOG_IMAGE_DIR . $newfile . "', '" . CATALOG_IMAGE_DIR . $thumb . "', '{$picname[$i]}', '{$picdesc[$i]}', 1, " . time () . ")";
                        } else {
                            self::error('Превью отсутствует. Загрузка прервана.');
                        }
                    }
                }
                if (count ( $ins_pict )) {
                    $query = "INSERT INTO ".DBIMAGES." VALUES " . implode ( ',', $ins_pict );
                    $db->query ( $query );
                }
            }
            /*
             * Конец загрузки файлов
             */

            // Обновление записей для картинок
            $picname_upd = post::arr('picname_upd');
            $picdesc_upd = post::arr('picdesc_upd');
            if ($picdesc_upd && $picname_upd && count($picdesc_upd) == count($picname_upd)) {
                $upd = array();
                foreach ($picdesc_upd as $i => $pd) {
                    $pn = $picname_upd[$i];
                    $upd[] = array('id'=>$i, 'name'=>$pn, 'desc'=>$pd);
                }
                
                $db->insertUpdateMulti( DBIMAGES, $upd );
            }

            // Удаление картинок
            $del_picture = post::arr ( 'del_picture', array () );
            $this->del_picture ( $del_picture );
        }
	}

}

?>