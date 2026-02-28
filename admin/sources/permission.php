<?php
$act = isset($_REQUEST['act'])?$_REQUEST['act']:"";
switch($act){
	case "editsm":
		if($_POST){
			$peroles = isset($_POST)?$_POST:"0";
			$uid = $_POST['id'];
			
			$sql = "DELETE from $GLOBALS[db_sp].permissions where uid=$uid ";
			$GLOBALS["sp"]->execute($sql);
			
			//die(print_r($peroles));
			foreach($peroles as $key=>$item)
			{
				if($key != 'id' && $key != 'addcheck' && $key != 'editcheck' && $key != 'delcheck' && $key != 'allcheck')
				{
					$perm = implode(",",$item);
					$sql = "
						INSERT INTO $GLOBALS[db_sp].permissions SET 
							 uid = $uid,
							`cid` = ".$key.", 
							`perm` = '".$perm."' 
					";
					$rs = $GLOBALS["sp"]->execute($sql);
					
				}
			}
		}
		$url = "index.php?do=permission&id=".$uid;
		page_transfer2($url);
	break;
	default:
		$uid  = $_GET["id"];
		$sql = "select * from $GLOBALS[db_sp].admin where id=$uid";
		$rs = $GLOBALS["sp"]->getRow($sql);
		$smarty->assign("viewuser",$rs);
		
		$html = '';
		$level = "";
		$str = "";
		
		$str = dequi('2',$html,$level,$uid);
		$smarty->assign("view",$str);
		
		////////check load cat menu
		$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid and cid=2 ";
		$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
		$list_mpms = '';
		if(in_array("1",explode(',',$rs_pms['perm'])))
			$list_mpms .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd0' type='checkbox' checked='checked' name='2[]' value='1' /></td>";
		else
			$list_mpms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd0' type='checkbox'  name='2[]' value='1' /></td>";
			
		if(in_array("2",explode(',',$rs_pms['perm'])))
			$list_mpms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit0' type='checkbox' checked='checked' name='2[]' value='2' /></td>";
		else
			$list_mpms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit0' type='checkbox'  name='2[]' value='2' /></td>";
		
		if(in_array("3",explode(',',$rs_pms['perm'])))
			$list_mpms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel0' type='checkbox' checked='checked' name='2[]' value='3' /></td>";
		else
			$list_mpms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel0' type='checkbox'  name='2[]' value='3' /></td>";
			
		if(in_array("4",explode(',',$rs_pms['perm'])))
			$list_mpms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall0' type='checkbox' checked='checked'  name='2[]' value='4' /></td>";
		else
			$list_mpms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall0' type='checkbox'  name='2[]' value='4' /></td>";
		
		$list_mpms = "
			
			 <tr onmouseout=\"this.className='bgno'\" onmouseover=\"this.className='bgonmose'\" class='bgno'>
			 	<td align='left' class='pa_t_b brbottom'> <div class='PemissionName'> Menu </div></td>
				".$list_mpms."
			 </tr>  
		 ";         
		$smarty->assign("viewMpms",$list_mpms);	
		///////////////////////
		
		////////Phân quyền khác//////////////////////////////////////
		$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid and cid=1 ";
		$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
		$listorther = $listorther1 = '';
		if(in_array("1",explode(',',$rs_pms['perm'])))
			$listorther .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd-1' type='checkbox' checked='checked' name='1[]' value='1' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd-1' type='checkbox'  name='1[]' value='1' /></td>";
			
		if(in_array("2",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-1' type='checkbox' checked='checked' name='1[]' value='2' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-1' type='checkbox'  name='1[]' value='2' /></td>";
		
		if(in_array("3",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-1' type='checkbox' checked='checked' name='1[]' value='3' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-1' type='checkbox'  name='1[]' value='3' /></td>";
			
		if(in_array("4",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-1' type='checkbox' checked='checked'  name='1[]' value='4' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-1' type='checkbox'  name='1[]' value='4' /></td>";
		
		$listorther1 = "
			 <tr onmouseout=\"this.className='bgno'\" onmouseover=\"this.className='bgonmose'\" class='bgno'>
			 	<td align='left' class='pa_t_b brbottom'> <div class='PemissionName'> Thông tin web site </div></td>
				".$listorther."
			 </tr>  
		 ";  
		 
		$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid and cid=-1 ";
		$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
		$listorther = $listorther2 = '';
		if(in_array("1",explode(',',$rs_pms['perm'])))
			$listorther .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd-2' type='checkbox' checked='checked' name='-1[]' value='1' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd-2' type='checkbox'  name='-1[]' value='1' /></td>";
			
		if(in_array("2",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-2' type='checkbox' checked='checked' name='-1[]' value='2' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-2' type='checkbox'  name='-1[]' value='2' /></td>";
		
		if(in_array("3",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-2' type='checkbox' checked='checked' name='-1[]' value='3' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-2' type='checkbox'  name='-1[]' value='3' /></td>";
			
		if(in_array("4",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-2' type='checkbox' checked='checked'  name='-1[]' value='4' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-2' type='checkbox'  name='-1[]' value='4' /></td>";
		
		$listorther2 = "
			 <tr onmouseout=\"this.className='bgno'\" onmouseover=\"this.className='bgonmose'\" class='bgno'>
			 	<td align='left' class='pa_t_b brbottom'> <div class='PemissionName'> Thành viên </div></td>
				".$listorther."
			 </tr>  
		 "; 
		 
		$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid and cid=-2 ";
		$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
		$listorther = $listorther3 = '';
		if(in_array("1",explode(',',$rs_pms['perm'])))
			$listorther .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd-3' type='checkbox' checked='checked' name='-2[]' value='1' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd-3' type='checkbox'  name='-2[]' value='1' /></td>";
			
		if(in_array("2",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-3' type='checkbox' checked='checked' name='-2[]' value='2' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-3' type='checkbox'  name='-2[]' value='2' /></td>";
		
		if(in_array("3",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-3' type='checkbox' checked='checked' name='-2[]' value='3' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-3' type='checkbox'  name='-2[]' value='3' /></td>";
			
		if(in_array("4",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-3' type='checkbox' checked='checked'  name='-2[]' value='4' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-3' type='checkbox'  name='-2[]' value='4' /></td>";
		
		$listorther3 = "
			 <tr onmouseout=\"this.className='bgno'\" onmouseover=\"this.className='bgonmose'\" class='bgno'>
			 	<td align='left' class='pa_t_b brbottom'> <div class='PemissionName'> Duyệt đơn hàng </div></td>
				".$listorther."
			 </tr>  
		 ";   
		 
		$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid and cid=-3 ";
		$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
		$listorther = $listorther4 = '';
		if(in_array("1",explode(',',$rs_pms['perm'])))
			$listorther .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd-4' type='checkbox' checked='checked' name='-3[]' value='1' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd-4' type='checkbox'  name='-3[]' value='1' /></td>";
			
		if(in_array("2",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-4' type='checkbox' checked='checked' name='-3[]' value='2' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-4' type='checkbox'  name='-3[]' value='2' /></td>";
		
		if(in_array("3",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-4' type='checkbox' checked='checked' name='-3[]' value='3' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-4' type='checkbox'  name='-3[]' value='3' /></td>";
			
		if(in_array("4",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-4' type='checkbox' checked='checked'  name='-3[]' value='4' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-4' type='checkbox'  name='-3[]' value='4' /></td>";
		
		$listorther4 = "
			 <tr onmouseout=\"this.className='bgno'\" onmouseover=\"this.className='bgonmose'\" class='bgno'>
			 	<td align='left' class='pa_t_b brbottom'> <div class='PemissionName'> Tỉnh thành </div></td>
				".$listorther."
			 </tr>  
		 ";
		 
		 
		$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid and cid=-4 ";
		$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
		$listorther = $listorther5 = '';
		if(in_array("1",explode(',',$rs_pms['perm'])))
			$listorther .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd-5' type='checkbox' checked='checked' name='-4[]' value='1' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd-5' type='checkbox'  name='-4[]' value='1' /></td>";
			
		if(in_array("2",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-5' type='checkbox' checked='checked' name='-4[]' value='2' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-5' type='checkbox'  name='-4[]' value='2' /></td>";
		
		if(in_array("3",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-5' type='checkbox' checked='checked' name='-4[]' value='3' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-5' type='checkbox'  name='-4[]' value='3' /></td>";
			
		if(in_array("4",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-5' type='checkbox' checked='checked'  name='-4[]' value='4' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-5' type='checkbox'  name='-4[]' value='4' /></td>";
		
		$listorther5 = "
			 <tr onmouseout=\"this.className='bgno'\" onmouseover=\"this.className='bgonmose'\" class='bgno'>
			 	<td align='left' class='pa_t_b brbottom'> <div class='PemissionName'> Duyệt bình luận </div></td>
				".$listorther."
			 </tr>  
		 ";		 
		 
		$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid and cid=-5 ";
		$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
		$listorther = $listorther6 = '';
		if(in_array("1",explode(',',$rs_pms['perm'])))
			$listorther .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd-6' type='checkbox' checked='checked' name='-5[]' value='1' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd-6' type='checkbox'  name='-5[]' value='1' /></td>";
			
		if(in_array("2",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-6' type='checkbox' checked='checked' name='-5[]' value='2' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-6' type='checkbox'  name='-5[]' value='2' /></td>";
		
		if(in_array("3",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-6' type='checkbox' checked='checked' name='-5[]' value='3' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-6' type='checkbox'  name='-5[]' value='3' /></td>";
			
		if(in_array("4",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-6' type='checkbox' checked='checked'  name='-5[]' value='4' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-6' type='checkbox'  name='-5[]' value='4' /></td>";
		
		$listorther6 = "
			 <tr onmouseout=\"this.className='bgno'\" onmouseover=\"this.className='bgonmose'\" class='bgno'>
			 	<td align='left' class='pa_t_b brbottom'> <div class='PemissionName'> Sản phẩm hot </div></td>
				".$listorther."
			 </tr>  
		 ";
		 
		$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid and cid=-6 ";
		$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
		$listorther = $listorther7 = '';
		if(in_array("1",explode(',',$rs_pms['perm'])))
			$listorther .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd-7' type='checkbox' checked='checked' name='-6[]' value='1' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd-7' type='checkbox'  name='-6[]' value='1' /></td>";
			
		if(in_array("2",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-7' type='checkbox' checked='checked' name='-6[]' value='2' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit-7' type='checkbox'  name='-6[]' value='2' /></td>";
		
		if(in_array("3",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-7' type='checkbox' checked='checked' name='-6[]' value='3' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel-7' type='checkbox'  name='-6[]' value='3' /></td>";
			
		if(in_array("4",explode(',',$rs_pms['perm'])))
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-7' type='checkbox' checked='checked'  name='-6[]' value='4' /></td>";
		else
			$listorther .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall-7' type='checkbox'  name='-6[]' value='4' /></td>";
		
		$listorther7 = "
			 <tr onmouseout=\"this.className='bgno'\" onmouseover=\"this.className='bgonmose'\" class='bgno'>
			 	<td align='left' class='pa_t_b brbottom'> <div class='PemissionName'> So Sánh Giá Đối Thủ Cạnh Tranh </div></td>
				".$listorther."
			 </tr>  
		 ";
		 
		$listortherOunt =  $listorther1 . $listorther2 . $listorther3 . $listorther4 . $listorther5 . $listorther6 . $listorther7;           
		$smarty->assign("viewOrther",$listortherOunt);	 
		///////////////////////
				
		$template = "permission/edit.tpl";
	break;
}
$smarty->assign("tabmenu",0);
$smarty->display("header.tpl");
$smarty->display($template);
$smarty->display("footer.tpl");
function dequi($root,&$html,$level,$uid){
	global $db,$flash;
	$sql = "select * from $GLOBALS[db_sp].categories where  pid=".$root." order by num asc ";
	$all = $GLOBALS["sp"]->getAll($sql);
	if( ceil(count($all)) > 0){
		for($i=0;$i<count($all);$i++){
			$flash++;
			if(($flash % 2) == 0)
				$bg = "bgno";
			else
				$bg = "bgf2";
			$class = "";
			$class = "class='PemissionName".$level."'";
			$list_pms = "";
			////load check da phan quyen
			
			$sql_pms = "select * from $GLOBALS[db_sp].permissions where uid=$uid  and cid=".$all[$i]['id'];
			$rs_pms = $GLOBALS["sp"]->getRow($sql_pms);
			
			if(in_array("1",explode(',',$rs_pms['perm'])))
				$list_pms .= "<td  align='center' class='brleft pa_t_b  brbottom'><input id='checkadd".$flash."' type='checkbox' checked='checked' name='".$all[$i]['id']."[]' value='1' /></td>";
			else
				$list_pms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkadd".$flash."' type='checkbox'  name='".$all[$i]['id']."[]' value='1' /></td>";
				
			if(in_array("2",explode(',',$rs_pms['perm'])))
				$list_pms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit".$flash."' type='checkbox' checked='checked' name='".$all[$i]['id']."[]' value='2' /></td>";
			else
				$list_pms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkedit".$flash."' type='checkbox'  name='".$all[$i]['id']."[]' value='2' /></td>";
			
			if(in_array("3",explode(',',$rs_pms['perm'])))
				$list_pms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel".$flash."' type='checkbox' checked='checked' name='".$all[$i]['id']."[]' value='3' /></td>";
			else
				$list_pms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkdel".$flash."' type='checkbox'  name='".$all[$i]['id']."[]' value='3' /></td>";
				
			if(in_array("4",explode(',',$rs_pms['perm'])))
				$list_pms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall".$flash."' type='checkbox' checked='checked'  name='".$all[$i]['id']."[]' value='4' /></td>";
			else
				$list_pms .= "<td align='center' class='brleft pa_t_b  brbottom' ><input id='checkall".$flash."' type='checkbox'  name='".$all[$i]['id']."[]' value='4' /></td>";
					
			$html .= "
				<tr class='".$bg."'  onmouseover=\"this.className='bgonmose'\" onmouseout=\"this.className='".$bg."'\">
				
					<td class='pa_t_b brbottom' align='left'> <div ".$class."> ".$all[$i]['name_vn']."</div></td>
					".$list_pms."
				 </tr>			
			";
			
			dequi($all[$i]['id'],$html,$level+1,$uid);
		}
	}
	return $html;
}
?>