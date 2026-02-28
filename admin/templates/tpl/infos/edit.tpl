<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content pad">
         <form id="frmEdit"
            action="index.php?do=infos&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}&comp={$smarty.request.comp}"
            method="post"
            enctype="multipart/form-data">

            <div class="btnseo">
               <input type="hidden" name="id" value="{$edit.id}" />
               <button type="submit"><i class="fa fa-save"></i> Save</button>
               <a href="javascript:history.back()"><i class="fa fa-mail-reply"></i> Trở về</a>
            </div>

            <div class="item">
               <div class="title">Tiêu đề</div>
               <div class="info-title">
                  <input type="text" name="name_vn" class="InputText" id="title"
                     value="{$edit.name_vn|escape:'html':'UTF-8'}" onkeyup="ChangeToSlug();">
               </div>
            </div>

            {* ================== PHẦN THEO ID ================== *}
            {if $edit.id eq 1}
            {include file='infos/edit_logo.tpl'}
            {elseif $edit.id eq 2}
            {include file='infos/edit_domain.tpl'}
            {elseif $edit.id eq 3}
            {include file='infos/edit_googlemap.tpl'}
            {elseif $edit.id eq 4}
            {include file='infos/edit_content.tpl'}
            {elseif $edit.id eq 5}
            {include file='infos/edit_hotline.tpl'}
            {elseif $edit.id eq 6}
            {include file='infos/edit_email.tpl'}
            {elseif $edit.id eq 7}
            {include file='infos/edit_social.tpl'}
            {elseif $edit.id eq 12}
            {include file='infos/edit_cart.tpl'}
            {elseif $edit.id eq 13}
            {include file='infos/edit_time.tpl'}
            {elseif $edit.id eq 15}
            {include file='infos/edit_seo.tpl'}
            {elseif $edit.id eq 29}
            {include file='infos/edit_pagination.tpl'}
            {else}
            {include file='infos/edit_default.tpl'}
            {/if}

            {* ================== CHECKBOX CHUNG ================== *}
            <div class="item">
               <div class="title">Hiển thị<input type="checkbox" class="CheckBox" name="active" value="active"
               {if $edit.active eq 1 || $smarty.request.act eq 'add' }checked{/if}></div>
               
            </div>
         </form>
      </div>
   </div>
</div>