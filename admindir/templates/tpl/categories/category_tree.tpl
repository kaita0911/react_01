<li>
    <label>
        <input type="checkbox"
            name="parentids[]"
            value="{$node.id}"
            data-parent="{$node.parent_id|default:0}"
            {if $selected|@is_array && in_array($node.id, $selected)}checked="checked" {/if}>
        {section name=i loop=$node.level}--&nbsp;{/section}
        {$node.detailsList[$currentLang].name|default:$node.name}
    </label>

    {if $node.children|@count > 0}
    <ul>
        {foreach from=$node.children item=child}
        {include file="categories/category_tree.tpl" node=$child selected=$selected currentLang=$currentLang languages=$languages level=$child.level}
        {/foreach}
    </ul>
    {/if}
</li>