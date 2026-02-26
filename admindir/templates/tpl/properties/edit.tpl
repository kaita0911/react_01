<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content">
         <form name="allsubmit" id="frmEdit"
            action="index.php?do=properties&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}"
            method="post"
            enctype="multipart/form-data">
            <input type="hidden" name="id" value="{$edit.id}" />

            <!-- Save button -->
            <div class="divright">
               <div class="acti2">
                  <button type="submit" class="add">
                     <i class="fa fa-save"></i> Save
                  </button>
               </div>
            </div>
            <div class="main-content">
               <!-- Left column -->
               <div class="left100">
                  <div class="item">
                     <div class="title">Tiêu đề</div>
                     <div class="info-title">
                        <input type="text" name="name_vn" id="title"
                           class="InputText"
                           value="{$edit.name_vn|escape:'html':'UTF-8'}" />
                     </div>
                  </div>

                  <div class="item">
                     <div class="title">
                        Show
                        <input type="checkbox" name="active" class="CheckBox" value="active"
                           {if $edit.active eq 1 || $smarty.request.act eq 'add' }checked{/if} />
                     </div>
                  </div>
               </div>
            </div>

         </form>

      </div>
   </div>
</div>