<div id="panelRight">
  
  <div class="SubRightTop1">
  		<a href="index.php?do=orders" class="First">Đơn hàng</a>
 </div>
  
  <div class="SubRightCenter">
    <ul>
        <li> 
	  		<a href="javascript:void(0)" title="Delete" onclick="ChangeAction('index.php?do=orders&act=dellist&s= <!--{$smarty.request.s}-->');"> <img src="images/Icon02.png"  /> </a> 
	    </li>
      <!--{if $smarty.request.s eq 0}-->
      	<li> 
	  		<a href="javascript:void(0)" title="Finish" onclick="ChangeAction('index.php?do=orders&act=finish')" > <img src="images/Icon03.png" alt="Show"  /> </a>
	  	</li>
      <!--{/if}-->
     	 
    </ul>
    <div class="MainTable">
	<form name="f" id="f" method="post"  action="index.php?do=orders&act=dellist&cid=1">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" >
        <tr  class="TableTop">
          <td align="center" valign="middle" width="5%">
		  	<input type="checkbox" onclick="checkAll();"  name="all"/>
          </td>
          <td align="left" valign="middle"  width="7%" class="SubText" > id </td>
          <td align="left" valign="middle"  width="18%" class="SubText"> date </td>
		  <td align="left" valign="middle"  width="28%" class="SubText"> name </td>
          <td align="left" valign="middle"  width="18%" class="SubText"> Total </td>
           <td align="left" valign="middle"  width="5%" class="SubText"> Type </td>
          <td align="left" valign="middle"  width="10%" class="SubText"> Payed </td>
          <td align="center" valign="middle"  width="10%" class="SubText">  Action </td>
        </tr>
        <!--{section name=i loop=$view}-->
			<!--{if $smarty.section.i.index%2 eq 0}-->
				<!--{assign var="class" value="Bgf1"}-->
			<!--{else}-->
				<!--{assign var="class" value="Bgf2"}-->
			<!--{/if}-->
        <tr class="<!--{cycle values='Bgf1,Bgf2'}-->">
          <td align="center" valign="middle" class="BrBottom">
		  		<input type="checkbox" value="<!--{$view[i].id}-->" name="iddel[]" id="check<!--{$smarty.section.i.index}-->">
          </td>
          <td align="left" valign="middle" class="BrBottom TextNone">
		  	<!--{$view[i].id}-->
          </td>
		  
		  <td class="SubText01 BrBottom TextNone">
		  	<!--{$view[i].time_order}-->
          </td>
		  
          <td  align="left" valign="middle" class="SubText01 BrBottom TextNone">
		  	 <!--{$view[i].name}-->
			
		  </td>
          
          <td  align="left" valign="middle" class="SubText01 BrBottom TextNone">
		  	 <!--{$view[i].total|number_format:0:".":","}-->
		  </td>
          
          <td  align="left" valign="middle" class="SubText01 BrBottom TextNone">
		  	 <!--{$view[i].type_pay}-->
		  </td>
          
          <td align="center" valign="middle" class="BrBottom">
		  	  <!--{if $view[i].is_payed eq "1"}-->
				<img src="images/active.png" alt="Show\Hide" width="30" />
			 <!--{else}--> 
				<img src="images/hide.png" alt="Show\Hide"   width="30" />
			 <!--{/if}-->
		  </td>
          <td align="center" valign="middle" class="BrBottom">
          	
          	 <a href="javascript:void(0)" onclick="window.open('index.php?do=orders&id=<!--{$view[i].id}-->&act=printer','mywindow','width=850,height=700,scrollbars=yes,menubar=yes')" title="Print"> 
				<img src="images/printer.png" alt="Print"  /> 
			</a> 
		  	<a href="javascript:void(0)" onclick="window.open('OrderDetail.php?id=<!--{$view[i].id}-->','mywindow','width=850,height=700,scrollbars=yes')" title="Edit"> 
				<img src="images/edit.png" alt="Edit"  /> 
			</a> 
           
		  </td>
        </tr>
        <!--{/section}-->
      </table>
	 </form> 
    </div>
	
	<div class="navcentre">
		<ul>
			<li>
				<!--{$link_url}-->
			</li>
		</ul>
	</div>
	
	<div class="Clear"></div>
  </div>

  <div class="SubRightBottom"></div>
</div>
