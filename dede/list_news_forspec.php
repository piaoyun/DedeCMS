<?
require("config.php");
require("inc_typelink.php");
require("inc_page_list.php");
$conn = connectMySql();
$ut = new TypeLink();
//�����Ƿ��صı�������
if(empty($qtype)) $qtype="";
//��ȡ����ʱ�õ�����ʱ����
$sql = "";
$sqldd="";
//��ȡ�б�����ز���
$pagesize=20;
$orderby=" order by dede_art.ID desc ";
$sql = "Select dede_art.ID,dede_art.title,dede_art.typeid,dede_art.isdd,dede_art.stime,dede_art.click,dede_arttype.typename,dede_arttype.typedir,dede_art.rank,dede_art.spec,dede_art.picname From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.rank>=0 ";
$sqlcount = "Select count(ID) as dd From dede_art where dede_art.rank>=0 ";
$pageurl = "list_news_forspec.php?qtype=$qtype";

if(empty($arttoptype)) $arttoptype="";
if($arttoptype==0||$arttoptype=="")
{
	$sids = $ut->GetSunID($cuserLogin->getUserChannel(),"dede_art",1);
	if($sids!=""&&trim($sids)!="()"&&trim($sids)!="( )") $sqldd.=" And $sids";
}
else
{
	$sids = $ut->GetSunID($arttoptype,"dede_art",1);
	if($sids!=""&&trim($sids)!="()"&&trim($sids)!="( )") $sqldd.=" And $sids";
	$pageurl.="&arttoptype=".$arttoptype;
}

if(isset($keyword))
{
	$sqldd.=" And title like '%".$keyword."%'";
	$pageurl.="&keyword=".urlencode($keyword);
}
$sql.=$sqldd;
$sqlcount.=$sqldd;       
if(!isset($total_record))
{
      $rs=mysql_query($sqlcount,$conn);
      $row=mysql_fetch_object($rs);
      $total_record = $row->dd;
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>��������</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script>
//���ѡ���ļ����ļ���
function getCheckboxItem()
{
	var allSel="";
	if(document.form2.artids.value) return document.form2.artids.value;
	for(i=0;i<document.form2.artids.length;i++)
	{
		if(document.form2.artids[i].checked)
		{
			if(allSel=="")
				allSel=document.form2.artids[i].value;
			else
				allSel=allSel+","+document.form2.artids[i].value;
		}
	}
	return allSel;	
}
function reValue()
{
	var qstr,qtype;
    qstr=getCheckboxItem();
    qtype = "<?=$qtype?>";
	if(qstr=="") alert("��ûѡ���κ����ݣ�");
	else
	{
		if(qtype==""||qtype=="specartid")
			window.opener.document.form1.specartid.value+=","+qstr;
        else
        	window.opener.document.form1.speclikeid.value+=","+qstr;
		window.opener=true;
    	window.close();
    }
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='6' topmargin='6'>
<table width='100%' border='0' align='center' cellpadding='0' cellspacing='0'>
  <tr> 
    <td height='425' align='center' valign='top'>
	<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#666666">
        <form name="form2">
		<tr bgcolor="#E7E7E7"> 
          <td height="20" colspan="6" background="img/tbg.gif"><strong>&nbsp;�������б�</strong></td>
        </tr>
        <tr align="center" bgcolor="#FAFAF1"> 
          <td width="5%" height="18">ѡ��</td>
          <td width="43%" height="18">&nbsp;���±���</td>
          <td width="16%">����ʱ��</td>
          <td width="16%">��Ŀ</td>
            <td width="12%" height="18">�ȼ�</td>
            <td width="8%">����</td>
        </tr>
        <?
        $sql.=$orderby.get_limit($pagesize);
        if($total_record!=0)
        {
        	$rs = mysql_query($sql,$conn);
        	while($row=mysql_fetch_object($rs))
        	{
        		$ID = $row->ID;
        		$title = trim($row->title);
        		$btypeid = $row->typeid;
        		$dtime = $row->stime;
        		$typename = $row->typename;
        		$typefile = $art_dir."/".$row->typedir;
        		$rank=$row->rank;
        		$rs2 = mysql_query("Select * from dede_membertype where rank='$rank'",$conn);
        		$row2 = mysql_fetch_array($rs2);
        		$rankn = cn_substr($row2["membername"],8);
        		$linkfile=$ut->GetFileName($ID,$row->typedir,$row->stime,$row->rank);
        ?>
        <tr bgcolor="#FFFFFF" height="18"> 
          <td align='center'><input name="artids" type="checkbox" class="np" id="artids" value="<?=$ID?>"></td>
          <td><a href='<?=$linkfile?>' target='_blank'><?=$title?></a></td>
          <td align='center'><?=$dtime?></td>
          <td align='center'><a href='<?=$typefile?>'><?=$typename?></a></td>
          <td align='center'><?=$rankn?></td>
          <td align='center'>
             <?
			 if($row->spec > 0) echo "ר��";
			 else echo "����";
			 ?>
          </td>
        </tr>
        <?
               }
        }
        ?>
        <tr  bgcolor="#FAFAF1"> 
            <td height="24" colspan="6">
            &nbsp;<input type='button' name='b2' value=" ȷ�� " onClick="reValue()">
            </td>
        </tr>
		</form>
        <tr align="right" bgcolor="#E7E7E7"> 
          <td height="20" colspan="6"> 
            <?
          get_page_list($pageurl,$total_record,$pagesize);
          ?>
            &nbsp;&nbsp;</td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <form name="form1" method="get">
          <tr> 
            <td height="4"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td style="border: 1px solid #525252" height="26" align="center" background="img/tbg.gif">
            ������Ŀ��
            <select name="arttoptype">
                <option value="0" selected>--��ѡ��--</option>
                <?
				if($cuserLogin->getUserChannel()<=0)
					$ut->GetOptionArray(0,0,1);
				else
					$ut->GetOptionArray(0,$cuserLogin->getUserChannel(),1);
				?>
              </select> &nbsp;&nbsp;
            �ؼ��֣� 
            <input name="keyword" type="text" id="keyword" size="15">
            
            <input type="submit" name="Submit" value="ȷ��"></td>
          </tr>
          <tr> 
            <td colspan="2" height="4"></td>
          </tr>
        </form>
      </table></td>
  </tr></table></body></html>