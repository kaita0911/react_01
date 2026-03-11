<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>
      <div class="right_content conten">
         <div class="tbtitle2 link00">
            <form id="f" method="post" action="index.php?do=infos&act=dellist&cid=1">
               <table class="br1" style="border-bottom:0" width="100%" cellspacing="0" cellpadding="0">
                  <thead>
                     <tr>
                        <th class="width-order">Thứ tự</th>
                        <th class="width-ttl">Tiêu đề</th>
                        {if $smarty.session.admin_artseed_username == 'kaita'}
                        <th class="width-show">Show</th>
                        <th class="width-show">Kích hoạt</th>
                        {/if}
                        <th class="width-action">Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     {foreach from=$view key=index item=item}
                     <tr>
                        <td align="center" class="brbottom">{$index+1+$number}</td>
                        <td class="paleft brbottom linkblack">{$item.name_vn|escape}</td>
                        {if $smarty.session.admin_artseed_username == 'kaita'}
                        <td align="center" class="brbottom">
                          
                             
                           <label class="toggle btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.active}"
                              data-column="active"
                              data-table="infos">
                           <input type="checkbox" {if $item.active == 1}checked{/if}>
                           <span class="track"></span>
                           </label>
                        </td>
                        <td align="center" class="brbottom">
                           {if $item.id == 2
                           or $item.id == 12
                           or $item.id == 13
                           or $item.id == 14
                           or $item.id == 15
                           or $item.id == 16
                           or $item.id == 19
                           or $item.id == 20
                           or $item.id == 21
                           or $item.id == 22
                           or $item.id == 23
                           or $item.id == 24
                           or $item.id == 25
                           or $item.id == 26
                           or $item.id == 27
                           or $item.id == 28
                           or $item.id == 30}

                           
                           <label class="toggle btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.open}"
                              data-column="open"
                              data-table="infos">
                           <input type="checkbox" {if $item.open == 1}checked{/if}>
                           <span class="track"></span>
                           </label>
                           {/if}
                        </td>
                        {/if}

                        <td align=" center" class="brbottom">
                           <div class="flex-btn">
                              <a class="act-btn btnEdit" href="index.php?do=infos&act=edit&comp={$smarty.request.comp}&id={$item.id}" title="Sửa">
                                 <i class="fa fa-edit"></i>
                              </a>
                           </div>
                        </td>
                     </tr>
                     {/foreach}
                  </tbody>
               </table>
            </form>
         </div>
      </div>
   </div>
</div>