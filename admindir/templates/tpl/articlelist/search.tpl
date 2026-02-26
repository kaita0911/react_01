<div class="contentmain">
  <div class="main">
    <div class="left_sidebar padding10">
      <!--{include file="left.tpl"}-->
    </div>
    <div class="right_content conten">
      <div class="divtitle margin5">
        <!--{if $tinhnang.id ==2}-->
        <div class="form_moblie_search">
          <form name="search" action="index.php?do=articlelist&comp=2" method="post" enctype="multipart/form-data">
            <!--<select id="select_cate" name="catedm">
              <option value="-1">Chọn danh mục</option>
              <!--{section name=i loop=$catedm}-->
              <option value="<!--{$catedm[i].id}-->">
              <!--{$catedm[i].name_vn}-->
            </option>
            <!--{/section}-->
          </select>-->
          <input type="text" name="names" value="<!--{$names}-->" playholder="Tìm kiếm"  />
          <input type="submit" name="button" id="button" value="Tìm kiếm" />
        </form>
      </div>
      <!--{/if}-->
      <div class="divleft">
        <div class="divright link00">
          <div class="acti2">
            <a class="add" href="javascript:void(0)" title="Add" onclick="ChangeAdd('index.php?do=articlelist&act=add&comp=<!--{$smarty.request.comp}-->');">
            <i class="fa fa-plus-circle"></i> Thêm mới
          </a>
        </div>
        <div class="acti2">
          <a class="del" href="javascript:void(0)" title="Delete" onclick="ChangeAction('index.php?do=articlelist&act=dellist&comp=<!--{$smarty.request.comp}-->');">
          <i class="fa fa-trash"></i> Xóa
        </a>
      </div>
      <div class="acti2">
        <a class="order" href="javascript:void(0)" title="Order" onclick="ChangeAction('index.php?do=articlelist&act=order&comp=<!--{$smarty.request.comp}-->');">
        <i class="fa-solid fa-up-down"></i> Sắp xếp
      </a>
    </div>
    <div class="acti2">
      <a class="rot" href="javascript:void(0)" title="new" onclick="ChangeAction('index.php?do=articlelist&act=newed&comp=<!--{$smarty.request.comp}-->');">
      <i class="fa fa-rotate-right"></i> Làm mới
    </a>
  </div>
</div>
</div>
</div>
<div class="tbtitle2 link00" >
<div class="left"></div>
<div class="right"></div>
<form name="f" id="f" method="post"  action="index.php?do=articlelist&act=dellist&cid=1">
<table class="br1"  style="border-bottom:0px" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="3%" height="25" align="center" class="brbottom">
      <input type="checkbox" onclick="checkAll();"  name="all"/>
    </td>
    <!--{if $tinhnang.hinhanh ==1}-->
    <td width="6%"  height="25"  align="center" class="brbottom brleft hidden-xs">
      <strong>Hình ảnh</strong>
    </td>
    <!--{/if}-->
    
    <td  height="25"  align="left" class="brbottom brleft paleft">
      <strong> Tiêu đề </strong>
    </td>
    <!--{if $tinhnang.price ==1}-->
    <td width="8%" height="25" align="center" class="brbottom brleft hidden-xs">
      <strong>Giá</strong>
    </td>
    <!--{/if}-->
    <!--{if $tinhnang.new ==1}-->
    <td width="8%" height="25" align="center" class="brbottom brleft">
      <strong>Mới</strong>
    </td>
    <!--{/if}-->
    <!--{if $tinhnang.hot ==1}-->
    <td width="8%" height="25" align="center" class="brbottom brleft">
      <strong>Nổi bật</strong>
    </td>
    <!--{/if}-->
    <!--{if $tinhnang.mostview ==1}-->
    <td width="8%" height="25" align="center" class="brbottom brleft">
      <strong>Xem nhiều</strong>
    </td>
    <!--{/if}-->
    <td width="8%" height="25" align="center" class="brbottom brleft">
      <strong>Show</strong>
    </td>
    <td width="5%" height="25" align="center" class="brbottom brleft">
      <strong>Thứ tự</strong>
    </td>
 
    <td width="6%" height="25" align="center" class="brbottom brleft">
      <strong>Edit</strong>
    </td>
  </tr>
  <!--{section name=i loop=$view}-->
  <!--{if $smarty.section.i.index%2 eq 0}-->
  <!--{assign var="class" value="bgno"}-->
  <!--{else}-->
  <!--{assign var="class" value="bgf2"}-->
  <!--{/if}-->
  <tr class="<!--{cycle values='bgno,bgf2'}-->"  onmouseout="this.className='<!--{$class}-->'" onmouseover="this.className='bgonmose'" id="g<!--{$view[i].mspid}-->">
  <td class="pa_t_b brbottom"  align="center">
    <input type="checkbox" value="<!--{$view[i].id}-->" name="iddel[]" id="check<!--{$smarty.section.i.index}-->">
  </td>
  <!--{if $tinhnang.hinhanh ==1}-->
  <td align="center" class="brleft pa_t_b  brbottom linkblack hidden-xs">
    <!--{if $view[i].img_thumb_vn neq ""}-->
    <img align="absmiddle" height="30" width="30"  src="../<!--{$view[i].img_thumb_vn}-->"   border="0" />
    <!--{/if}-->
  </td>
  <!--{/if}-->
  
  <td  align="left" class="brleft paleft pa_t_b  brbottom linkblack">
    <!--{$view[i].name_vn}-->
  </td>
  <!--{if $tinhnang.price ==1}-->
  <td  align="center" class="brleft pa_t_b  brbottom linkblack hidden-xs">
    <!--{insert name="showprice" idpr=$view[i].id}-->
  </td>
  <!--{/if}-->
  <!--{if $tinhnang.new ==1}-->
  <td align="center" class="brleft pa_t_b  brbottom">
    <span class="btn_checks checks_new <!--{if $view[i].new eq "1"}-->btn-success<!--{else}-->btn-danger<!--{/if}-->" data="<!--{$view[i].id}-->">
    <img src="images/<!--{$view[i].new}-->.png" alt="Show\Hide"  />
  </span>
</td>
<!--{/if}-->
<!--{if $tinhnang.hot ==1}-->
<td align="center" class="brleft pa_t_b  brbottom">
  <span class="btn_checks checks_nb <!--{if $view[i].hot eq "1"}-->btn-success<!--{else}-->btn-danger<!--{/if}-->" data="<!--{$view[i].id}-->">
  <img src="images/<!--{$view[i].hot}-->.png" alt="Show\Hide"  />
</span>
</td>
<!--{/if}-->
<!--{if $tinhnang.mostview ==1}-->
<td align="center" class="brleft pa_t_b  brbottom">
<span class="btn_checks checks_mv <!--{if $view[i].mostview eq "1"}-->btn-success<!--{else}-->btn-danger<!--{/if}-->" data="<!--{$view[i].id}-->">
<img src="images/<!--{$view[i].mostview}-->.png" alt="Show\Hide"  />
</span>
</td>
<!--{/if}-->
<td  align="center" class="brleft pa_t_b  brbottom">
<span class="btn_checks status_active <!--{if $view[i].active eq "1"}-->btn-success<!--{else}-->btn-danger<!--{/if}-->" data="<!--{$view[i].id}-->">
<img src="images/<!--{$view[i].active}-->.png" alt="Show\Hide"  />
</span>
</td>
<td align="center" class="brleft pa_t_b  brbottom linkblack">
<input type="text" name="ordering[]" class="InputOrder"  value="<!--{$view[i].num}-->" size="2">
<input type="hidden" name="id[]" value="<!--{$view[i].id}-->" />
</td>

<td  align="center" class="brleft pa_t_b  brbottom">
<a href="index.php?do=articlelist&act=edit&comp=<!--{$smarty.request.comp}-->&id=<!--{$view[i].id}-->" title="Eidt">
<img src="images/icon3.gif"  align="center"/> Sửa
</a>
</td>
</tr>
<!--{/section}-->
</table>
</form>
<div class="clearfix"></div>
<!--{if $Checkpg eq 1}-->
<ul class="pagination">
<!--{$linkpg}-->
</ul>
<!--{/if}-->
</div>
</div>
</div>
<div class="clear"></div>
</div>
</div>
<script type="text/javascript">
$(document).on('click','.checks_new',function(){

var checks_new = ($(this).hasClass("btn-success")) ? '0' : '1';

//var prsized = document.querySelector('input[type=radio][name=optionsize]:checked');

var msg = (checks_new=='0')? 'Ẩn' : 'Hiển thị';

if(confirm("Bạn muốn "+ msg)){

var current_element = $(this);

url = "<!--{$path_admin_url}-->/ajax_active/new.php";

$.ajax({

type:"POST",

url: url,

data: {id:$(current_element).attr('data'),checks_new:checks_new},

success: function(data)

{

location.reload();

}



});

}

});

$(document).on('click','.checks_nb',function(){

var checks_nb = ($(this).hasClass("btn-success")) ? '0' : '1';

//var prsized = document.querySelector('input[type=radio][name=optionsize]:checked');

var msg = (checks_nb=='0')? 'Ẩn' : 'Hiển thị';

if(confirm("Bạn muốn "+ msg)){

var current_element = $(this);

url = "<!--{$path_admin_url}-->/ajax_active/hot.php";

$.ajax({

type:"POST",

url: url,

data: {id:$(current_element).attr('data'),checks_nb:checks_nb},

success: function(data)

{

location.reload();

}



});

}

});
$(document).on('click','.checks_mv',function(){

var checks_mv = ($(this).hasClass("btn-success")) ? '0' : '1';

//var prsized = document.querySelector('input[type=radio][name=optionsize]:checked');

var msg = (checks_mv=='0')? 'Ẩn' : 'Hiển thị';

if(confirm("Bạn muốn "+ msg)){

var current_element = $(this);

url = "<!--{$path_admin_url}-->/ajax_active/mostview.php";

$.ajax({

type:"POST",

url: url,

data: {id:$(current_element).attr('data'),checks_mv:checks_mv},

success: function(data)

{

location.reload();

}



});

}

});

$(document).on('click','.status_active',function(){

var actived = ($(this).hasClass("btn-success")) ? '0' : '1';

//var prsized = document.querySelector('input[type=radio][name=optionsize]:checked');

var msg = (actived=='0')? 'Ẩn' : 'Hiển thị';

if(confirm("Bạn muốn "+ msg)){

var current_element = $(this);

url = "<!--{$path_admin_url}-->/ajax_active/pr.php";

$.ajax({

type:"POST",

url: url,

data: {id:$(current_element).attr('data'),actived:actived},

success: function(data)

{

location.reload();

}



});

}

});

</script>
</div>