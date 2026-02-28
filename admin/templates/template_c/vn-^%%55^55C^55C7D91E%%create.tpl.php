<?php /* Smarty version 2.6.30, created on 2026-02-08 11:42:49
         compiled from articlelist/create.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'articlelist/create.tpl', 10, false),array('modifier', 'count', 'articlelist/create.tpl', 22, false),array('modifier', 'escape', 'articlelist/create.tpl', 116, false),)), $this); ?>
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
         <form name="allsubmit" id="ArticleForm"
            action="index.php?do=articlelist&act=<?php if ($_REQUEST['act'] == 'add'): ?>addsm<?php else: ?>editsm<?php endif; ?>&comp=<?php echo $_REQUEST['comp']; ?>
"
            method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['edit']['id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
" />
            <div class="divright">
               <div class="acti2">
                  <button class="add" type="submit"><i class="fa fa-save"></i> Save</button>
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
                     <div class="tab-content <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
">
                        <div class="item <?php echo $this->_tpl_vars['lang']['code']; ?>
">
                           <div class="title">Tiêu đề</div>
                           <div class="info-title">
                              <input type="text" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][name]" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
" id="title_<?php echo $this->_tpl_vars['lang']['code']; ?>
"
                                 class="InputText title-input" <?php if ($this->_tpl_vars['lang']['code'] == 'vi'): ?>required<?php endif; ?> />
                           </div>
                        </div>
                        <div class="item">
                           <div class="title">URL</div>
                           <div class="info-title">
                              <input type="text" id="slug_<?php echo $this->_tpl_vars['lang']['code']; ?>
" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][unique_key]" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
"
                                 class="InputText slug-input" />
                           </div>
                        </div> <?php if ($this->_tpl_vars['tinhnang']['short'] == 1): ?>
                        <div class="item">
                           <div class="title">Mô tả ngắn</div>

                           <textarea name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][short]" id="short_$lang.id}" class="InputTextarea"></textarea>
                        </div>
                        <?php endif; ?>

                        <?php if ($this->_tpl_vars['tinhnang']['des'] == 1): ?>
                        <div class="item">
                           <div class="title">Mô tả chi tiết</div>
                           <textarea class="ckeditor" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][content]" id="content_$lang.id}"></textarea>
                        </div>
                        <?php endif; ?>

                        <?php if ($this->_tpl_vars['tinhnang']['metatag'] == 1): ?>
                        <div class="title">Meta Keywords</div>
                        <div class="tags-group" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
">
                           <input type="hidden" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][tags]" class="tagsInput" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
" value='[]'>
                           <div class="tagContainer" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
">
                              <div class="tagsWrapper" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
"></div>
                              <input type="text" class="tagInput InputText" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
" placeholder="Nhập tag...">
                           </div>
                        </div>
                        <div class="item">
                           <div class="title">Meta Descriptions</div>
                           <textarea name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][des]" class="InputTextarea" id="inputDesc"></textarea>
                           <span id="showNumDesc" style="color:#ed1b24;">0</span>
                        </div>
                        <?php endif; ?>
                     </div>
                     <?php endforeach; endif; unset($_from); ?>
                  </div>
                  <div class="right100">

                     <?php if ($this->_tpl_vars['tinhnang']['nhomcon'] == 1): ?>
                     <div class="item">
                        <div class="title">Danh mục</div>
                        <!-- <div class="tab <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
">
                              <div class="selectlist">
                                 <ul class="category-tree">
                                    <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
                                    <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['node']):
?>
                                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "articlelist/category_tree.tpl", 'smarty_include_vars' => array('node' => $this->_tpl_vars['node'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                                    <?php endforeach; endif; unset($_from); ?>
                                    <?php endforeach; endif; unset($_from); ?>
                                 </ul>
                              </div> -->
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
$this->_smarty_include(array('smarty_include_tpl_file' => "articlelist/category_tree.tpl", 'smarty_include_vars' => array('node' => $this->_tpl_vars['node'],'selected' => $this->_tpl_vars['selected'],'level' => 0,'currentLang' => $this->_tpl_vars['lang']['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                              <?php endforeach; endif; unset($_from); ?>
                           </ul>
                           <?php endforeach; endif; unset($_from); ?>
                        </div>
                     </div>
                     <?php endif; ?>

                     <?php if ($this->_tpl_vars['tinhnang']['brand'] == 1): ?>
                     <div class="item">
                        <div class="title">Thương hiệu</div>
                        <div class="selectlist">
                           <ul class="category-tree">
                              <?php $_from = $this->_tpl_vars['brands']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['node']):
?>
                              <label> <input type="radio" name="brand_id" value="<?php echo $this->_tpl_vars['node']['id']; ?>
"
                                    <?php if ($this->_tpl_vars['node']['id'] == $this->_tpl_vars['selectedBrandId']): ?>checked<?php endif; ?>>
                                 <?php echo ((is_array($_tmp=$this->_tpl_vars['node']['detail_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
</label>
                              <?php endforeach; endif; unset($_from); ?>
                           </ul>
                        </div>
                     </div>
                     <?php endif; ?>
                     <?php if ($this->_tpl_vars['tinhnang']['masp'] == 1): ?>
                     <div class="item">
                        <div class="title">Tiêu đề báo</div>
                        <input type="text" name="code" class="InputText">
                     </div>
                     <?php endif; ?>
                     <?php if ($this->_tpl_vars['tinhnang']['link_out'] == 1): ?>
                     <div class="item">
                        <div class="title">Link</div>
                        <input type="text" name="link_out" class="InputText">
                     </div>
                     <?php endif; ?>

                     <?php if ($this->_tpl_vars['tinhnang']['hinhanh'] == 1): ?>
                     <div class="item">
                        <div class="title">Hình ảnh</div>
                        <div class="info-title">
                           <?php if ($this->_tpl_vars['edit']['img_thumb_vn'] != ""): ?>
                           <!-- Ảnh cũ -->
                           <img id="current-img" src="../<?php echo $this->_tpl_vars['edit']['img_thumb_vn']; ?>
" height="60" style="display:block; margin-bottom:8px;">
                           <?php endif; ?>

                           <label for="img_thumb_vn" class="custom-upload">
                              <i class="fa fa-upload"></i> Upload image
                           </label>
                           <!-- Input chọn ảnh -->
                           <input type="file"
                              accept="image/png,image/gif,image/jpeg,image/jpg"
                              name="img_thumb_vn"
                              id="img_thumb_vn" class="img-thumb-input">

                           <!-- Preview ảnh mới -->
                           <p class="previewimg" style="margin-top:8px;">
                              <img id="preview-img" style="max-height:150px; display:none;">
                           </p>
                        </div>
                     </div>
                     <?php endif; ?>

                     <?php if ($this->_tpl_vars['tinhnang']['nhieuhinh'] == 1): ?>
                     <div class="item">
                        <div class="title">Upload multi images</div>
                        <label for="multiimages" class="custom-upload">
                           <i class="fa fa-upload"></i> Upload multi images
                        </label>
                        <input type="file" id="multiimages" name="multiimages[]" multiple accept="image/*">
                        <div class="preview-gallery"></div>

                     </div>

                     <?php endif; ?>
                     <?php if ($this->_tpl_vars['tinhnang']['price'] == 1): ?>
                     <div class="item">
                        <div class="title">Giá</div>
                        <input type="text" name="price" class="InputPrice" />
                     </div>
                     <?php endif; ?>

                     <?php if ($this->_tpl_vars['tinhnang']['priceold'] == 1): ?>
                     <div class="item">
                        <div class="title">Giá cũ</div>
                        <input type="text" name="priceold" class="InputPrice" />
                     </div>
                     <?php endif; ?>
                     <?php if ($this->_tpl_vars['tinhnang']['mausac'] == 1): ?>
                     <div class="item">
                        <div class="title">Danh sách màu</div>
                        <div class="selectlist">
                           <ul class="category-tree">
                              <?php $_from = $this->_tpl_vars['colors']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
                              <li><label>
                                    <input type="checkbox"
                                       name="colorids[]"
                                       value="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                                    <?php echo $this->_tpl_vars['item']['name']; ?>

                                 </label></li>
                              <?php endforeach; endif; unset($_from); ?>
                           </ul>
                        </div>
                     </div>
                     <?php endif; ?>
                     <?php if ($this->_tpl_vars['tinhnang']['kichthuoc'] == 1): ?>
                     <div class="item">
                        <div class="title">Danh sách size</div>
                        <div class="selectlist">
                           <ul class="category-tree">
                              <?php $_from = $this->_tpl_vars['sizes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
                              <li><label>
                                    <input type="checkbox"
                                       name="sizeids[]"
                                       value="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                                    <?php echo $this->_tpl_vars['item']['name']; ?>

                                 </label></li>
                              <?php endforeach; endif; unset($_from); ?>
                           </ul>
                        </div>
                     </div>
                     <?php endif; ?>
                     <?php if ($this->_tpl_vars['tinhnang']['attribute'] == 1): ?>
                     <div class="item">
                        <div id="add-product-code">➕ Thêm mã sản phẩm</div>
                        <div id="product-code-wrapper"></div>
                     </div>
                     <?php endif; ?>
                     <?php if ($this->_tpl_vars['tinhnang']['new'] == 1): ?>
                     <div class="item">
                        <div class="title">
                           Mới <input type="checkbox" class="CheckBox" name="new" />
                        </div>
                     </div>
                     <?php endif; ?>

                     <?php if ($this->_tpl_vars['tinhnang']['hot'] == 1): ?>
                     <div class="item">
                        <div class="title">
                           Nổi bật <input type="checkbox" class="CheckBox" name="hot" />
                        </div>
                     </div>
                     <?php endif; ?>
                     <?php if ($this->_tpl_vars['tinhnang']['mostview'] == 1): ?>
                     <div class="item">
                        <div class="title">
                           Xem nhiều <input type="checkbox" class="CheckBox" name="mostview" />
                        </div>
                     </div>
                     <?php endif; ?>
                     <div class="item">
                        <div class="title">Show   <input type="checkbox" name="active" value="active" <?php if ($this->_tpl_vars['edit']['active'] == 1 || $_REQUEST['act'] == 'add'): ?>checked<?php endif; ?>></div>
                      
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>