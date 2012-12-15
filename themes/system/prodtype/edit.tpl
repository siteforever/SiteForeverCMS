{form form=$form}
<fieldset>
    {$form->htmlFieldWrapped('id')}
    {$form->htmlFieldWrapped('name')}
</fieldset>
<fieldset>
    <legend>{t cat="catalog"}Fields{/t}</legend>
    <table>
        <tr>
            <th>Тип</th>
            <th>Наименование</th>
            <th>Ед. изм.</th>
            <th>Удалить</th>
        </tr>
    {foreach $fields as $field}
        <tr class="field-row" data-field-id="{$field->id}">
            <td><input type="hidden" name="field[id][]" class="field-input-id" value="{$field->id}">
                {html_options name="field[type][]" options=$types selected=$field->type}</td>
            <td><input type="text" name="field[name][]" class="input-xlarge" value="{$field->name}"></td>
            <td><input type="text" name="field[unit][]" class="input-mini" value="{$field->unit}"></td>
            <td>{a controller="prodtype" action="deleteField" id=$field->id class="field-delete"}Удалить{/a}</td>
        </tr>
    {/foreach}
        <tr class="hide field-pattern">
            <td><input type="hidden" name="field[id][]" class="field-input-id" value="">
                {html_options name="field[type][]" options=$types}</td>
            <td><input type="text" name="field[name][]" class="input-xlarge" value=""></td>
            <td><input type="text" name="field[unit][]" class="input-mini" value=""></td>
            <td></td>
        </tr>
    </table>
    {a class="btn field-add"}{icon name="add"} {t cat="catalog"}Add field{/t}{/a}
</fieldset>
{/form}