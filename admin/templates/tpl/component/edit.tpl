<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>
      <div class="right_content">
         <form name="allsubmit" id="frmEdit"
            action="index.php?do=component&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}&id={$smarty.request.id}"
            method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{$edit.id}" />
            <div class="divright">
               <div class="acti2">
                  <button class="add" type="submit"><i class="fa fa-save"></i> Save</button>
               </div>
            </div>
            <div class="main-content">

               {if $smarty.session.admin_artseed_username == 'kaita'}
               <!-- ================== THÔNG TIN CƠ BẢN ================== -->
               <fieldset>
                  <legend>Thông tin cơ bản</legend>
                  <!-- Tiêu đề & URL -->
                  <div class="item">
                     <div class="title">Tiêu đề</div>
                     <input type="text" value="{$edit.name}"
                        name="name" class="InputText"
                        id="title" />
                  </div>
                  <div class="item">
                     <div class="title">TYPE</div>
                     <input type="text" value="{$edit.do}" name="do" class="InputText" />
                  </div>
                  <div class="item">
                     <div class="title">Phân trang</div>
                     <input type="num" value="{$edit.phantrang}" name="phantrang" class="InputText" />
                  </div>
                  <div class="item">
                     <div class="title">Icon font</div>
                     <input type="text" value="{$edit.iconfont}" name="iconfont" class="InputText" />
                  </div>
                  <div class="item">
                     <div class="title">Thứ tự</div>
                     <input type="text" value="{$edit.num}" name="num" class="InputNum num-order" />
                  </div>

               </fieldset>

               <!-- ================== THUỘC TÍNH RIÊNG ================== -->
               <fieldset>
                  <legend>Thuộc tính riêng</legend>
                  {php}
                  $attrs = array(
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
                  array('name'=>'viewed','label'=>'Đã xem'),
                  array('name'=>'active','label'=>'Show'),
                  array('name'=>'link_out','label'=>'Link ngoài'),
                  array('name'=>'attribute','label'=>'Thuộc tính')
                  );
                  $this->assign('attrs', $attrs);
                  {/php}
                  <div class="box-feature">
                     {foreach from=$attrs item=attr}
                     {assign var="checked" value=false}
                     {if $attr.name eq 'active'}
                     {if $edit.active eq 1 || $smarty.request.act eq 'add'}{assign var="checked" value=true}{/if}
                     {else}
                     {if $edit[$attr.name] eq 1}{assign var="checked" value=true}{/if}
                     {/if}
                     <div class="item">
                        <div class="title">
                           {$attr.label}
                           <input type="checkbox" class="CheckBox" name="{$attr.name}" value="{$attr.name}" {if $checked}checked{/if} />
                        </div>
                     </div>
                     {/foreach}
                  </div>

               </fieldset>
               <!-- ================== THUỘC TÍNH DANH MỤC ================== -->
               <fieldset>
                  <legend>Thuộc tính DANH MỤC</legend>

                  {php}
                  $attrs = array(
                  array('name'=>'nhomcon','label'=>'Danh mục'),
                  array('name'=>'danhmuchome','label'=>'Hiện trang chủ'),
                  array('name'=>'hinhdanhmuc','label'=>'Hình danh mục'),
                  array('name'=>'motadanhmuc','label'=>'Mô tả danh mục'),
                  array('name'=>'brand','label'=>'Thương hiệu')
                  );
                  $this->assign('attrs', $attrs);
                  {/php}
                  <div class="box-feature">
                     {foreach from=$attrs item=attr}
                     {assign var="checked" value=false}
                     {if $attr.name eq 'active'}
                     {if $edit.active eq 1 || $smarty.request.act eq 'add'}{assign var="checked" value=true}{/if}
                     {else}
                     {if $edit[$attr.name] eq 1}{assign var="checked" value=true}{/if}
                     {/if}
                     <div class="item">
                        <div class="title">
                           {$attr.label}
                           <input type="checkbox" class="CheckBox" name="{$attr.name}" value="{$attr.name}" {if $checked}checked{/if} />
                        </div>
                     </div>
                     {/foreach}
                  </div>
               </fieldset>
               <!-- ================== THUỘC TÍNH CHUNG MODULE ================== -->
               <fieldset>
                  <legend>Thuộc tính chung module</legend>
                  <div class="box-feature">
                     <div class="item">
                        <div class="title"> Hình ảnh
                           <input type="checkbox" class="CheckBox" name="hinhmodule" value="hinhmodule" {if $edit.hinhmodule eq 1}checked{/if} />

                        </div>
                     </div>
                     <div class="item">
                        <div class="title"> Mô tả chung
                           <input type="checkbox" class="CheckBox" name="motamodule" value="motamodule" {if $edit.motamodule eq 1 || $smarty.request.act eq 'add' }checked{/if} />

                        </div>
                     </div>
                  </div>
               </fieldset>
               {/if}
            </div>
         </form>
      </div>
   </div>
</div>