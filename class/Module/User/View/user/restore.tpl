{if isset($success)}{alert type='success' msg=$msg}{/if}

<div class="row-fluid">
    <div class="span6">

        {if $request->getFeedback()}{alert type='error' msg=$request->getFeedbackString()}{/if}

        {if isset($form)}
            {alert type='info' msg=t('user','To recover your password enter your Email address')}
            {$form->html()}
        {/if}
    </div>
</div>


