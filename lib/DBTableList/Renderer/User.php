<?php
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
class UserColumnRenderer implements DBTableList_Renderer_Sigma_ColumnRenderer {
    public function renderColumn($name,$column) {
        return $column;
    }
}
class UserRowRenderer implements DBTableList_Renderer_Sigma_RowRenderer {
    private $objRightsAdminAuth = null;
    public function __construct(& $objRightsAdminAuth) {
        $this->objRightsAdminAuth=$objRightsAdminAuth;
    }
    public function renderRow(& $tpl,$row) {
        $user = $this->objRightsAdminAuth->getUsers(array(
             'auth_user_id' => array(
                'name'  => 'auth_user_id',
                'op' => '=', 
                'value' => $row['organization_user_id'], 
                'cond' => '')
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
        if ($user[0]['lastlogin']!='') {
            $time = date("d.m.Y H:i:s",strtotime($user[0]['lastlogin']));
        }
        $tpl->setVariable('usr_lastlogin',$time);
    }
}
?>