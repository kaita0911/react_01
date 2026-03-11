<?php /* Smarty version 2.6.30, created on 2026-02-11 15:35:49
         compiled from infos/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'infos/edit.tpl', 23, false),)), $this); ?>
<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>

      <div class="right_content pad">
         <form id="frmEdit"
            action="index.php?do=infos&act=<?php if ($_REQUEST['act'] == 'add'): ?>addsm<?php else: ?>editsm<?php endif; ?>&comp=<?php echo $_REQUEST['comp']; ?>
"
            method="post"
            enctype="multipart/form-data">

            <div class="btnseo">
               <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['edit']['id']; ?>
" />
               <button type="submit"><i class="fa fa-save"></i> Save</button>
               <a href="javascript:history.back()"><i class="fa fa-mail-reply"></i> Trở về</a>
            </div>

            <div class="item">
               <div class="title">Tiêu đề</div>
               <div class="info-title">
                  <input type="text" name="name_vn" class="InputText" id="title"
                     value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['name_vn'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
" onkeyup="ChangeToSlug();">
               </div>
            </div>

                        <?php if ($this->_tpl_vars['edit']['id'] == 1): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_logo.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 2): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_domain.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 3): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_googlemap.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 4): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_content.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 5): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_hotline.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 6): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_email.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 7): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_social.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 12): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_cart.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 13): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_time.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 15): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_seo.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php elseif ($this->_tpl_vars['edit']['id'] == 29): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_pagination.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php else: ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'infos/edit_default.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php endif; ?>

                        <div class="item">
               <div class="title">Hiển thị<input type="checkbox" class="CheckBox" name="active" value="active"
               <?php if ($this->_tpl_vars['edit']['active'] == 1 || $_REQUEST['act'] == 'add'): ?>checked<?php endif; ?>></div>
               
            </div>
         </form>
      </div>
   </div>
</div>