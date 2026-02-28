<div class="contentmain">
  <div class="main">
    <div class="left_sidebar padding10">
      {include file="left.tpl"}
    </div>
    <div class="right_content conten">
      <form name="allsubmit" id="frmEdit"
        action="index.php?do=footer&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}&id={$smarty.request.id}"
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
                  <input type="text" name="name" value="{$edit_name.name|escape:'html':'UTF-8'}" class="InputText" id="title" />
                </div>
              </div>
              <div class="item">
                <div class="title">Địa chỉ</div>
                <div class="info-title">
                  <input type="text" name="address" value="{$edit_name.address}" class="InputText" />
                </div>
              </div>

              {* Bản đồ *}
              <div class="item">
                <div class="title">Bản đồ</div>
                <div class="info-title">
                  <textarea class="InputTextarea" name="map">{$edit.map}</textarea>
                </div>
              </div>
            </div> {* left100 *}
            <div class="right100">
              <div class="item">
                <div class="title">Hotline</div>
                <div class="info-title">
                  <input type="text" name="hotline" value="{$edit.hotline}" class="InputText" />
                </div>
              </div>
              <div class="item">
                <div class="title">Email</div>
                <div class="info-title">
                  <input type="text" name="email" value="{$edit.email}" class="InputText" />
                </div>
              </div>
            </div> {* right100 *}
          </div>
        </div>
      </form>
    </div>
  </div>
</div>