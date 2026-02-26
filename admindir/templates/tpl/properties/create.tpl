<div class="main">
   <div class="left_sidebar padding10">
      {include file="left.tpl"}
   </div>

   <div class="right_content conten">
      {assign var="actType" value="editsm"}
      {if $smarty.request.act == 'add'}
      {assign var="actType" value="addsm"}
      {/if}
      <form name="allsubmit" id="frmEdit"
         action="index.php?do=properties&act={$actType}"
         method="post"
         enctype="multipart/form-data">
         <input type="hidden" name="id" value="{$edit.id|default:''}" />
         <div class="divright">
            <div class="acti2">
               <button type="submit" class="add">
                  <i class="fa fa-save"></i> Save
               </button>
            </div>
         </div>
         <div class="main-content">
            <div class="left100">
               <div class="item">
                  <div class="title">Tiêu đề</div>
                  <div class="info-title">
                     <input type="text" value="{$edit.name_vn|escape:'html':'UTF-8'}"
                        name="name_vn"
                        class="InputText"
                        id="title" />
                  </div>
               </div>

               <div class="item">
                  <div class="title">
                     Show
                     <input type="checkbox" class="CheckBox" name="active" value="active"
                        {if $edit.active eq 1 or $smarty.request.act eq 'add' }checked{/if} />
                  </div>
               </div>
            </div>
         </div>
         <!-- left100 -->
      </form>
   </div>
</div>