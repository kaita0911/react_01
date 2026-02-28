<div class="item">
    <div class="title">Thời gian</div>
    <div class="info-title">
        <input type="text" name="domain" value="{$edit.domain}" class="InputText" />
    </div>
</div>

<div class="item">
    <div class="title">Đóng web</div>
    <div class="info-title">
        <input type="checkbox" class="CheckBox" name="open" value="open"
            {if $edit.open eq 1 || $smarty.request.act eq 'add' }checked{/if} />
    </div>
</div>

<div class="item">
    <div class="title">Hiện ngày tháng</div>
    <div class="info-title">
        <input type="checkbox" class="CheckBox" name="ngaythang" value="ngaythang"
            {if $edit.ngaythang eq 1 || $smarty.request.act eq 'add' }checked{/if} />
    </div>
</div>