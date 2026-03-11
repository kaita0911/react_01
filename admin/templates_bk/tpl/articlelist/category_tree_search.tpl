<option value="{$node.id}" {if $selected|@is_array && in_array($node.id, $selected)}selected{/if}>
    {section name=i loop=$node.level}--&nbsp;{/section}
    {$node.detailsList[$currentLang].name|default:$node.name}
</option>

{if $node.children|@count > 0}
{foreach from=$node.children item=child}
{include file="articlelist/category_tree_search.tpl" node=$child selected=$selected}
{/foreach}
{/if}