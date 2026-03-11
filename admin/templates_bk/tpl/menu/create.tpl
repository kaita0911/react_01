<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>
      <div class="right_content">
         <form id="frmEdit" name="frmEdit" action="index.php?do=menu&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}" method="post" enctype="multipart/form-data">
            <div class="col00">
               <div class="content">
                  <input type="hidden" name="cat" value="{$smarty.request.cid}">
                  <input type="hidden" name="id" value="{$edit.id}">
                  <div class="divright">
                     <div class="acti2"><button class="add" type="submit"><i class="fa fa-save"></i> Save</button></div>
                     <div class="acti2"> <a class="add" href="javascript:history.back()"><i class="fa fa-mail-reply"></i> Trở về</a></div>
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
                     <div class="tab-content {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">

                     </div>
                     {/foreach}
                     <div class="item">
                        <div class="title">Tiêu đề</div>
                        <div class="info-title">
                           <input type="text" name="languages[{$lang.id}][name]" data-lang="{$lang.code}" id="title_{$lang.code}"
                              class="InputText title-input" {if $lang.code=='vi' }required{/if} />
                        </div>
                     </div>
                     <div class="item">
                        <div class="title">URL</div>
                        <div class="info-title">
                           <input type="text" id="slug_{$lang.code}" name="languages[{$lang.id}][unique_key]" data-lang="{$lang.code}"
                              class="InputText slug-input" />
                        </div>
                     </div>
                     <div class="item">
                        <div class="title">Liên kết</div>
                        <div class="option_link">
                           <label class="radio-inline"><input type="radio" id="yes2" name="choose" value="1" checked> Loại bài viết</label>
                           <label class="radio-inline"><input type="radio" id="yes1" name="choose" value="0"> Link</label>
                        </div>
                        <select id="menu" name="menu" class="show">
                           {section name=i loop=$lienket}
                           <option value="{$lienket[i].component_id}" {if $lienket[i].component_id==$edit.comp}selected{/if}>{$lienket[i].name}</option>
                           {/section}
                        </select>
                        <div class="linkngoai hide"><input type="text" id="link" name="link" value="{$edit.link}" class="InputText"></div>
                     </div>
                     <div class="item">
                        <div class="title">Có menu con <input type="checkbox" class="CheckBox" name="menucon" value="menucon" {if $edit.menucon eq 1}checked{/if}></div>
                     </div>
                     <div class="item">
                        <div class="title">Hiển thị <input type="checkbox" class="CheckBox" name="active" value="active" {if $edit.active eq 1 || $smarty.request.act eq 'add' }checked{/if}></div>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
{literal}
<script>
   function toggleLinkOption() {
      const isLink = document.getElementById('yes1').checked;
      document.getElementById('menu').classList.toggle('hide', isLink);
      document.querySelector('.linkngoai').classList.toggle('show', isLink);
      document.querySelector('.linkngoai').classList.toggle('hide', !isLink);
   }
   document.getElementById('yes1').addEventListener('click', toggleLinkOption);
   document.getElementById('yes2').addEventListener('click', toggleLinkOption);
   toggleLinkOption();

   function SubmitFromGo(formId) {
      if (typeof updateAllSlugs === 'function') updateAllSlugs();
      const form = document.getElementById(formId);
      if (form) form.submit();
      else console.error("Không tìm thấy form:", formId);
   }
</script>
{/literal}