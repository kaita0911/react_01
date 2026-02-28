<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         <!--{include file="left.tpl"}-->
      </div>
      <div class="right_content conten">
         <div class="divtitle margin5">
            <div class="divleft">
               <div class="divright link00">
                <div class="btnseo">
                  
                  <button type="button" onclick="CheckPass();"><i class="fa fa-save"></i> Save</button>
                  <a href="javascript:history.go(-1)"><i class="fa fa-mail-reply"></i> Trở về</a>
               </div>
                  
               </div>
            </div>
         </div>
         <div class="tbtitle2 link00">
                <form name="allsubmit" id="frm" action="" method="post" enctype="multipart/form-data">
      <table  width="100%" border="0" cellspacing="15" cellpadding="0">
        <tr>
          <td width="10%"  valign="top" align="right">
            <strong>Password Old </strong>
          </td>
          <td valign="top" width="70%" align="left">
            <input type="password" value="" name="pwold" class="InputText" />
          </td>
          
        </tr>
        
        <tr>
          <td width="10%"  valign="top" align="right">
            <strong>Password New </strong>
          </td>
          
          <td valign="top" width="70%" align="left">
            <input type="password" value="" name="password" class="InputText" />
          </td>
          
        </tr>
        
        
        <tr>
          <td width="10%"  valign="top" align="right">
            <strong>Cfirm Password New </strong>
          </td>
          <td valign="top" width="70%" align="left">
            <input type="password" value="" name="password_conf" class="InputText" />
          </td>
        </tr>
        
        
        
        
        
      </table>
    </form>

         </div>
        
     
      </div>
   </div>
</div>

<script language="javascript">
function CheckPass(){
  var pwold = document.allsubmit.pwold;
  var password = document.allsubmit.password;
  var pwc = document.allsubmit.password_conf;
  if(pwold.value.length == ""){
    alert('Please enter Password Old.');
    pwold.focus();
    return false;
  }
  else if(password.value.length == ""){
    alert('Please enter Password.');
    password.focus();
    return false;
  }
  
  else if(document.allsubmit.password.value != document.allsubmit.password_conf.value){
    alert('Password and Confirm is not same.');
    pwc.focus();
    return false;
  }
  else{
    var pwold = pwold.value;
    jQuery.post('ajax/Checkip.php',{pwold:pwold,act:'changes'},function(data) {
      var obj = jQuery.parseJSON(data);
      if(obj.status != ''){ //loi
        alert(obj.status);
        pwold.focus();
        return false;
      }
      else{
        document.allsubmit.submit();
      }
      });
  }
  
}
</script>