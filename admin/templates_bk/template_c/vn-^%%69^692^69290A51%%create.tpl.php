<?php /* Smarty version 2.6.30, created on 2025-12-04 09:13:40
         compiled from footer/create.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'footer/create.tpl', 21, false),array('modifier', 'escape', 'footer/create.tpl', 65, false),)), $this); ?>
<div class="contentmain">
  <div class="main">
    <div class="left_sidebar padding10">
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    </div>

    <div class="right_content ">
      <form name="allsubmit" id="frmEdit"
        action="index.php?do=footer&act=<?php if ($_REQUEST['act'] == 'add'): ?>addsm<?php else: ?>editsm<?php endif; ?>"
        method="post" enctype="multipart/form-data">
        <div class="divright">
          <div class="acti2">
            <button class="add" type="submit"><i class="fa fa-save"></i> Save</button>
          </div>
          <div class="acti2"><a class="add" href="javascript:history.go(-1)"><i class="fa fa-mail-reply"></i> Trở về</a></div>
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
                <div class="item">
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
                  <div class="title">Địa chỉ</div>
                  <div class="info-title">
                    <input type="text" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][address]" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
" id="title_<?php echo $this->_tpl_vars['lang']['code']; ?>
"
                      class="InputText title-input" <?php if ($this->_tpl_vars['lang']['code'] == 'vi'): ?>required<?php endif; ?> />
                  </div>
                </div>
                <div class="item">
                  <div class="title">Mô tả ngắn</div>
                  <div class="meta">
                    <textarea name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][content]" id="content_<?php echo $this->_tpl_vars['lang']['id']; ?>
"></textarea>
                  </div>
                </div>
              </div>
              <?php endforeach; endif; unset($_from); ?>

              <div class="item">
                <div class="title">Bản đồ</div>
                <div class="info-title">
                  <textarea class="InputTextarea" name="map"></textarea>
                </div>
              </div>
            </div>
            <div class="right100">
              <div class="item">
                <div class="title">Hotline</div>
                <div class="info-title">
                  <input type="text" name="hotline" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['hotline'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
" class="InputText" />
                </div>
              </div>

              <div class="item">
                <div class="title">Email</div>
                <div class="info-title">
                  <input type="text" name="email" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
" class="InputText" />
                </div>
              </div>
            </div>
            <!-- right100 -->
          </div>
        </div>
      </form>
      <!-- <h2>Form Builder Demo</h2>
      <div id="form-builder"></div> -->
    </div>
  </div>
</div>