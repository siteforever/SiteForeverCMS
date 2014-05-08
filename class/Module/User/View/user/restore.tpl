<h1>{$request->getTitle()}</h1>
{if isset($success)}{alert type='success' msg=$msg}{/if}
{*{if $request->getFeedback()}{alert type='error' msg=$request->getFeedbackString()}{/if}*}

{if isset($form)}
    {alert type='warning' msg=$this->t('user','To recover your password enter your Email address')}
    {$form->html()}
{/if}


