<?php
/**
 * Контроллер новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Controller_News extends Sfcms_Controller
{

    function indexAction()
    {
        $model = $this->getModel('News');
//        $this->request->setTitle('Новости');

        if ( $this->request->get('doc') ) {
            return $this->getNews($model);
        }
        else {
            return $this->getNewsList($model);
        }
        //print __FILE__;
    }

    /**
     * Отображать новость на сайте
     * @param model_News $model
     * @return void
     */
    function getNews($model)
    {
        $id = intval( $this->request->get('doc') );
        $news = $model->find( $id );

        if ( ! $news ) {
            $this->request->addFeedback('Материал не найден');
            return;
        }

        // работаем над хлебными крошками
        $bc = $this->tpl->getBreadcrumbs();
        $bc->clearPieces();
        $bc->fromJson( $this->page['path'] );
        $bc->addPiece( null, $news['title'] ? $news['title'] : $news['name'] );

        $this->request->set('tpldata.page.path',$bc->toJson());

        $this->tpl->news = $news->getAttributes();

        $this->request->setTitle(
            ( $news['title'] ? $news['title'] : $news['name'] )
        );

        if ( ! $this->user->hasPermission( $news['protected'] ) ) {
            $this->request->setContent('Не достаточно прав доступа');
        }
        else {
            $this->request->setContent($this->tpl->fetch('news.item'));
        }
    }

    /**
     * Отображать список новостей на сайте
     * @param model_News $model
     * @return void
     */
    function getNewsList($model)
    {
        /**
         * @var Data_Object_NewsCategory $cat
         */
        $cat    = $model->category->find( $this->page['link'] );

        if ( ! $cat ) {
            $this->request->setContent(
                $this->tpl->fetch('news.catempty')
            );
            return;
        }

        $cond   = '`deleted` = 0 AND `hidden` = 0 AND `cat_id` = ?';
        $params = array($cat->getId());

        $count  = $model->count($cond, $params);

        $paging     = new Pager( $count, $cat->per_page, $this->page['alias'] );

        $list   = $model->findAllWithLinks(array(
            'cond'     => $cond,
            'params'   => $params,
            'limit'    => $paging['limit'],
            'order'    => '`date` DESC',
        ));

        $this->tpl->assign(array(
            'paging'    => $paging,
            'list'      => $list,
            'page'      => $this->page,
            'cat'       => $cat->getAttributes(),
        ));

        switch ( $cat['type_list'] ) {
            case 2:
                $template   = 'news.items_list';
                break;
            default:
                $template   = 'news.items_blog';
        }

        $this->request->setContent($this->tpl->fetch($template));
    }

    /**
     * Управление новостями
     * @return void
     */
    function adminAction()
    {

        $this->request->setTitle('Материалы');
        /**
         * @var model_News $model
         */
        $model      = $this->getModel('News');
        $category   = $model->category;
//        die(__FILE__.':'.__LINE__);

      /*  if ( $this->request->get('catid', FILTER_VALIDATE_INT) !== null ) {
            $this->newsList( $model );
            return;
        }

        if ( $this->request->get('catedit', FILTER_VALIDATE_INT) !== null ) {
            $this->catEdit( $model );
            return;
        }

        if ( $this->request->get('newsedit', FILTER_SANITIZE_NUMBER_INT) !== null  ) {
            $this->newsEdit( $model );
            return;
        }

        if ( $this->request->get('catdel', FILTER_VALIDATE_INT) !== null ) {
            $this->catDelete( $model );
            return;
        }

        if ( $this->request->get('newsdel', FILTER_VALIDATE_INT) !== null ) {
            $this->newsDelete( $model );
            return;
        }     */

        $list   = $category->findAll(array('cond'=>'deleted = 0'));
        $this->tpl->assign(array(
            'list'  => $list,
        ));

        //$this->request->setContent( $this->tpl->fetch('system:news.admin') );
        $this->request->setContent( $this->tpl->fetch('news.catslist') );
        return;
    }

    /**
     * Список новостей для админки
     * @param model_News $model
     * @return void
     */
//    function newsList( $model )
    function newslistAction()
    {
        /**/
        $this->request->setTitle('Материалы');
        $model      = $this->getModel('News');
        /**/
        $cat_id =  $this->request->get('catid', FILTER_SANITIZE_NUMBER_INT);

        $count  = $model->count('cat_id = :cat_id', array(':cat_id'=>$cat_id));
        //die(__FILE__.':'.__LINE__);
        //$paging = $this->paging($count, 20, 'admin/news/catid='.$cat_id);
//        $paging = new Pager( $count, 20, 'admin/news/catid='.$cat_id);
        $paging = new Pager( $count, 20, 'news/newslist/catid/'.$cat_id);

        $list = $model->findAll(array(
            'cond'      => 'cat_id = :cat_id AND deleted = 0',
            'params'    => array(':cat_id'=>$cat_id),
            'limit'     => $paging->limit,
            'order'     => '`date` DESC, `id` DESC',
        ));

        $cat    = $model->category->find($cat_id);

        $this->tpl->assign(array(
            'paging'    => $paging,
            'list'      => $list,
            'cat'       => $cat,
        ));

        $this->request->setContent($this->tpl->fetch('news.admin'));
    }

    /**
     * Редактрование новости для админки
     * @param model_News $model
     * @return
     */
//    function newsEdit( $model )
    function newseditAction( )
    {
        /**/
        $this->request->setTitle('Материалы');
        $model      = $this->getModel('News');
        /** @var $form Form_Form */
        $form   = $model->getForm();

        if ( $form->getPost() ) {
            $this->setAjax();

            if ( $form->validate() ) {
                $data   = $form->getData();
                $obj    = $model->createObject( $data );

                if ( $model->save( $obj ) ) {
                    $this->request->addFeedback(t('Data save successfully'));
                    if ( ! $data['id'] ) {
//                        $this->reload('admin/news/', array('catid'=>$data['cat_id'],));
                        $this->reload('news/newslist/', array('catid'=>$data['cat_id'],));
                    }
                }
                else {
                    $this->request->addFeedback(t('Data not saved'));
                }
            }
            else {
                //$this->request->addFeedback('Форма заполнена не правильно');
                $this->request->addFeedback($form->getFeedbackString());
            }
            return $this->request->getFeedbackString();
        }

//        $edit   = $this->request->get('newsedit', FILTER_SANITIZE_NUMBER_INT);
        $edit   = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT);

        if ( $edit ) {
            $news   = $model->find( $edit );
            $form->setData( $news->getAttributes() );
        } else {
            $news   = $model->createObject();
        }

        $cat    = null;
        if ( isset( $news['cat_id'] ) && $news['cat_id'] ) {
            $cat    = $model->category->find( $news['cat_id'] );
        }
        if ( is_null( $cat ) && $this->request->get('cat', FILTER_VALIDATE_INT) ) {
            $cat    = $model->category->find( $this->request->get('cat', FILTER_SANITIZE_NUMBER_INT) );
        }

        if ( $edit !== false ) {
            $this->tpl->assign(array(
                'form'  => $form,
                'cat'   => $cat,
            ));
            return $this->tpl->fetch('news.edit');
        }
    }

    /**
     * Править категорию для админки
     * @param model_News $model
     * @return void
     */
//    function catEdit( $model )
    function cateditAction( )
    {
        /**/
        $this->request->setTitle('Материалы');
        $model      = $this->getModel('News');
        /**/
        $category   = $model->category;

        $form   = $category->getForm();


        if ( $form->getPost() )
        {
            $this->setAjax();

            if ( $form->validate() )
            {
                $data   = $form->getData();
                $obj    = $category->createObject( $data );

                if ( $category->save( $obj ) )
                {
                    $this->request->addFeedback('Сохранено успешно');
                    if ( ! $form->getField('id')->getValue() ) {
                        $this->reload('admin/news');
                    }
                }
                else {
                    $this->request->addFeedback('Данные не были сохранены');
                }
            }
            else {
                //$this->request->addFeedback('Форма заполнена не правильно');
                $this->request->addFeedback($form->getFeedbackString());
            }
            return;
        }


//        $edit   = $this->request->get('catedit', FILTER_SANITIZE_NUMBER_INT);
        $edit   = $this->request->get('id', FILTER_SANITIZE_NUMBER_INT);

        if ( $edit ) {
            $news   = $category->find( $edit );
            $form->setData( $news->getAttributes() );
        }

        if ( $edit !== false ) {
            $this->tpl->assign(array(
                'form'  => $form,
            ));
            $this->request->setContent($this->tpl->fetch('news.catedit'));
            return;
        }
    }

    /**
     * Удаление категории новостей и ее подновостей
     * @param model_News $model
     * @return void
     */
//    function catDelete( $model )
    function catdeleteAction( )
    {
        /**/
        $this->request->setTitle('Материалы');
        $model      = $this->getModel('News');
        /**/
        $category   = $this->getModel('NewsCategory');
        $cat_id     = $this->request->get('catdel', FILTER_SANITIZE_NUMBER_INT);

        $cat_obj    = $category->find($cat_id);

        $news       = $model->findAll(array(
            'cond'  => 'cat_id = :cat_id',
            'params'=> array(':cat_id'=>$cat_id),
        ));

        foreach ( $news as $obj ) {
            $obj->deleted = 1;
        }

        $cat_obj->deleted   = 1;

        $this->reload('admin/news');
    }

    /**
     * @param model_News $model
     * @return void
     */
//    function newsDelete( $model )
    function newsdeleteAction(  )
    {
        /**/
        $this->request->setTitle('Материалы');
        $model      = $this->getModel('News');
        /**/
        $news_id    = $this->request->get('newsdel', FILTER_SANITIZE_NUMBER_INT);

        $obj    = $model->find( $news_id );

        $cat_id = $obj->cat_id;

        $obj->deleted = 1;

        $this->reload('admin/news', array('catid'=>$cat_id));
    }

     /**
     * @return array
     */
    function access()
    {
        return array(
            'system'    => array('admin','newslist','newsedit','catedit','catdelete','newsdelete'),
        );
    }

}