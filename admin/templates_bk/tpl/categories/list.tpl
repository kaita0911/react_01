<div class="contentmain">
  <div class="main">
    <div class="left_sidebar padding10">
      {include file="left.tpl"}
    </div>
    <div class="right_content">
      <div class="divright">
        <div class="acti2">
          <button class="add" type="button" id="btnAddnew" data-comp="{$smarty.request.comp|default:0}">
            <i class="fa fa-plus-circle"></i> Thêm mới
          </button>
        </div>
        <div class="acti2">
          <button class="add" type="button" id="btnDelete">
            <i class="fa fa-trash"></i> Xóa
          </button>
        </div>
        <div class="acti2">
          <button class="add" type="button" id="saveOrderBtn" data-comp="{$smarty.request.comp|default:0}">
            <i class="fa-solid fa-up-down"></i> Sắp xếp
          </button>
        </div>
      </div>
      <div class="main-content">
        {if $languages|@count > 1}
        <ul class="tab-list">
          {foreach from=$languages item=lang}
          <li class="tab {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">{$lang.name}</li>
          {/foreach}
        </ul>
        {/if}
        <form class="form-all" id="categoryForm" method="post" action="" enctype="multipart/form-data">
          <table class="br1 catelist" width="100%" cellspacing="0" cellpadding="0" style="border-bottom:0">
            <thead>
              <tr>
                <th class="width-del">
                  <input type="checkbox" name="all" id="checkAll" />
                </th>
                <th class="width-order">Thứ tự</th>
                {if $tinhnang.hinhdanhmuc == 1}
                <th class="width-image">Hình ảnh</th>
                {/if}
                <th class="width-ttl">Tiêu đề</th>
                {if $tinhnang.danhmuchome == 1}
                <th class="width-show">Home</th>
                {/if}
                <th class="width-show">Show</th>
                <th class="width-action">Action</th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$categories item=category}
              {include file="categories/category_row_lang.tpl" category=$category level=0 languages=$languages currentLang=$currentLang}
              {/foreach}
            </tbody>
          </table>
        </form>

      </div>
    </div>
  </div>
</div>