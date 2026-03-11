<div class="contentmain">
  <div class="main">
    <div class="left_sidebar padding10">
      {include file="left.tpl"}
    </div>
    <div class="right_content conten">
      <form name="allsubmit" id="frmEdit"
        action="index.php?do=footer&act={if $smarty.request.act eq 'add'}addsm{else}editsm{/if}&id={$smarty.request.id}"
        method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="{$footer.id}" />
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
              {assign var=detail value=null}
              {foreach from=$footerDetail item=ad}
              {if $ad.languageid == $lang.id}
              {assign var=detail value=$ad}
              {/if}
              {/foreach}
              <div class="tab-content {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">
                <div class="item">
                  <div class="title">Tiêu đề</div>
                  <!-- <div class="info-title">
                    <input type="text" name="name" value="{$edit_name.name|escape:'html':'UTF-8'}" class="InputText" id="title" />
                  </div> -->
                  <div class="info-title">
                    <input type="text" name="languages[{$lang.id}][name]" data-lang="{$lang.code}" id="title_{$lang.code}" class="InputText title-input"
                      value="{$detail.name|escape:'html':'UTF-8'}" {if $lang.code=='vi' }required{/if} />
                  </div>
                </div>
                <div class="item">
                  <div class="title">Địa chỉ</div>
                  <div class="info-title">
                    <input type="text" name="languages[{$lang.id}][address]" value="{$detail.address}" class="InputText" />
                  </div>
                </div>
                <!-- <div class="item">
                  <div class="title">Mô tả ngắn</div>
                  <div class="meta">
                    <textarea id="content_{$lang.id}" name="languages[{$lang.id}][content]">{$detail.content}</textarea>
                  </div>
                </div> -->
              </div>
              {/foreach}

              {* Bản đồ *}
              <div class="item">
                <div class="title">Bản đồ</div>
                <div class="info-title">
                  <textarea class="InputTextarea" name="map">{$footer.map}</textarea>
                </div>
              </div>
            </div> {* left100 *}
            <div class="right100">
              <div class="item">
                <div class="title">Hotline</div>
                <div class="info-title">
                  <input type="text" name="hotline" value="{$footer.hotline}" class="InputText" />
                </div>
              </div>
              <div class="item">
                <div class="title">Email</div>
                <div class="info-title">
                  <input type="text" name="email" value="{$footer.email}" class="InputText" />
                </div>
              </div>
            </div> {* right100 *}
          </div>
        </div>
      </form>
    </div>
  </div>
</div>