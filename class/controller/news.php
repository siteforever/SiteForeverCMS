<?php
/**
 * Контроллер новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

class Controller_News extends Controller
{

    function indexAction()
    {
        $model = $this->getModel('News');

        if ( $this->request->get('doc', FILTER_VALIDATE_INT) ) {
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
        $id = $this->request->get('doc', FILTER_SANITIZE_NUMBER_INT);
        $news = $model->find( $id );

        // работаем над хлебными крошками
        $path = json_decode( $this->page['path'] );
        $path_part = array();
        $path_part['id']      = false;
        $path_part['name']    = $news['title'] ? $news['title'] : $news['name'];
        $path_part['url']     = $this->page['alias'].'/doc='.$id;
        $path[] = $path_part;
        $this->page['path'] =   json_encode( $path );

        $this->request->set('tpldata.page.path', $this->page['path']);

        $this->tpl->news = $news->getAttributes();

        $this->request->setTitle(
            ( $this->page['title'] ? $this->page['title'] : $this->page['name'] ) .
            ' &rarr; ' .
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

        if ( $count ) {
            $list   = $model->findAllWithLinks(array(
                   'cond'     => $cond,
                   'params'   => $params,
                   'limit'    => $paging['limit'],
                   'order'    => '`date` DESC',
              ));
        }

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

        if ( $this->request->get('catid', FILTER_VALIDATE_INT) !== null ) {
            $this->newsList( $model );
            return;
        }

        if ( $this->request->get('catedit', FILTER_VALIDATE_INT) !== null ) {
            $this->catEdit( $model );
            return;
        }

        if ( $this->request->get('newsedit', FILTER_VALIDATE_INT) !== null ) {
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
        }

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
    function newsList( $model )
    {
        $cat_id =  $this->request->get('catid', FILTER_SANITIZE_NUMBER_INT);

        $count  = $model->count('cat_id = :cat_id', array(':cat_id'=>$cat_id));
        //die(__FILE__.':'.__LINE__);
        //$paging = $this->paging($count, 20, 'admin/news/catid='.$cat_id);
        $paging = new Pager( $count, 20, 'admin/news/catid='.$cat_id);

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
    function newsEdit( $model )
    {
        $form   = $model->getForm();

        if ( $form->getPost() )
        {
            $this->setAjax();

            if ( $form->validate() )
            {
                $data   = $form->getData();
                $obj    = $model->createObject( $data );

                if ( $model->save( $obj ) ) {
                    $this->request->addFeedback('Сохранено успешно');
                    if ( ! $data['id'] ) {
                        reload('admin/news', array('catid'=>$data['cat_id'],));
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

        $edit   = $this->request->get('newsedit', FILTER_SANITIZE_NUMBER_INT);

        if ( $edit ) {
            $news   = $model->find( $edit );
            $form->setData( $news->getAttributes() );
        }
        else {
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
            $this->request->setContent($this->tpl->fetch('news.edit'));
            return;
        }
    }

    /**
     * Править категорию для админки
     * @param model_News $model
     * @return void
     */
    function catEdit( $model )
    {
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
                        reload('admin/news');
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


        $edit   = $this->request->get('catedit', FILTER_SANITIZE_NUMBER_INT);

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
    function catDelete( $model )
    {
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

        reload('admin/news');
    }

    /**
     * @param model_News $model
     * @return void
     */
    function newsDelete( $model )
    {
        $news_id    = $this->request->get('newsdel', FILTER_SANITIZE_NUMBER_INT);

        $obj    = $model->find( $news_id );

        $cat_id = $obj->cat_id;

        $obj->deleted = 1;

        reload('admin/news', array('catid'=>$cat_id));
    }

}