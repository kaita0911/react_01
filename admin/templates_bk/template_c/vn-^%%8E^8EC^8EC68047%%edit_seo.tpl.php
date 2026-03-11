<?php /* Smarty version 2.6.30, created on 2025-11-28 09:47:44
         compiled from infos/edit_seo.tpl */ ?>
<div class="item">
    <div class="title">Title Web</div>
    <div class="info-title">
        <input type="text" name="plain_text_vn" value="<?php echo $this->_tpl_vars['edit']['plain_text_vn']; ?>
" class="InputText" />
    </div>
</div>

<div class="item">
    <div class="title">Domain</div>
    <div class="info-title">
        <input type="text" name="domain" value="<?php echo $this->_tpl_vars['edit']['domain']; ?>
" class="InputText" />
    </div>
</div>

<div class="item">
    <div class="title">Meta Keywords</div>
    <div class="meta">
        <input name="keyword" value="<?php echo $this->_tpl_vars['edit']['keyword']; ?>
" data-role="tagsinput" class="InputText" />
    </div>
</div>

<div class="item">
    <div class="title">Meta Description</div>
    <div class="meta">
        <textarea name="desc" class="InputTextarea" id="inputDesc"><?php echo $this->_tpl_vars['edit']['desc']; ?>
</textarea>
        <span id="showNumDesc" style="color:#ed1b24;">0</span>
    </div>
</div>