<div class="contentmain">
  <div class="main">
    <div class="left_sidebar padding10">
      {include file="left.tpl"}
    </div>

    <div class="right_content ">
      <form name="allsubmit" id="frmEdit"
        action="index.php?do=size&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}"
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