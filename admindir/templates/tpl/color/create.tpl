<div class="contentmain">
  <div class="main">
    <div class="left_sidebar padding10">
      {include file="left.tpl"}
    </div>

    <div class="right_content ">
      <form name="allsubmit" id="frmEdit"
        action="index.php?do=color&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}"
        method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="{$edit.id}" />
        <div class="divright">
          <div class="acti2">
            <button class="add" type="submit"><i class="fa fa-save"></i> Save</button>
          </div>
          <div class="acti2"><a class="add" href="javascript:history.go(-1)"><i class="fa fa-mail-reply"></i> Trở về</a></div>
        </div>
        <div class="main-content">
          <div class="wrap-main">
            <div class="left100">
              <div class="item">
                <div class="title">Tiêu đề</div>
                <div class="info-title">
                  <input type="text" name="name" class="InputText" />
                </div>
              </div>
            </div>
            <div class="right100">
              <div class="item">
                <div class="title"> Chọn màu</div>
                <input type="color" id="colorPicker" name="code" value="#ff0000" required>
                <span id="colorCode">#ff0000</span>
              </div>
              {literal}
              <script>
                // Hiển thị mã màu khi chọn
                const picker = document.getElementById('colorPicker');
                const code = document.getElementById('colorCode');
                picker.addEventListener('input', function() {
                  code.textContent = this.value;
                  code.style.color = this.value;
                });
              </script>
              {/literal}
              <div class="item">
                <div class="title">Show</div>
                <input type="checkbox" name="active" value="active" {if $edit.active eq 1 || $smarty.request.act eq 'add' }checked{/if}>
              </div>
            </div>
            <!-- right100 -->
          </div>
        </div>
      </form>
    </div>
  </div>
</div>