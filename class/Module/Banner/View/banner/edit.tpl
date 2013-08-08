{form form=$form}
{tabs page=$this->t('Main') content=$this->t('page','Content')}
{tab name="page" active=1}
    {$form->htmlFieldWrapped('id')}
    {$form->htmlFieldWrapped('cat_id')}
    {$form->htmlFieldWrapped('name')}
    {$form->htmlFieldWrapped('url')}
    {$form->htmlFieldWrapped('target')}
{/tab}
{tab name="content"}
    {$form->htmlField('content')}
{/tab}
{/tabs}
{/form}
