<?php /* Smarty version 2.6.30, created on 2025-11-23 11:39:54
         compiled from categories/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'categories/list.tpl', 9, false),array('modifier', 'count', 'categories/list.tpl', 25, false),)), $this); ?>
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
      <div class="divright">
        <div class="acti2">
          <button class="add" type="button" id="btnAddnew" data-comp="<?php echo ((is_array($_tmp=@$_REQUEST['comp'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">
            <i class="fa fa-plus-circle"></i> Thêm mới
          </button>
        </div>
        <div class="acti2">
          <button class="add" type="button" id="btnDelete">
            <i class="fa fa-trash"></i> Xóa
          </button>
        </div>
        <div class="acti2">
          <button class="add" type="button" id="saveOrderBtn" data-comp="<?php echo ((is_array($_tmp=@$_REQUEST['comp'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">
            <i class="fa-solid fa-up-down"></i> Sắp xếp
          </button>
        </div>
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
        <form class="form-all" id="categoryForm" method="post" action="" enctype="multipart/form-data">
          <table class="br1 catelist" width="100%" cellspacing="0" cellpadding="0" style="border-bottom:0">
            <thead>
              <tr>
                <th class="width-del">
                  <input type="checkbox" name="all" id="checkAll" />
                </th>
                <th class="width-order">Thứ tự</th>
                <?php if ($this->_tpl_vars['tinhnang']['hinhdanhmuc'] == 1): ?>
                <th class="width-image">Hình ảnh</th>
                <?php endif; ?>
                <th class="width-ttl">Tiêu đề</th>
                <?php if ($this->_tpl_vars['tinhnang']['danhmuchome'] == 1): ?>
                <th class="width-show">Home</th>
                <?php endif; ?>
                <th class="width-show">Show</th>
                <th class="width-action">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['category']):
?>
              <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "categories/category_row_lang.tpl", 'smarty_include_vars' => array('category' => $this->_tpl_vars['category'],'level' => 0,'languages' => $this->_tpl_vars['languages'],'currentLang' => $this->_tpl_vars['currentLang'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
              <?php endforeach; endif; unset($_from); ?>
            </tbody>
          </table>
        </form>

      </div>
    </div>
  </div>
</div>