{form form=$form}
<fieldset>
    {$form->htmlFieldWrapped('id')}
    {$form->htmlFieldWrapped('name')}
</fieldset>
<fieldset>
    <legend>{t cat="catalog"}Fields{/t}</legend>
    <table class="table">
        <tr>
            <th>{t cat="catalog"}Type{/t}</th>
            <th>{t cat="catalog"}Name{/t}</th>
            <th>{t cat="catalog"}Unit{/t}</th>
            <th>{t cat="catalog"}Delete{/t}</th>
        </tr>
    {foreach $fields as $field}
        <tr class="field-row" data-field-id="{$field->id}">
            <td>
                <input type="hidden" name="field[id][]" class="form-control field-input-id" value="{$field->id}">
                {html_options name="field[type][]" options=$types selected=$field->type}
            </td>
            <td><input type="text" name="field[name][]" class="form-control input-sm col-xs-4" value="{$field->name}"></td>
            <td><input type="text" name="field[unit][]" class="form-control input-sm col-xs-2" value="{$field->unit}"></td>
            <td>{a controller="prodtype" action="deleteField" id=$field->id class="btn btn-xs btn-danger field-delete"}{t cat="catalog"}Delete{/t}{/a}</td>
        </tr>
    {/foreach}
        <tr class="hide field-pattern">
            <td><input type="hidden" name="field[id][]" class="field-input-id" value="">
                {html_options name="field[type][]" options=$types}</td>
            <td><input type="text" name="field[name][]" class="form-control input-sm col-xs-4" value=""></td>
            <td><input type="text" name="field[unit][]" class="form-control input-sm col-xs-2" value=""></td>
            <td></td>
        </tr>
    </table>
    {a class="btn btn-link field-add"}{icon name="add"} {t cat="catalog"}Add field{/t}{/a}
</fieldset>
{/form}
