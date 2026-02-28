<?php /* Smarty version 2.6.30, created on 2026-01-05 11:37:14
         compiled from component/edit.tpl */ ?>
<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>
      <div class="right_content">
         <form name="allsubmit" id="frmEdit"
            action="index.php?do=component&act=<?php if ($_REQUEST['act'] == 'add'): ?>addsm<?php else: ?>editsm<?php endif; ?>&id=<?php echo $_REQUEST['id']; ?>
"
            method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['edit']['id']; ?>
" />
            <div class="divright">
               <div class="acti2">
                  <button class="add" type="submit"><i class="fa fa-save"></i> Save</button>
               </div>
            </div>
            <div class="main-content">

               <?php if ($_SESSION['admin_artseed_username'] == 'kaita'): ?>
               <!-- ================== THÔNG TIN CƠ BẢN ================== -->
               <fieldset>
                  <legend>Thông tin cơ bản</legend>
                  <!-- Tiêu đề & URL -->
                  <div class="item">
                     <div class="title">Tiêu đề</div>
                     <input type="text" value="<?php echo $this->_tpl_vars['edit']['name']; ?>
"
                        name="name" class="InputText"
                        id="title" />
                  </div>
                  <div class="item">
                     <div class="title">TYPE</div>
                     <input type="text" value="<?php echo $this->_tpl_vars['edit']['do']; ?>
" name="do" class="InputText" />
                  </div>
                  <div class="item">
                     <div class="title">Phân trang</div>
                     <input type="num" value="<?php echo $this->_tpl_vars['edit']['phantrang']; ?>
" name="phantrang" class="InputText" />
                  </div>
                  <div class="item">
                     <div class="title">Icon font</div>
                     <input type="text" value="<?php echo $this->_tpl_vars['edit']['iconfont']; ?>
" name="iconfont" class="InputText" />
                  </div>
                  <div class="item">
                     <div class="title">Thứ tự</div>
                     <input type="text" value="<?php echo $this->_tpl_vars['edit']['num']; ?>
" name="num" class="InputNum num-order" />
                  </div>

               </fieldset>

               <!-- ================== THUỘC TÍNH RIÊNG ================== -->
               <fieldset>
                  <legend>Thuộc tính riêng</legend>
                  <?php 
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
                   ?>
                  <div class="box-feature">
                     <?php $_from = $this->_tpl_vars['attrs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['attr']):
?>
                     <?php $this->assign('checked', false); ?>
                     <?php if ($this->_tpl_vars['attr']['name'] == 'active'): ?>
                     <?php if ($this->_tpl_vars['edit']['active'] == 1 || $_REQUEST['act'] == 'add'): ?><?php $this->assign('checked', true); ?><?php endif; ?>
                     <?php else: ?>
                     <?php if ($this->_tpl_vars['edit'][$this->_tpl_vars['attr']['name']] == 1): ?><?php $this->assign('checked', true); ?><?php endif; ?>
                     <?php endif; ?>
                     <div class="item">
                        <div class="title">
                           <?php echo $this->_tpl_vars['attr']['label']; ?>

                           <input type="checkbox" class="CheckBox" name="<?php echo $this->_tpl_vars['attr']['name']; ?>
" value="<?php echo $this->_tpl_vars['attr']['name']; ?>
" <?php if ($this->_tpl_vars['checked']): ?>checked<?php endif; ?> />
                        </div>
                     </div>
                     <?php endforeach; endif; unset($_from); ?>
                  </div>

               </fieldset>
               <!-- ================== THUỘC TÍNH DANH MỤC ================== -->
               <fieldset>
                  <legend>Thuộc tính DANH MỤC</legend>

                  <?php 
                  $attrs = array(
                  array('name'=>'nhomcon','label'=>'Danh mục'),
                  array('name'=>'danhmuchome','label'=>'Hiện trang chủ'),
                  array('name'=>'hinhdanhmuc','label'=>'Hình danh mục'),
                  array('name'=>'motadanhmuc','label'=>'Mô tả danh mục'),
                  array('name'=>'brand','label'=>'Thương hiệu')
                  );
                  $this->assign('attrs', $attrs);
                   ?>
                  <div class="box-feature">
                     <?php $_from = $this->_tpl_vars['attrs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['attr']):
?>
                     <?php $this->assign('checked', false); ?>
                     <?php if ($this->_tpl_vars['attr']['name'] == 'active'): ?>
                     <?php if ($this->_tpl_vars['edit']['active'] == 1 || $_REQUEST['act'] == 'add'): ?><?php $this->assign('checked', true); ?><?php endif; ?>
                     <?php else: ?>
                     <?php if ($this->_tpl_vars['edit'][$this->_tpl_vars['attr']['name']] == 1): ?><?php $this->assign('checked', true); ?><?php endif; ?>
                     <?php endif; ?>
                     <div class="item">
                        <div class="title">
                           <?php echo $this->_tpl_vars['attr']['label']; ?>

                           <input type="checkbox" class="CheckBox" name="<?php echo $this->_tpl_vars['attr']['name']; ?>
" value="<?php echo $this->_tpl_vars['attr']['name']; ?>
" <?php if ($this->_tpl_vars['checked']): ?>checked<?php endif; ?> />
                        </div>
                     </div>
                     <?php endforeach; endif; unset($_from); ?>
                  </div>
               </fieldset>
               <!-- ================== THUỘC TÍNH CHUNG MODULE ================== -->
               <fieldset>
                  <legend>Thuộc tính chung module</legend>
                  <div class="box-feature">
                     <div class="item">
                        <div class="title"> Hình ảnh
                           <input type="checkbox" class="CheckBox" name="hinhmodule" value="hinhmodule" <?php if ($this->_tpl_vars['edit']['hinhmodule'] == 1): ?>checked<?php endif; ?> />

                        </div>
                     </div>
                     <div class="item">
                        <div class="title"> Mô tả chung
                           <input type="checkbox" class="CheckBox" name="motamodule" value="motamodule" <?php if ($this->_tpl_vars['edit']['motamodule'] == 1 || $_REQUEST['act'] == 'add'): ?>checked<?php endif; ?> />

                        </div>
                     </div>
                  </div>
               </fieldset>
               <?php endif; ?>
            </div>
         </form>
      </div>
   </div>
</div>