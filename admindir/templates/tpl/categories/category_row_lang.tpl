<tr data-id="{$category.id}">
    <td align="center">
        <input class="c-item" type="checkbox" name="cid[]" value="{$category.id}">
    </td>
    <td align="center">
        <input type="text" class="numInput" value="{$category.num}">
    </td>

    {if $tinhnang.hinhdanhmuc == 1}
    <td align="center" class="img-table">
        {if $category.img_vn}
        <div class="c-imgs">
            <img src="/{$category.img_vn}?width=60&height=60&mode=scale" alt="img">
        </div>
        {/if}
    </td>
    {/if}

    <td align="left">


        <div class="tab-mirror">
            {foreach from=$languages item=lang}
            {assign var=detail value=null}
            {foreach from=$category.detailsList item=ad}
            {if $ad.languageid == $lang.id}
            {assign var=detail value=$ad}
            {/if}
            {/foreach}

            <span class="tab c-name {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">
                <span class="view-text"> {section name=i loop=$level}&nbsp;--{/section} {$detail.name|default:''|escape:'html':'UTF-8'}</span>
                <input type="text" class="edit-input form-control" value="{$detail.name|default:''|escape:'html':'UTF-8'}" style="display:none;">
            </span>
            {/foreach}
        </div>
    </td>

    {if $tinhnang.danhmuchome == 1}
    <td align="center">
        
        <label class="toggle btn_toggle"
            data-id="{$category.id}" 
            data-active="{$category.home}" 
            data-column="home" 
            data-table="categories">
            <input type="checkbox" {if $category.home == 1}checked{/if}>
            <span class="track"></span>
        </label>
    </td>
    {/if}

    <td align="center">
       
        <label class="toggle btn_toggle"
            data-id="{$category.id}" 
            data-active="{$category.active}" 
            data-column="active" 
            data-table="categories">
            <input type="checkbox" {if $category.active == 1}checked{/if}>
            <span class="track"></span>
        </label>
    </td>

    <td align="center">
        <div class="flex-btn tab-mirror">

            {foreach from=$languages item=lang}
            {assign var=detail value=null}
            {foreach from=$category.detailsList item=ad}
            {if $ad.languageid == $lang.id}
            {assign var=detail value=$ad}
            {/if}
            {/foreach}

            <a data-lang="{$lang.id}" class="tab act-btn btnView {if $lang.id==$currentLang}active{/if}"
                href="{$web_base_url}/{$detail.unique_key}/" target="_blank" title="Xem nhanh">
                <i class="fa fa-eye"></i>
            </a>
            {/foreach}

            <a title="Chỉnh sửa" class="act-btn btnEdit" href="index.php?do=categories&act=edit&id={$category.id}&comp={$smarty.request.comp}">
                <i class="fa fa-edit"></i>
            </a>
            <button title="Làm mới" type="button" class="act-btn btnUpdateNum" data-id="{$category.id}" data-comp="{$smarty.request.comp}">
                <i class="fa fa-refresh"></i>
            </button>
            <button title="Xoá" type="button" class="act-btn btnDeleteRow" data-id="{$category.id}">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </td>
</tr>

{if $category.children|@count > 0}
{foreach from=$category.children item=child}
{include file="categories/category_row_lang.tpl" category=$child level=$level+1 languages=$languages currentLang=$currentLang}
{/foreach}
{/if}