<?php

class HRAdmin_Admin {
    private $permAdmin;
    public function __construct($permAdmin) {
        $this->permAdmin = $permAdmin;
    }

    public function addUserToGroup($user_id) {
        $this->permAdmin->addUserToGroup($user_id,HRADMIN_GROUP_USERS);
    }
    public function removeUserFromGroup($user_id) {
        $this->permAdmin->removeUserFromGroup($user_id,HRADMIN_GROUP_USERS);
    }
    public function addUserToAdminGroup($user_id) {
        $this->permAdmin->addUserToGroup($user_id,HRADMIN_GROUP_ADMINS);
    }
    public function removeUserFromAdminGroup($user_id) {
        $this->permAdmin->removeUserFromGroup($user_id,HRADMIN_GROUP_ADMINS);
    }
}

?>