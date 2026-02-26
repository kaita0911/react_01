<div class="item">
    <div class="title">Giỏ hàng</div>
    <div class="info-title">
        <input type="checkbox" class="CheckBox" name="open" value="open"
            {if $edit.open eq 1 || $smarty.request.act eq 'add' }checked{/if} />
    </div>
</div>

<div class="item">
    <div class="title">Phí ship</div>
    <div class="info-title">
        <input type="checkbox" class="CheckBox" name="phiship" value="phiship"
            {if $edit.phiship eq 1 || $smarty.request.act eq 'add' }checked{/if} />
    </div>
</div>