<div class="item">
    <div class="title">Logo</div>
    {if $edit.img_thumb_vn neq ""}
    <!-- Ảnh cũ -->
    <img id="current-img" src="../{$edit.img_thumb_vn}" height="60" style="display:block; margin-bottom:8px;">
    {/if}

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