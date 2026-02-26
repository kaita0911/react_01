<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content conten">
         <form name="allsubmit" id="frmEdit" method="post" enctype="multipart/form-data"
            action="index.php?do=categories&act={if $smarty.request.act == 'add'}addsm{else}editsm{/if}&comp={$smarty.request.comp}&id={$smarty.request.id}">
            <input type="hidden" name="id" value="{$category.id}" />
            <input type="hidden" name="comp" value="{$smarty.request.comp|default:0}">
            <div class="divright">
               <div class="acti2">
                  <button type="submit" class="add">
                     <i class="fa fa-save"></i> Save
                  </button>
               </div>
               <div class="acti2">
                  <a class="add" href="javascript:history.go(-1)"><i class="fa fa-mail-reply"></i> Trở về</a>
               </div>
            </div>
            <div class="main-content">
               <div class="wrap-main">
                  <div class="left100">
                     {if $languages|@count > 1}
                     <ul class="tab-list">
                        {foreach from=$languages item=lang}
                        <li class="tab {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">{$lang.name}</li>
                        {/foreach}
                     </ul>
                     {/if}
                     {foreach from=$languages item=lang}
                     {assign var=detail value=null}
                     {foreach from=$categoryDetail item=ad}
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
                        <div class="item">
                           <div class="title">Mô tả ngắn</div>
                           <div class="meta">
                              <textarea id="short_{$lang.id}" name="languages[{$lang.id}][short]">{$detail.short}</textarea>
                           </div>
                        </div>
                        <div class="item">
                           <div class="title">Mô tả chi tiết</div>
                           <div class="meta">
                              <textarea class="ckeditor" id="content_{$lang.id}]" name="languages[{$lang.id}][content]">{$detail.content}</textarea>
                           </div>
                        </div>

                        <div class="item">
                           <div class="title">Meta Keywords</div>

                           <div class="tags-group" data-lang="{$lang.code}">
                              <input type="hidden" name="languages[{$lang.id}][tags]" class="tagsInput" data-lang="{$lang.code}" value="{$detail.tagsJson|escape:'html'}">
                              <div class="tagContainer" data-lang="{$lang.code}">
                                 <div class="tagsWrapper" data-lang="{$lang.code}"></div>
                                 <input type="text" class="tagInput InputText" data-lang="{$lang.code}" placeholder="Nhập tag...">
                              </div>
                           </div>
                        </div>

                        <div class="item">
                           <div class="title">Meta Descriptions</div>
                           <div class="meta">
                              <textarea name="languages[{$lang.id}][des]" class="InputTextarea" id="inputDesc">{$detail.des}</textarea>
                              <span id="showNumDesc" style="color:#ed1b24;">0</span>
                           </div>
                        </div>
                     </div>
                     {/foreach}
                  </div>
                  <div class="right100">
                     <div class="item">
                        <div class="title">Danh mục</div>
                        <div class="selectlist tab-mirror">
                           {foreach from=$languages item=lang}
                           <ul class="tab category-tree {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">
                              {foreach from=$categories item=node}
                              {include file="categories/category_tree.tpl"
                              node=$node
                              selected=$selected
                              level=0
                              currentLang=$lang.id}
                              {/foreach}
                           </ul>
                           {/foreach}
                        </div>
                     </div>
                     {if $tinhnang.hinhdanhmuc == 1}
                     <div class="item">
                        <div class="title">Hình ảnh</div>

                        <div class="info-title">
                           {if $category.img_vn neq ""}
                           <!-- Ảnh cũ -->
                           <img id="current-img" src="/{$category.img_vn}?width=60&height=60&mode=scale" height="60" style="display:block; margin-bottom:8px;">
                           {/if}

                           <label for="img_vn" class="custom-upload">
                              <i class="fa fa-upload"></i> Upload image
                           </label>
                           <!-- Input chọn ảnh -->
                           <input type="file"
                              accept="image/png,image/gif,image/jpeg,image/jpg"
                              name="img_vn"
                              id="img_vn" class="img-thumb-input">

                           <!-- Preview ảnh mới -->
                           <p class="previewimg" style="margin-top:8px;">
                              <img id="preview-img" style="max-height:150px; display:none;">
                           </p>
                        </div>
                     </div>

                     {/if}
                     <!-- <div class="item">
                        <div class="title">Link xem thêm</div>
                        <input type="text" name="link" class="InputText" value="{$category.link}">
                     </div> -->
                     <div class="item">
                        <div class="title">Thứ Tự
                           <input type="text" name="num" value="{$category.num}" class="InputNum" />
                        </div>
                     </div>

                     <div class="item">
                        <div class="title">Show
                           <input type="checkbox" class="CheckBox" name="active" value="active"
                              {if $category.active eq 1 || $smarty.request.act=='add' }checked{/if} />
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>