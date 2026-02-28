<?php /* Smarty version 2.6.30, created on 2026-02-03 11:08:51
         compiled from register_info/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'register_info/edit.tpl', 24, false),)), $this); ?>
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
            <?php if ($this->_tpl_vars['edit']['title']): ?>
            <div class="form-item">
               <label class="title" for="fullname">Bài viết đã xem</label>
               <div class="meta">
                  <input type="text" id="fullname"
                     value="<?php echo $this->_tpl_vars['edit']['title']; ?>
"
                     class="InputText" readonly>
               </div>
            </div>
            <?php endif; ?>
            <div class="form-item">
               <label class="title" for="fullname">Họ tên</label>
               <div class="meta">
                  <input type="text" id="fullname"
                     value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['fullname'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
"
                     class="InputText" readonly>
               </div>
            </div>

            <div class="form-item">
               <label class="title" for="phone">Điện thoại</label>
               <div class="meta">
                  <input type="text" id="phone"
                     value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['phone'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
"
                     class="InputText" readonly>
               </div>
            </div>
            <div class="form-item">
               <label class="title" for="email">Email</label>
               <div class="meta">
                  <input type="text" id="email"
                     value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
"
                     class="InputText" readonly>
               </div>
            </div>
            <?php if ($this->_tpl_vars['edit']['address']): ?>
            <div class="form-item">
               <label class="title" for="email">Địa chỉ</label>
               <div class="meta">
                  <input type="text" id="email"
                     value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['address'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
"
                     class="InputText" readonly>
               </div>
            </div>
            <?php endif; ?>
            <div class="form-item">
               <label class="title" for="message">Nội dung</label>
               <div class="meta">
                  <textarea id="message" class="InputTextarea" readonly><?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['message'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
</textarea>
               </div>
            </div>

            <p class="slss">
               <a href="index.php?do=register_info" title="Trở về">
                  <i class="fa fa-rotate-left"></i> Trở về
               </a>
            </p>

         </div>
      </div>

   </div>
</div>