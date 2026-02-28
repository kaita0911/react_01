<?php /* Smarty version 2.6.30, created on 2026-02-08 14:52:11
         compiled from menu/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'menu/edit.tpl', 11, false),array('modifier', 'count', 'menu/edit.tpl', 27, false),array('modifier', 'escape', 'menu/edit.tpl', 46, false),)), $this); ?>
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
         <form id="formId"
            action="index.php?do=menu&act=<?php if ($_REQUEST['act'] == 'add'): ?>addsm<?php else: ?>editsm<?php endif; ?>&comp=<?php echo $_REQUEST['comp']; ?>
"
            method="post"
            enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo ((is_array($_tmp=@$this->_tpl_vars['edit']['id'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')); ?>
">

            <div class="divright">
               <div class="acti2">
                  <button class="add" type="submit">
                     <i class="fa fa-save"></i> Save
                  </button>
               </div>
               <div class="acti2">
                  <a class="add" href="javascript:history.back()">
                     <i class="fa fa-mail-reply"></i> Trở về
                  </a>
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
               <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
               <?php $this->assign('detail', null); ?>
               <?php $_from = $this->_tpl_vars['menuDetail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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
               </div>
               <?php endforeach; endif; unset($_from); ?>

              
               <div class="item">
                  <div class="title">Liên kết</div>
                  <div class="option_link">
                     <label>
                        <input type="radio" name="choose" value="1" <?php if ($this->_tpl_vars['edit']['choose'] == 1): ?>checked<?php endif; ?>>
                        Loại bài viết
                     </label>
                     <label>
                        <input type="radio" name="choose" value="0" <?php if ($this->_tpl_vars['edit']['choose'] == 0): ?>checked<?php endif; ?>>
                        Link
                     </label>
                  </div>

                  <select id="menu" name="menu" class="show">
                     <?php $_from = $this->_tpl_vars['lienket']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['link']):
?>
                     <option value="<?php echo $this->_tpl_vars['link']['component_id']; ?>
" <?php if ($this->_tpl_vars['link']['component_id'] == $this->_tpl_vars['edit']['comp']): ?>selected<?php endif; ?>>
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['link']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

                     </option>
                     <?php endforeach; endif; unset($_from); ?>
                  </select>

                  <input type="text"
                     id="link"
                     name="link"
                     class="linkngoai hide"
                     value="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['edit']['link_out'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
">
               </div>

               <div class="item">
               <div class="title">
                     <input type="checkbox"
                        name="menucon"
                        value="menucon"
                        class="CheckBox"
                        <?php if ($this->_tpl_vars['edit']['has_sub'] == 1): ?>checked<?php endif; ?>>
                     Có menucon
                        </div>
                  <div class="title">
                     <input type="checkbox"
                        name="active"
                        value="active"
                        class="CheckBox"
                        <?php if ($this->_tpl_vars['edit']['active'] == 1 || $_REQUEST['act'] == 'add'): ?>checked<?php endif; ?>>
                     Show
                  </div>
                  <div class="item">
                  <div class="title">Thứ tự<input type="text"
                        name="num"
                        class="InputNum num-order"
                        value="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['edit']['num'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
"></div>
                 
                  </div>

               </div>
            </div>
         </form>
      </div>
   </div>
</div>
<?php echo '
<script>
   document.addEventListener("DOMContentLoaded", function() {
      const toggleMenuLink = () => {
         const yes1 = document.querySelector("#yes1");
         const menu = document.getElementById("menu");
         const link = document.querySelector(".linkngoai");
         if (yes1 && yes1.checked) {
            menu.classList.add("hide");
            menu.classList.remove("show");
            link.classList.add("show");
            link.classList.remove("hide");
         } else {
            menu.classList.add("show");
            menu.classList.remove("hide");
            link.classList.add("hide");
            link.classList.remove("show");
         }
      };
      document.querySelectorAll(\'input[name="choose"]\').forEach(el => {
         el.addEventListener("change", toggleMenuLink);
      });
      toggleMenuLink();
   });
</script>
'; ?>