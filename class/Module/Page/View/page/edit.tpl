
{form form=$form}

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tabs-1" data-toggle="tab">{t cat="page"}Main settings{/t}</a></li>
        <li><a href="#tabs-2" data-toggle="tab">{t cat="page"}Notice{/t}</a></li>
        <li><a href="#tabs-3" data-toggle="tab">{t cat="page"}Content{/t}</a></li>
    </ul>

    {$form->htmlFieldWrapped('id')}
    <div class="tab-content">
        <div class="tab-pane active" id="tabs-1">
            <fieldset>
                <legend>{"System"|lang:"page"}</legend>
                {$form->htmlFieldWrapped('name')}
                {$form->htmlFieldWrapped('parent')}
                {$form->htmlFieldWrapped('template')}
                {$form->htmlFieldWrapped('alias')}
                {$form->htmlFieldWrapped('date')}
                {$form->htmlFieldWrapped('update')}
                {$form->htmlFieldWrapped('pos')}
                {$form->htmlFieldWrapped('controller')}
                {$form->htmlFieldWrapped('link')}
                {$form->htmlFieldWrapped('action')}
                {$form->htmlFieldWrapped('sort')}
            </fieldset>
            <fieldset>
                <legend>{"Seo"|lang:"page"}</legend>
                {$form->htmlFieldWrapped('title')}
                {$form->htmlFieldWrapped('keywords')}
                {$form->htmlFieldWrapped('description')}
                {$form->htmlFieldWrapped('nofollow')}
            </fieldset>
            <fieldset>
                <legend>{"Images"|lang:"page"}</legend>
                {$form->htmlFieldWrapped('thumb')}
                {$form->htmlFieldWrapped('image')}
            </fieldset>
            <fieldset>
                <legend>{"Constraints"|lang:"page"}</legend>
                {$form->htmlFieldWrapped('author')}
                <div class="control-group">
                    <label class="control-label">{$form->htmlFieldLabel('hidden')}</label>
                    <div class="controls">
                        {$form->htmlField('hidden')}
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">{$form->htmlFieldLabel('protected')}</label>
                    <div class="controls">
                        {$form->htmlField('protected')}
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">{$form->htmlFieldLabel('system')}</label>
                    <div class="controls">
                        {$form->htmlField('system')}
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="tab-pane" id="tabs-2">
            {$form->htmlField('notice')}
        </div>

        <div class="tab-pane" id="tabs-3">
            {$form->htmlField('content')}
        </div>
</div>
{/form}


