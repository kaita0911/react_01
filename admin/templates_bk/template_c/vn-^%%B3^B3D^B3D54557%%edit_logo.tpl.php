<?php /* Smarty version 2.6.30, created on 2025-11-21 09:52:36
         compiled from infos/edit_logo.tpl */ ?>
<div class="item">
    <div class="title">Logo</div>
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