<?php
/**
 * Контроллер новостей
 * @author KelTanas
 *
 */
class controller_News extends Controller
{

    function indexAction()
    {
        $model = $this->getModel('News');

        if ( $this->request->get('doc', FILTER_VALIDATE_INT) ) {
            $this->getNews($model);
        }
        else {
            $this->getNewsList($model);
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
        $path_part = new stdClass();
        $path_part->id      = false;
        $path_part->name    = $news['title'];
        $path_part->url     = $this->page['alias'].'/doc='.$id;
        $path[] = $path_part;
        $this->page['path'] =   json_encode( $path );
        $this->request->set('tpldata.page.path', $this->page['path']);

        $this->tpl->assign('news', $news);
        $this->request->setTitle($news['title']);
        $this->request->setContent($this->tpl->fetch('news.item'));

        //print $news['protected'].' == '.$this->user->getPermission();
        if ( $news['protected'] > $this->user->getPermission() ) {
            $this->request->setContent('Не достаточно прав доступа');
        }
    }

    /**
     * Отображать список новостей на сайте
     * @param model_News $model
     * @return void
     */
    function getNewsList($model)
    {
        $cat        = $model->findCat( $this->page['link'] );

        $model->setCond( 'news.deleted = 0 AND news.hidden = 0 AND news.cat_id = '.$this->page['link']);

        $count      = $model->count();
        $paging     = $this->paging( $count, $cat['per_page'], $this->page['alias'] );

        try {
            $list       = $model->findAllWithLinks($paging['from'].','.$paging['perpage']);
        } catch ( Exception $e ) {
            print $e->getMessage();
        }

        try {
            //$list       = $model->findAll($paging['from'].','.$paging['perpage']);
        } catch ( Exception $e ) {
            print $e->getMessage();
        }

        $model->clearCond();

        $this->tpl->assign(array(
            'paging'    => $paging,
            'list'      => $list,
            'page'      => $this->page,
            'cat'       => $cat,
        ));
        //printVar($list);

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
        $model  = Model::getModel('model_News');

        if ( $this->request->get('catid', FILTER_VALIDATE_INT) !== false ) {
            $this->newsList( $model );
            return;
        }

        if ( $this->request->get('catedit', FILTER_VALIDATE_INT) !== false ) {
            $this->catEdit( $model );
            return;
        }

        if ( $this->request->get('newsedit', FILTER_VALIDATE_INT) !== false ) {
            $this->newsEdit( $model );
            return;
        }

        $list   = $model->findAllCats();
        $this->tpl->assign(array(
            'list'  => $list,
        ));

        //$this->request->setContent( $this->tpl->fetch('system:news.admin') );
        $this->request->setContent( $this->tpl->fetch('news.catslist') );
        return;
    }

    /**
     * Список новостей для админки
     * @param  $model
     * @return void
     */
    function newsList( $model )
    {
        $cat_id =  $this->request->get('catid', FILTER_SANITIZE_NUMBER_INT);

        $model->setCond("cat_id = {$cat_id}");
        $count  = $model->count();
        $paging = $this->paging($count, 20, 'admin/news/catid='.$cat_id);

        $list = $model->findAll($paging['from'].','.$paging['perpage']);

        $cat    = $model->findCat($cat_id);

        $this->tpl->assign(array(
            'paging'    => $paging,
            'list'      => $list,
            'cat'       => $cat,
        ));

        $this->request->setContent($this->tpl->fetch('news.admin'));
        $model->clearCond();
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

            if ( $form->validate() ) {
                $data   = $form->getData();
                $model->setData( $data );
                $res = $model->update();
                if ( $res ) {
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

        $news   = array();
        if ( $edit ) {
            $news   = $model->find( $edit );
            $form->setData( $news );
        }

        $cat = null;
        if ( isset( $news['cat_id'] ) && $news['cat_id'] ) {
            $cat    = $model->findCat( $news['cat_id'] );
        }
        if ( is_null( $cat ) && $this->request->get('cat', FILTER_VALIDATE_INT) ) {
            $cat    = $model->findCat( $this->request->get('cat', FILTER_SANITIZE_NUMBER_INT) );
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
        $form   = $model->getCategoryForm();

        if ( $form->getPost() )
        {
            $this->setAjax();

            if ( $form->validate() )
            {
                $data   = $form->getData();
                $model->setData( $data );
                $res = $model->updateCat();
                if ( $res )
                {
                    $this->request->addFeedback('Сохранено успешно');
                    if ( ! $data['id'] ) {
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
            $news   = $model->findCat( $edit );
            $form->setData( $news );
        }

        if ( $edit !== false ) {
            $this->tpl->assign(array(
                'form'  => $form,
            ));
            $this->request->setContent($this->tpl->fetch('news.catedit'));
            return;
        }
    }

}