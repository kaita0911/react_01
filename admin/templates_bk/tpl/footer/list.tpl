<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content">
         <div class="divright">
            <div class="acti2">
               <a class="add" href="index.php?do=footer&act=add">
                  <i class="fa fa-plus-circle"></i> Thêm mới
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
            <form class="form-all" method="post" action="">
               <table class="br1">
                  <thead>
                     <tr>
                        <th align="left" class="width-ttl">
                           <strong>Tiêu đề</strong>
                        </th>
                        <th class="width-action" align="center">
                           <strong>Action</strong>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     {foreach from=$view item=item}

                     <tr data-id="{$item.id}">
                        <td align="left" class="tab-mirror">
                           {foreach from=$languages item=lang}
                           {assign var=detail value=null}
                           {foreach from=$item.details item=ad}
                           {if $ad.languageid == $lang.id}
                           {assign var=detail value=$ad}
                           {/if}
                           {/foreach}
                           <span class="tab {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">
                              {$detail.name}
                           </span>
                           {/foreach}
                        </td>
                        <td align="center">
                           <div class="flex-btn">
                              <a title="Chỉnh sửa" class="act-btn btnEdit" href="index.php?do=footer&act=edit&id={$item.id}">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <button title="Xoá" type="button" class="act-btn btnDeleteRow" data-id="{$item.id}"> <i class="fa fa-trash"></i> </button>
                           </div>
                        </td>
                     </tr>
                     {/foreach}
                  </tbody>

               </table>
            </form>
         </div>

         <div class="clear"></div>
      </div>
   </div>
</div>