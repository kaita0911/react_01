<?php /* Smarty version 2.6.30, created on 2026-02-18 11:32:36
         compiled from categories/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'categories/edit.tpl', 11, false),array('modifier', 'count', 'categories/edit.tpl', 25, false),array('modifier', 'escape', 'categories/edit.tpl', 44, false),)), $this); ?>
<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>

      <div class="right_content conten">
         <form name="allsubmit" id="frmEdit" method="post" enctype="multipart/form-data"
            action="index.php?do=categories&act=<?php if ($_REQUEST['act'] == 'add'): ?>addsm<?php else: ?>editsm<?php endif; ?>&comp=<?php echo $_REQUEST['comp']; ?>
&id=<?php echo $_REQUEST['id']; ?>
">
            <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['category']['id']; ?>
" />
            <input type="hidden" name="comp" value="<?php echo ((is_array($_tmp=@$_REQUEST['comp'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">
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
                     <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
                     <?php $this->assign('detail', null); ?>
                     <?php $_from = $this->_tpl_vars['categoryDetail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ad']):
?>
                     <?php if ($this->_tpl_vars['ad']['languageid'] == $this->_tpl_vars['lang']['id']): ?>
                     <?php $this->assign('detail', $this->_tpl_vars['ad']); ?>
                     <?php endif; ?>
                     <?php endforeach; endif; unset($_from); ?>
                     <div class="tab-content <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
">
                        <div class="item">
                           <div class="title">Tiêu đề</div>
                           <div class="info-title">
                              <input type="text" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][name]" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
" id="title_<?php echo $this->_tpl_vars['lang']['code']; ?>
" class="InputText title-input"
                                 value="<?php echo ((is_array($_tmp=$this->_tpl_vars['detail']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
" <?php if ($this->_tpl_vars['lang']['code'] == 'vi'): ?>required<?php endif; ?> />
                           </div>
                        </div>

                        <div class="item">
                           <div class="title">URL</div>
                           <div class="info-title">
                              <input type="text" id="slug_<?php echo $this->_tpl_vars['lang']['code']; ?>
" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][unique_key]" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
"
                                 value="<?php echo $this->_tpl_vars['detail']['unique_key']; ?>
"
                                 class="InputText slug-input" />
                           </div>
                        </div>
                        <div class="item">
                           <div class="title">Mô tả ngắn</div>
                           <div class="meta">
                              <textarea id="short_<?php echo $this->_tpl_vars['lang']['id']; ?>
" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][short]"><?php echo $this->_tpl_vars['detail']['short']; ?>
</textarea>
                           </div>
                        </div>
                        <div class="item">
                           <div class="title">Mô tả chi tiết</div>
                           <div class="meta">
                              <textarea class="ckeditor" id="content_<?php echo $this->_tpl_vars['lang']['id']; ?>
]" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][content]"><?php echo $this->_tpl_vars['detail']['content']; ?>
</textarea>
                           </div>
                        </div>

                        <div class="item">
                           <div class="title">Meta Keywords</div>

                           <div class="tags-group" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
">
                              <input type="hidden" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][tags]" class="tagsInput" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['detail']['tagsJson'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
                              <div class="tagContainer" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
">
                                 <div class="tagsWrapper" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
"></div>
                                 <input type="text" class="tagInput InputText" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
" placeholder="Nhập tag...">
                              </div>
                           </div>
                        </div>

                        <div class="item">
                           <div class="title">Meta Descriptions</div>
                           <div class="meta">
                              <textarea name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][des]" class="InputTextarea" id="inputDesc"><?php echo $this->_tpl_vars['detail']['des']; ?>
</textarea>
                              <span id="showNumDesc" style="color:#ed1b24;">0</span>
                           </div>
                        </div>
                     </div>
                     <?php endforeach; endif; unset($_from); ?>
                  </div>
                  <div class="right100">
                     <div class="item">
                        <div class="title">Danh mục</div>
                        <div class="selectlist tab-mirror">
                           <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
                           <ul class="tab category-tree <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
">
                              <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['node']):
?>
                              <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "categories/category_tree.tpl", 'smarty_include_vars' => array('node' => $this->_tpl_vars['node'],'selected' => $this->_tpl_vars['selected'],'level' => 0,'currentLang' => $this->_tpl_vars['lang']['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                              <?php endforeach; endif; unset($_from); ?>
                           </ul>
                           <?php endforeach; endif; unset($_from); ?>
                        </div>
                     </div>
                     <?php if ($this->_tpl_vars['tinhnang']['hinhdanhmuc'] == 1): ?>
                     <div class="item">
                        <div class="title">Hình ảnh</div>

                        <div class="info-title">
                           <?php if ($this->_tpl_vars['category']['img_vn'] != ""): ?>
                           <!-- Ảnh cũ -->
                           <img id="current-img" src="/<?php echo $this->_tpl_vars['category']['img_vn']; ?>
?width=60&height=60&mode=scale" height="60" style="display:block; margin-bottom:8px;">
                           <?php endif; ?>

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

                     <?php endif; ?>
                     <!-- <div class="item">
                        <div class="title">Link xem thêm</div>
                        <input type="text" name="link" class="InputText" value="<?php echo $this->_tpl_vars['category']['link']; ?>
">
                     </div> -->
                     <div class="item">
                        <div class="title">Thứ Tự
                           <input type="text" name="num" value="<?php echo $this->_tpl_vars['category']['num']; ?>
" class="InputNum" />
                        </div>
                     </div>

                     <div class="item">
                        <div class="title">Show
                           <input type="checkbox" class="CheckBox" name="active" value="active"
                              <?php if ($this->_tpl_vars['category']['active'] == 1 || $_REQUEST['act'] == 'add'): ?>checked<?php endif; ?> />
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>