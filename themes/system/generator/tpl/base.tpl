<?php
/**
{foreach from=$fields item="f"}
 * @property {$f.vartype} {$f.name}
{/foreach}
 */
abstract class Data_Base_{$name} extends Data_Object
{
}