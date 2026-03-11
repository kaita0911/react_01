<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>
      <div class="right_content">
         <form name="allsubmit" id="frmEdit" action="index.php?do=component&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}" method="post" enctype="multipart/form-data">

            <div class="divright">
               <div class="acti2">
                  <button type="submit" class="add"><i class="fa fa-save"></i> Save</button>
               </div>

            </div>
            <div class="main-content">
               <fieldset>
                  <legend>Link</legend>
                  {section name=i loop=$languages}
                  <div class="item">
                     <div class="title">Tiêu đề {$languages[i].code}</div>
                     <div class="info-title">
                        <input type="text" name="name_{$languages[i].id}" class="InputText" id="title_{$languages[i].id}" value="{$edit.name|escape:'html':'UTF-8'}" onkeyup="ChangeToSlug{$languages[i].id}();" />
                     </div>
                  </div>
                  <div class="item">
                     <div class="title">URL {$languages[i].code}</div>
                     <div class="info-title">
                        <input type="text" name="unique_key_{$languages[i].id}" class="InputText" id="slug{$languages[i].id}" value="{$edit.unique_key}" />
                     </div>
                  </div>
                  {/section}
               </fieldset>

               <fieldset>
                  <legend>Thông tin cơ bản</legend>
                  <!-- ===== Basic Info ===== -->
                  <div class="box-feature">
                     {php}
                     $checkboxes = array(
                     array('name'=>'nhomcon','label'=>'Nhóm con'),
                     array('name'=>'hinhanh','label'=>'Hình ảnh'),
                     array('name'=>'short','label'=>'Mô tả vắn tắt'),
                     array('name'=>'des','label'=>'Mô tả chi tiết'),
                     array('name'=>'metatag','label'=>'Meta tag'),
                     array('name'=>'nhieuhinh','label'=>'Nhiều hình'),
                     array('name'=>'masp','label'=>'Mã sản phẩm'),
                     array('name'=>'price','label'=>'Có giá'),
                     array('name'=>'priceold','label'=>'Giá cũ'),
                     array('name'=>'mausac','label'=>'Màu sắc'),
                     array('name'=>'kichthuoc','label'=>'Kích thước'),
                     array('name'=>'voucher','label'=>'Mã voucher'),
                     array('name'=>'phiship','label'=>'Phí ship'),
                     array('name'=>'new','label'=>'Mới'),
                     array('name'=>'hot','label'=>'Nổi bật'),
                     array('name'=>'mostview','label'=>'Xem nhiều'),
                     array('name'=>'viewed','label'=>'Đã xem')
                     );
                     $this->assign('checkboxes', $checkboxes);
                     {/php}

                     {foreach from=$checkboxes item=cb}
                     <div class="item">
                        <div class="title">
                           {$cb.label}
                           <input type="checkbox" class="CheckBox" name="{$cb.name}" value="{$cb.name}" {if $edit[$cb.name] eq 1}checked{/if} />
                        </div>
                     </div>
                     {/foreach}
                  </div>
               </fieldset>
               <fieldset>
                  <legend>Thuộc tính chung module</legend>
                  <!-- ===== Module Attributes ===== -->
                  <div class="box-feature">
                     <div class="item">
                        <div class="title">
                           Hình ảnh <input type="checkbox" name="hinhmodule" value="hinhmodule" {if $edit.hinhmodule eq 1}checked{/if} />
                        </div>
                     </div>
                     <div class="item">
                        <div class="title">
                           Mô tả chung <input type="checkbox" name="motamodule" value="motamodule" {if $edit.motamodule eq 1}checked{/if} />
                        </div>
                     </div>
                  </div>
               </fieldset>
               <fieldset>
                  <legend>Thuộc tính property</legend>
                  <!-- ===== Properties ===== -->
                  <div class="box-feature">
                     {section name=i loop=$properties}
                     <div class="item">
                        <div class="title">
                           {$properties[i].name_vn}
                           <input type="checkbox" name="properties[]" value="{$properties[i].id}" />
                        </div>
                     </div>
                     {/section}
                  </div>
               </fieldset>
               <!-- ===== Ordering & Show ===== -->
               <div class="item">
                  <div class="title">
                     <span>Thứ tự</span>
                     <input type="text" name="num" class="InputNum num-order" value="{$numkai.num}" />
                  </div>
               </div>
               <div class="item">
                  <div class="title">
                     Show
                     <input type="checkbox" class="CheckBox" name="active" value="active" {if $edit.active eq 1 || $smarty.request.act eq 'add' }checked{/if} />
                  </div>
               </div>
            </div>
         </form>
      </div>
      <!--/right_content-->
   </div>
   <!--/main-->
</div>
<!--/contentmain-->