<?php
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
class UserColumnRenderer implements DBTableList_Renderer_Sigma_ColumnRenderer {
    public function renderColumn($name,$column) {
        return $column;
    }
}
class UserRowRenderer implements DBTableList_Renderer_Sigma_RowRenderer {
    private $admin = null;
    public function __construct(& $admin) {
        $this->admin=$admin;
    }
    public function renderRow(& $tpl,$row) {
        $user = $this->admin->getUsers('perm',array(
             'auth_user_id' => $row['organization_user_id']
        ));
        $tpl->setVariable('usr_handle',$user[0]['handle']);
        $tpl->setVariable('usr_name',$user[0]['name']);
        $tpl->setVariable('usr_email',$user[0]['email']);
        $active = $user[0]['is_active'];
        if ($active) {
            $active = '<img src="'.IMAGES_DIR.'/active.png" alt="Aktiv" />';
        } else {
            $active = '<img src="'.IMAGES_DIR.'/inactive.png" alt="Inktiv" />';
        }
        $tpl->setVariable('usr_active',$active);
        $time = null;
        if ($user[0]['lastlogin']!='') {
            $time = date("d.m.Y H:i:s",strtotime($user[0]['lastlogin']));
        }
        $tpl->setVariable('usr_lastlogin',$time);
    }
}
?>