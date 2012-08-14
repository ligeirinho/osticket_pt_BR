<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die('Access Denied');

//List all staff members...not pagenating...
$sql='SELECT staff.staff_id, staff.group_id,staff.dept_id, firstname,lastname, username'.
     ',isactive,onvacation,isadmin,group_name,dept_name,DATE(staff.created) as created,lastlogin,staff.updated '.
     ' FROM '.STAFF_TABLE.' staff '.
     ' LEFT JOIN '.GROUP_TABLE.' groups ON staff.group_id=groups.group_id'.
     ' LEFT JOIN '.DEPT_TABLE.' dept ON staff.dept_id=dept.dept_id';
    
if($_REQUEST['dept'] && is_numeric($_REQUEST['dept'])){
    $id=$_REQUEST['dept'];
    $sql.=' WHERE staff.dept_id='.db_input($_REQUEST['dept']);
}
$users=db_query($sql.' ORDER BY lastname,firstname');
$showing=($num=db_num_rows($users))?"Staff Members":"Atendente não encontrado. <a href='admin.php?t=staff&a=new&dept=$id'>Add New User</a>.";        
?>
 <div class="msg">&nbsp;<?=$showing?>&nbsp;</div>
 <table width="100%" border="0" cellspacing=1 cellpadding=2>
    <form action="admin.php?t=staff" method="POST" name="staff" onSubmit="return checkbox_checker(document.forms['staff'],1,0);">
    <input type=hidden name='a' value='staff'>
    <input type=hidden name='do' value='mass_process'>
    <tr><td>
     <table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
        <tr>
	        <th width="7px">&nbsp;</th>
	        <th>Nome Completo</th>
            <th>Nonme de usuário</th>
            <th>Estado</th>
            <th>Grupo</th>
            <th>Departamento</th>
            <th>Criação</th>
            <th>Último Login</th>
        </tr>
        <?
        $class = 'row1';
        $total=0;
        $uids=($errors && is_array($_POST['uids']))?$_POST['uids']:null;
        if($users && db_num_rows($users)):
            while ($row = db_fetch_array($users)) {
                $sel=false;
                if(($uids && in_array($row['staff_id'],$uids)) or ($uID && $uID==$row['staff_id'])){
                    $class="$class highlight";
                    $sel=true;
                }
                $name=ucfirst($row['firstname'].' '.$row['lastname']);
                ?>
            <tr class="<?=$class?>" id="<?=$row['staff_id']?>">
                <td width=7px>
                  <input type="checkbox" name="uids[]" value="<?=$row['staff_id']?>" <?=$sel?'checked':''?>  onClick="highLight(this.value,this.checked);">
                <td><a href="admin.php?t=staff&id=<?=$row['staff_id']?>"><?=Format::htmlchars($name)?></a>&nbsp;</td>
                <td><?=$row['username']?></td>
                <td><?=$row['isactive']?'Active':'<b>Locked</b>'?>&nbsp;<?=$row['onvacation']?'(<i>vacation</i>)':''?></td>
                <td><a href="admin.php?t=grp&id=<?=$row['group_id']?>"><?=Format::htmlchars($row['group_name'])?></a></td>
                <td><a href="admin.php?t=dept&id=<?=$row['dept_id']?>"><?=Format::htmlchars($row['dept_name'])?></a></td>
                <td><?=Format::db_date($row['created'])?></td>
                <td><?=Format::db_datetime($row['lastlogin'])?>&nbsp;</td>
            </tr>
            <?
            $class = ($class =='row2') ?'row1':'row2';
            } //end of while.
        else: ?> 
            <tr class="<?=$class?>"><td colspan=8><b>Consulta retornou 0 resultado(s)</b></td></tr>
        <?
        endif; ?>
     </table>
    </td></tr>
    <?
    if(db_num_rows($users)>0): //Show options..
     ?>
    <tr><td>
        <div style="margin-left:20px;">
            Selecionar:&nbsp;
            <a href="#" onclick="return select_all(document.forms['staff'],true)">Todos</a>&nbsp;&nbsp;
            <a href="#" onclick="return toogle_all(document.forms['staff'],true)">Alterne</a>&nbsp;&nbsp;
            <a href="#" onclick="return reset_all(document.forms['staff'])">Nenhum</a>&nbsp;&nbsp;
        </div>
    </td></tr>
    <tr><td align="center">
        <input class="button" type="submit" name="enable" value="Habilitar" 
            onClick=' return confirm("Tem certeza que você deseja HABILITAR o(s) usuário(s) seleccionado(s)?");'>
        <input class="button" type="submit" name="disable" value="Bloquear" 
            onClick=' return confirm("Tem certeza que você deseja BLOQUEAR o(s) usuário(s) seleccionado(s)?");'>
        <input class="button" type="submit" name="delete" value="Excluir" 
            onClick=' return confirm("Tem certeza que você deseja EXCLUIR o(s) usuário(s) seleccionado(s)?");'>
    </td></tr>
    <?
    endif;
    ?>
    </form>
 </table>
