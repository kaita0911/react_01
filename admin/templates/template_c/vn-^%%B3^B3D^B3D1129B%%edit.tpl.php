<?php /* Smarty version 2.6.30, created on 2026-02-08 14:30:39
         compiled from contact/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contact/edit.tpl', 11, false),)), $this); ?>
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
         <div class="main-content">
            <div class="item">
               <div class="title">Họ tên</div>
               <div class="meta">
                  <input type="text" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
" class="InputText" />
               </div>
            </div>
            <div class="item">
               <div class="title">Điện thoại</div>
               <div class="meta">
                  <input type="text" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['phone'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
" class="InputText" />
               </div>
            </div>
            <div class="item">
               <div class="title">Email</div>
               <div class="meta">
                  <input type="text" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
" class="InputText" />
               </div>
            </div>
            <div class="item">
               <div class="title">Nội dung</div>
               <div class="meta">
                  <textarea class="InputTextarea"><?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['message'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
</textarea>
               </div>
            </div>
            <div class="item">
               <div class="title">File đính kèm</div>
               <div class="meta">
                  <a href="../../../<?php echo $this->_tpl_vars['edit']['fileUpload']; ?>
" target="_blank">
                     <i class="fa fa-book"></i> Xem file
                  </a>
               </div>
            </div>
            <p class="slss">
               <a href="index.php?do=contact&comp=23" title="Trở về">
                  <i class="fa fa-rotate-left"></i> Trở về
               </a>
            </p>
         </div>
      </div>
   </div>
</div>