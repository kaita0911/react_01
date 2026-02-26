<div class="contentmain">
  <div class="main">
    <div class="left_sidebar padding10">
      {include file="left.tpl"}
    </div>

    <div class="right_content ">
      <form name="allsubmit" id="frmEdit"
        action="index.php?do=footer&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}"
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
              {if $languages|@count > 1}
              <ul class="tab-list">
                {foreach from=$languages item=lang}
                <li class="tab {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">{$lang.name}</li>
                {/foreach}
              </ul>
              {/if}
              {foreach from=$languages item=lang}
              <div class="tab-content {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">
                <div class="item">
                  <div class="title">Tiêu đề</div>
                  <div class="info-title">
                    <input type="text" name="languages[{$lang.id}][name]" data-lang="{$lang.code}" id="title_{$lang.code}"
                      class="InputText title-input" {if $lang.code=='vi' }required{/if} />
                  </div>
                </div>

                <div class="item">
                  <div class="title">Địa chỉ</div>
                  <div class="info-title">
                    <input type="text" name="languages[{$lang.id}][address]" data-lang="{$lang.code}" id="title_{$lang.code}"
                      class="InputText title-input" {if $lang.code=='vi' }required{/if} />
                  </div>
                </div>
                <!-- <div class="item">
                  <div class="title">Mô tả ngắn</div>
                  <div class="meta">
                    <textarea name="languages[{$lang.id}][content]" id="content_{$lang.id}"></textarea>
                  </div>
                </div> -->
              </div>
              {/foreach}

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
                  <input type="text" name="hotline" value="{$edit.hotline|escape:'html':'UTF-8'}" class="InputText" />
                </div>
              </div>

              <div class="item">
                <div class="title">Email</div>
                <div class="info-title">
                  <input type="text" name="email" value="{$edit.email|escape:'html':'UTF-8'}" class="InputText" />
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