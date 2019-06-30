{function name=form_start class="" domain="messages"}{strip}
    {$attr = $form->vars.attr}
    {$class = [$class, $attr.class]|join:" "|trim}
    <form name="form_{$attr.name}" id="form_{$attr.id}" role="form" method="{$attr.method}"
            {if not $attr.validate} novalidate="novalidate"{/if}
            {if $class} class="{$class}"{/if}
            {if $attr.enctype} enctype="{$attr.enctype}"{/if}
            {if $attr.action} action="{$attr.action}"{/if}>
{/strip}{/function}{*


*}{function name=form_end}</form>{$form = $form->setRendered()}{/function}{*


*}{function name=form_label class="" domain="messages"}
{strip}
    {$attr = $form->vars.attr}
    <label for="{$attr.id}"{if $class} class="{$class}"{/if}>
        {$attr.label|trans:[]:$domain|ucfirst}{if $attr.required}&nbsp;<b>*</b>{/if}
    </label>
{/strip}
{/function}{*


*}{function name=form_errors domain="messages"}{strip}
    {$attr = $form->vars.attr}
    {if $attr.errors}
    <ul class="errors">
    {foreach $attr.errors as $msg}
        <li>{$msg|trans:[]:$domain}</li>
    {/foreach}
    </ul>
    {/if}
    {if not empty($attr.msg)}
    <div class="help-block" style="clear: both">{$attr.msg|trans:["%label%"=>$attr.label|trans]:$domain}</div>
    {/if}
{/strip}{/function}{*


*}{function name=form_input class="form-control" type="" domain="messages"}{strip}
    {$attr = $form->vars.attr}
    <input{call form_row_attr class=$class form=$form type=$type}>
    {$form = $form->setRendered()}
{/strip}{/function}{*


*}{function name=form_row_attr type="" class=""}{strip}
    {$attr = $form->vars.attr}
    {if trim($attr.id)} id="{$attr.id}" {/if}
    {if not $type}{$type = $attr.type}{/if}
    {if in_array($attr.type, ['int', 'float', 'date']) and not $attr.hidden}
        {$class = [$class, $attr.type]|join:' '|trim}
        {$type = 'text'}
    {/if}
    {if trim($type)}type="{$type}" {/if}

    {if 'hidden' == $type}
        {$class = []}
    {else}
        {$class = [$class, $attr.class]}
    {/if}

    {if count(array_filter($class))}class="{$class|join:" "|trim}" {/if}

    name="{$form->parent->vars.attr.name}[{$attr.name}]"

    {if isset($attr.multiple) && $attr.multiple} multiple="multiple"{/if}
    {if isset($attr.formenctype)} formenctype="{$attr.formenctype}"{/if}
    {if $attr.required} required="required"{/if}
    {if $attr.readonly} readonly="readonly"{/if}
    {if $attr.disabled} disabled="disabled"{/if}
    {if ! $attr.autocomplete} autocomplete="off"{/if}
    {if $attr.value and 'password' != $attr.type and 'textarea' != $attr.type} value="{$attr.value|escape}"{/if}
    {if !empty($attr.placeholder)} placeholder="{$attr.placeholder}"{/if}
{/strip}{/function}{*


*}{function name=form_row class="form-control" domain="messages" buttons=true}{strip}
{if "" == $class}{$class = "form-control"}{/if}
{if not $form->isRendered()}
        {$attr = $form->vars.attr}
    {if $attr.hidden}
        <input{call form_row_attr form=$form type="hidden"}>
        {$form = $form->setRendered()}
    {else}
        <div class="form-group{if $attr.error} has-error{/if}" data-field-name="{$attr.name}">
        {if in_array($attr.type, ['int', 'intfield', 'float', 'floatfield', 'date'])}
            {form_label form=$form class="control-label" domain=$domain}
            <div class="row">
                <div class="col-xs-3">{form_input form=$form class=$class}</div>
            </div>
            {form_errors form=$form}
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif in_array($attr.type, ['text', 'password'])}
            {form_label form=$form class="control-label" domain=$domain}
            {form_input form=$form class=$class}
            {form_errors form=$form}
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif in_array($attr.type, ['datetime'])}
            {form_label form=$form class="control-label" domain=$domain}
            {form_input form=$form class=$class type="text"}
            {form_errors form=$form}
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif in_array($attr.type, ['textarea'])}
            {form_label form=$form class="control-label" domain=$domain}
            <textarea{call form_row_attr class=$class form=$form}>{$attr.value}</textarea>
            {form_errors form=$form}
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif in_array($attr.type, ['checkbox'])}
            <div class="col-sm-12">
                <input type="hidden" name="{$form->parent->vars.attr.name}[{$attr.name}]" value="0">
                <label for="{$attr.id}" class="checkbox col-xs-6">
                    <input type="checkbox" id="{$attr.id}" name="{$form->parent->vars.attr.name}[{$attr.name}]" value="1" {if $attr.value} checked="checked"{/if}>
                    {$attr.label|trans:[]:$domain|ucfirst}{if $attr.required}&nbsp;<b>*</b>{/if}
                </label>
            </div>
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif in_array($attr.type, ['radio'])}
            {form_label form=$form class="control-label" domain=$domain}
            {foreach $attr.variants as $title}
                <label>
                    <input type="radio" name="{$form->parent->vars.attr.name}[{$attr.name}]" value="{$title@key}" {if $title@key == $attr.value} checked="checked" {/if}> <span>{$title|trans:[]:$domain}</span>
                </label>&nbsp;
            {/foreach}
            {form_errors form=$form}
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif in_array($attr.type, ['select'])}
            {form_label form=$form class="control-label" domain=$domain}
            <select name="{$form->parent->vars.attr.name}[{$attr.name}]"{if trim($attr.id)} class="{$class}" id="{$attr.id}" {/if}{if $attr.multiple} multiple="multiple"{/if}>
            {foreach $attr.variants as $title}
                <option value="{$title@key}"{if $title@key == $attr.value} selected="selected"{/if}>{$title|trans:[]:$domain}</option>
            {/foreach}
            </select>
            {form_errors form=$form}
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif in_array($attr.type, ['captcha'])}
            <div class="g-recaptcha" data-sitekey="{$captcha.html_key}"></div>
            {form_input form=$form type="hidden" class=$class value="code"}
            {form_errors form=$form}
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif 'file' == $attr.type}
            {form_label form=$form class="control-label" domain=$domain}
            {form_input form=$form class=$class}
            {form_errors form=$form}
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
        {elseif in_array($attr.type, ['button','reset','submit'])}
            {if $buttons}
            {$class = ["btn btn-default", $attr.class]}
            <input type="{$attr.type}"{if $class} class="{$class|join:" "|trim}"{/if} name="{$form->parent->vars.attr.name}[{$attr.name}]"{if trim($attr.id)} id="{$attr.id}" {/if} value="{$attr.value|trans:[]:$domain}">
            {if $attr.notice}<div class="help-block"><small>{$attr.notice}</small></div>{/if}
            {$form = $form->setRendered()}
            {/if}
        {else}
            Unresolved type
            {var_dump($form->vars)}
        {/if}
        </div>
    {/if}
{/if}
{/strip}{/function}{*


*}{function name=form_rest class="" domain="messages" buttons=true}{strip}
    {$attr = $form->vars.attr}
    {foreach $form->children as $child}
        {if not $form->isRendered()}
            {call form_row form=$child domain=$domain buttons=$buttons}
        {/if}
    {/foreach}
{/strip}{/function}{*


*}{function name=form_full class="" domain="messages" hint=true buttons=true}
    {$attr = $form->vars.attr}
    {form_start form=$form class=$class domain=$domain}
    {*{form_errors form=$form}*}
    {call form_rest form=$form domain=$domain buttons=false}
    {if $hint}<p><b>*</b> &ndash; {'smarty.form.hint'|trans:[]:'smarty'}</p>{/if}
    {call form_rest form=$form domain=$domain buttons=$buttons}
    {form_end}
{/function}
