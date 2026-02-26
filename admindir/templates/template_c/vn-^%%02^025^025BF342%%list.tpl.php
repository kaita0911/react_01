<?php /* Smarty version 2.6.30, created on 2026-02-09 13:57:53
         compiled from articlelist/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'articlelist/list.tpl', 21, false),array('modifier', 'count', 'articlelist/list.tpl', 54, false),array('modifier', 'date_format', 'articlelist/list.tpl', 173, false),)), $this); ?>
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
         <div class="box-foles">
            <div class="divright">
               <div class="acti2">
                  <button class="add" type="button" id="btnAddnew" data-comp="0">
                     <i class="fa fa-plus-circle"></i> Thêm mới
                  </button>
               </div>
               <div class="acti2">
                  <button class="add" type="button" id="btnDelete" data-comp="<?php echo $_REQUEST['comp']; ?>
">
                     <i class="fa fa-trash"></i> Xóa
                  </button>
               </div>
               <div class="acti2">
                  <button class="add" type="button" id="saveOrderBtn" data-comp="<?php echo ((is_array($_tmp=@$_REQUEST['comp'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">
                     <i class="fa-solid fa-up-down"></i> Sắp xếp
                  </button>
               </div>
               <div class="acti2">
                  <button class="add" type="button" id="btnRefresh" data-comp="<?php echo $_REQUEST['comp']; ?>
">
                     <i class="fa fa-copy"></i> Copy
                  </button>
               </div>
            </div>
            <?php if ($_REQUEST['comp'] == 2): ?>
            <!-- ====== Bộ lọc tìm kiếm ====== -->
            <form method="get" action="index.php">
               <input type="hidden" name="do" value="articlelist">
               <input type="hidden" name="comp" value="<?php echo ((is_array($_tmp=@$_REQUEST['comp'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">

               <div class="filter-bar">
                  <select name="cate_id">
                     <option value="">-- Tất cả danh mục --</option>
                     <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['node']):
?>
                     <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "articlelist/category_tree_search.tpl", 'smarty_include_vars' => array('node' => $this->_tpl_vars['node'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                     <?php endforeach; endif; unset($_from); ?>
                  </select>

                  <input type="text" name="keyword" value="<?php echo $_REQUEST['keyword']; ?>
" placeholder="Từ khóa sản phẩm..." style="min-width:200px; margin-left:10px;" />

                  <button type="submit" name="search" value="1" style="margin-left:10px;">Tìm kiếm</button>
               </div>
            </form>
            <?php endif; ?>
         </div>

         <div class="main-content">
            <?php if (count($this->_tpl_vars['languages']) > 1): ?>
            <ul class="tab-list">
               <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
               <li class="tab <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
"><?php echo $this->_tpl_vars['lang']['name']; ?>
</li>
               <?php endforeach; endif; unset($_from); ?>

            </ul>
            <?php endif; ?>
            <form class="form-all" method="post" action="">
               <table class="br1">
                  <thead>
                     <tr>
                        <th align="center" class="width-del">
                           <input type="checkbox" name="all" id="checkAll" />
                        </th>
                        <!-- <th align="center" class="width-order">Thứ tự</th> -->
                        <th align="center" class="width-order">Thứ tự</th>
                        <?php if ($this->_tpl_vars['tinhnang']['hinhanh'] == 1): ?>
                        <th align="center" class="width-image">Hình ảnh</th>
                        <?php endif; ?>

                        <!-- <?php if ($this->_tpl_vars['tinhnang']['id'] == 15): ?>
                        <th align="center" class="width-image">Mã màu</th>
                        <?php endif; ?> -->

                        <th align="left" class="width-ttl">Tiêu đề</th>

                        <?php if ($this->_tpl_vars['tinhnang']['price'] == 1): ?>
                        <th align="center" class="width-image">Giá</th>
                        <?php endif; ?>

                        <!-- <th align="center" class="width-image">Ngày tạo</th> -->
                        <th align="center" class="width-date">Ngày</th>

                        <?php if ($this->_tpl_vars['tinhnang']['new'] == 1): ?>
                        <th align="center" class="width-show">Mới</th>
                        <?php endif; ?>

                        <?php if ($this->_tpl_vars['tinhnang']['hot'] == 1): ?>
                        <th align="center" class="width-show">Nổi bật</th>
                        <?php endif; ?>

                        <?php if ($this->_tpl_vars['tinhnang']['mostview'] == 1): ?>
                        <th align="center" class="width-show">Cập nhật</th>
                        <?php endif; ?>

                        <th align="center" class="width-show">Show</th>

                        <th align="center" class="width-action">Action</th>
                     </tr>
                  </thead>

                  <tbody>
                     <?php $_from = $this->_tpl_vars['articlelist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['item']):
        $this->_foreach['loop']['iteration']++;
?>
                     <tr data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                        <td align="center">
                           <input type="checkbox" class="c-item" name="cid[]" value="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                        </td>
                        <!-- <td align="center">
                           <?php echo $this->_foreach['loop']['iteration']; ?>

                        </td> -->
                        <td align="center">
                           <input type="text" class="numInput" value="<?php echo $this->_tpl_vars['item']['num']; ?>
" />
                        </td>

                        <?php if ($this->_tpl_vars['tinhnang']['hinhanh'] == 1): ?>

                        <td align="center">
                           <?php if ($this->_tpl_vars['item']['img_thumb_vn'] != ""): ?>
                           <div class="c-img <?php echo $this->_tpl_vars['item']['comp']; ?>
" data-comp="<?php echo $this->_tpl_vars['item']['comp']; ?>
" title="Làm mới">
                              <label class="img-change">
                                 <img src="/<?php echo $this->_tpl_vars['item']['img_thumb_vn']; ?>
?width=80&height=80&mode=contain"
                                    class="preview-img" />
                                 <span class="img-overlay">
                                    <i class="fa fa-camera"></i>
                                    <small>Đổi ảnh</small>
                                 </span>
                                 <input type="file" class="img-input" hidden accept="image/*">
                              </label>
                           </div>
                           <?php endif; ?>
                        </td>

                        <?php endif; ?>

                        <!-- <?php if ($this->_tpl_vars['tinhnang']['id'] == 15): ?>
                        <td align="center">
                           <span class="bg-color" style="background: <?php echo $this->_tpl_vars['item']['color']; ?>
;"></span>
                        </td>
                        <?php endif; ?> -->

                        <td align="left">
                           <div class="tab-mirror">
                              <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
                              <?php $this->assign('detail', null); ?>
                              <?php $_from = $this->_tpl_vars['item']['details']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ad']):
?>
                              <?php if ($this->_tpl_vars['ad']['languageid'] == $this->_tpl_vars['lang']['id']): ?>
                              <?php $this->assign('detail', $this->_tpl_vars['ad']); ?>
                              <?php endif; ?>
                              <?php endforeach; endif; unset($_from); ?>
                              <div title="Click vào cập nhật tên" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
" class="act-btn price-view tab c-name editable-name <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                                 <span><?php echo $this->_tpl_vars['detail']['name']; ?>
</span>
                              </div>
                              <?php endforeach; endif; unset($_from); ?>
                           </div>
                        </td>

                        <?php if ($this->_tpl_vars['tinhnang']['price'] == 1): ?>

                        <td align="center">
                           <span title="Click vào cập nhật giá"  class="act-btn price-view btn_edit_price"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-price="<?php echo $this->_tpl_vars['item']['price']['price']; ?>
">
                              <?php echo $this->_tpl_vars['item']['price']['price']; ?>
₫
                           </span>
                        </td>
                        <?php endif; ?>

                        <!-- <td align="center">
                           <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['dated'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%d/%m/%Y %H:%M") : smarty_modifier_date_format($_tmp, "%d/%m/%Y %H:%M")); ?>

                        </td>-->

                        <td align="center">
                           <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['dated_edit'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M - %d/%m/%Y") : smarty_modifier_date_format($_tmp, "%H:%M - %d/%m/%Y")); ?>

                        </td> 

                        <?php if ($this->_tpl_vars['tinhnang']['new'] == 1): ?>
                        <td align="center">
                           <!-- <button type="button"
                              class="btn_checks btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['new']; ?>
"
                              data-column="new"
                              data-table="articlelist">
                              <img src="images/<?php echo $this->_tpl_vars['item']['new']; ?>
.png" alt="Trạng thái Mới" />
                           </button> -->

                           <label class="toggle btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['new']; ?>
"
                              data-column="new"
                              data-table="articlelist">
                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['new'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                        </td>
                        <?php endif; ?>

                        <?php if ($this->_tpl_vars['tinhnang']['hot'] == 1): ?>
                        <td align="center">
                           <!-- <button type="button"
                              class="btn_checks btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['hot']; ?>
"
                              data-column="hot"
                              data-table="articlelist">
                              <img src="images/<?php echo $this->_tpl_vars['item']['hot']; ?>
.png" alt="Trạng thái Hot" />
                           </button> -->

                           <label class="toggle btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['hot']; ?>
"
                              data-column="hot"
                              data-table="articlelist">
                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['hot'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                        </td>
                        <?php endif; ?>

                        <?php if ($this->_tpl_vars['tinhnang']['mostview'] == 1): ?>
                        <td align="center">
                           <!-- <button type="button"
                              class="btn_checks btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['mostview']; ?>
"
                              data-column="mostview"
                              data-table="articlelist">
                              <img src="images/<?php echo $this->_tpl_vars['item']['mostview']; ?>
.png" alt="Trạng thái Xem nhiều" />
                           </button> -->
                           <label class="toggle btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['mostview']; ?>
"
                              data-column="mostview"
                              data-table="articlelist">
                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['mostview'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                        </td>
                        <?php endif; ?>

                        <td align="center">
                           <label class="toggle btn_toggle"
                                 data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                                 data-active="<?php echo $this->_tpl_vars['item']['active']; ?>
"
                                 data-column="active"
                                 data-table="articlelist">

                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['active'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                        </td>

                        <td align="center">
                           <div class="flex-btn">

                              <?php if ($_REQUEST['comp'] == 1 || $_REQUEST['comp'] == 2 || $_REQUEST['comp'] == 3 || $_REQUEST['comp'] == 25 || $_REQUEST['comp'] == 27 || $_REQUEST['comp'] == 10): ?>
                              <div class="tab-mirror">
                                 <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
                                 <?php $this->assign('detail', null); ?>
                                 <?php $_from = $this->_tpl_vars['item']['details']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ad']):
?>
                                 <?php if ($this->_tpl_vars['ad']['languageid'] == $this->_tpl_vars['lang']['id']): ?>
                                 <?php $this->assign('detail', $this->_tpl_vars['ad']); ?>
                                 <?php endif; ?>
                                 <?php endforeach; endif; unset($_from); ?>
                                 <!-- <a data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
" class="tab act-btn btnView <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" href="<?php echo $this->_tpl_vars['web_base_url']; ?>
/<?php echo $this->_tpl_vars['detail']['unique_key']; ?>
.html" target="_blank" title="Xem nhanh">
                                    <i class="fa fa-eye"></i>
                                 </a> -->
                                 <a data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
" class="tab act-btn btnView <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" href="<?php echo $this->_tpl_vars['web_base_url']; ?>
/<?php echo $this->_tpl_vars['detail']['unique_key']; ?>
.html" target="_blank" title="Xem nhanh">
                                    <i class="fa fa-eye"></i>
                                 </a>
                                 <?php endforeach; endif; unset($_from); ?>
                              </div>
                              <?php endif; ?>
                              <a class="act-btn btnEdit" title="Chỉnh sửa" href="index.php?do=articlelist&act=edit&id=<?php echo $this->_tpl_vars['item']['id']; ?>
&comp=<?php echo $_REQUEST['comp']; ?>
">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <button type="button" class="act-btn btnUpdateNum" title="Làm mới" data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
" data-comp="<?php echo $_REQUEST['comp']; ?>
">
                                 <i class="fa fa-refresh"></i>
                              </button>
                              <button  type="button" class="act-btn btnDeleteRow" title="Xoá" data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
" data-comp="<?php echo $_REQUEST['comp']; ?>
">
                                 <i class="fa fa-trash"></i>
                              </button>
                              <!-- <span class="act-btn btnPassword"
                                 data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                                 title="Quản lý mật khẩu">
                                 <i class="fa fa-lock"></i>
                              </span> -->
                           </div>
                        </td>
                     </tr>
                     <?php endforeach; endif; unset($_from); ?>
                  </tbody>
               </table>
            </form>
            <div class="pagination-wrapper">
               <?php echo $this->_tpl_vars['pagination']; ?>

            </div>

         </div>
      </div>
   </div>
</div>
<div id="passwordModal" style="display:none" class="modal">
   <div class="modal-content">
      <h3>Quản lý mật khẩu bài viết</h3>

      <input type="hidden" id="article_id">

      <button id="btnGeneratePassword">
         🔑 Tạo mật khẩu
      </button>

      <div id="generatedBox" style="display:none; margin-top:10px;">
         <strong>Mật khẩu vừa tạo</strong>
         <input type="text" id="generatedPassword" readonly>
         <button onclick="copyPassword()">Copy</button>
      </div>

      <ul id="passwordList"></ul>

      <button onclick="closeModal()">Đóng</button>
   </div>
</div>