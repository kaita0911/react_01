<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>
      <div class="right_content">
         <form id="formId"
            action="index.php?do=menu&act={if $smarty.request.act=='add'}addsm{else}editsm{/if}&comp={$smarty.request.comp}"
            method="post"
            enctype="multipart/form-data">
            <input type="hidden" name="id" value="{$edit.id|default:''}">

            <div class="divright">
               <div class="acti2">
                  <button class="add" type="submit">
                     <i class="fa fa-save"></i> Save
                  </button>
               </div>
               <div class="acti2">
                  <a class="add" href="javascript:history.back()">
                     <i class="fa fa-mail-reply"></i> Trở về
                  </a>
               </div>
            </div>

            <div class="main-content">
               {if $languages|@count > 1}
               <ul class="tab-list">
                  {foreach from=$languages item=lang}
                  <li class="tab {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">{$lang.name}</li>
                  {/foreach}
               </ul>
               {/if}
               {foreach from=$languages item=lang}
               {assign var=detail value=null}
               {foreach from=$menuDetail item=ad}
               {if $ad.languageid == $lang.id}
               {assign var=detail value=$ad}
               {/if}
               {/foreach}
               <div class="tab-content {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">
                  <div class="item">
                     <div class="title">Tiêu đề</div>
                     <div class="info-title">
                        <input type="text" name="languages[{$lang.id}][name]" data-lang="{$lang.code}" id="title_{$lang.code}" class="InputText title-input"
                           value="{$detail.name|escape:'html':'UTF-8'}" {if $lang.code=='vi' }required{/if} />
                     </div>
                  </div>

                  <div class="item">
                     <div class="title">URL</div>
                     <div class="info-title">
                        <input type="text" id="slug_{$lang.code}" name="languages[{$lang.id}][unique_key]" data-lang="{$lang.code}"
                           value="{$detail.unique_key}"
                           class="InputText slug-input" />
                     </div>
                  </div>
               </div>
               {/foreach}

              
               <div class="item">
                  <div class="title">Liên kết</div>
                  <div class="option_link">
                     <label>
                        <input type="radio" name="choose" value="1" {if $edit.choose==1}checked{/if}>
                        Loại bài viết
                     </label>
                     <label>
                        <input type="radio" name="choose" value="0" {if $edit.choose==0}checked{/if}>
                        Link
                     </label>
                  </div>

                  <select id="menu" name="menu" class="show">
                     {foreach from=$lienket item=link}
                     <option value="{$link.component_id}" {if $link.component_id==$edit.comp}selected{/if}>
                        {$link.name|escape}
                     </option>
                     {/foreach}
                  </select>

                  <input type="text"
                     id="link"
                     name="link"
                     class="linkngoai hide"
                     value="{$edit.link_out|default:''|escape:'html':'UTF-8'}">
               </div>

               <div class="item">
               <div class="title">
                     <input type="checkbox"
                        name="menucon"
                        value="menucon"
                        class="CheckBox"
                        {if $edit.has_sub==1}checked{/if}>
                     Có menucon
                        </div>
                  <div class="title">
                     <input type="checkbox"
                        name="active"
                        value="active"
                        class="CheckBox"
                        {if $edit.active==1 || $smarty.request.act=='add' }checked{/if}>
                     Show
                  </div>
                  <div class="item">
                  <div class="title">Thứ tự<input type="text"
                        name="num"
                        class="InputNum num-order"
                        value="{$edit.num|default:0|escape:'html':'UTF-8'}"></div>
                 
                  </div>

               </div>
            </div>
         </form>
      </div>
   </div>
</div>
{literal}
<script>
   document.addEventListener("DOMContentLoaded", function() {
      const toggleMenuLink = () => {
         const yes1 = document.querySelector("#yes1");
         const menu = document.getElementById("menu");
         const link = document.querySelector(".linkngoai");
         if (yes1 && yes1.checked) {
            menu.classList.add("hide");
            menu.classList.remove("show");
            link.classList.add("show");
            link.classList.remove("hide");
         } else {
            menu.classList.add("show");
            menu.classList.remove("hide");
            link.classList.add("hide");
            link.classList.remove("show");
         }
      };
      document.querySelectorAll('input[name="choose"]').forEach(el => {
         el.addEventListener("change", toggleMenuLink);
      });
      toggleMenuLink();
   });
</script>
{/literal}