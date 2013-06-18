{*<h2><a {href url="user/admin"}>{icon name="arrow_left" title="Пользователи"}</a> {$title}</h2>*}
{form form=$form}
{tabs main=$this->t('Main') advanced=$this->t('Advanced') company=$this->t('Company')}
{tab name="main" active="1"}
    {$form->htmlFieldWrapped('id')}
    <div class="row-fluid">
        <div class="span6">
            {$form->htmlFieldWrapped('login')}
            {$form->htmlFieldWrapped('password')}
            {$form->htmlFieldWrapped('fname')}
            {$form->htmlFieldWrapped('lname')}
        </div>
        <div class="span6">
            {$form->htmlFieldWrapped('email')}
            {$form->htmlFieldWrapped('phone')}
            {$form->htmlFieldWrapped('address')}
        </div>
    </div>
{/tab}
{tab name="advanced"}
    {$form->htmlFieldWrapped('status')}
    {$form->htmlFieldWrapped('date')}
    {$form->htmlFieldWrapped('last')}
    {$form->htmlFieldWrapped('perm')}
{/tab}
{tab name="company"}
    {$form->htmlFieldWrapped('name')}
    {$form->htmlFieldWrapped('fax')}
    {$form->htmlFieldWrapped('inn')}
    {$form->htmlFieldWrapped('kpp')}
{/tab}
{/tabs}
{/form}
