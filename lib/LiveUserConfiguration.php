<?php
/*
 *   Copyright (C) 2004  Gerrit Goetsch
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *   Author: Gerrit Goetsch <goetsch@cross-solution.de>
 *   
 *   $Id: LiveUserConfiguration.php,v 1.2 2005/07/01 08:42:27 goetsch Exp $
 */

require_once 'DB.php';
require_once 'LiveUser.php';
require_once 'LiveUser/Admin.php';

class LiveUserConfiguration {
    
    private $admin = null;
    private $permAdmin = null;
    private $authAdmin = null;
    private $user = null;
    private $configuration = null;
    
    public function __construct($settings) {
        $this->configuration = $this->getConfiguration($settings);
    }
    
    public function getLiveUser() {
        if (is_null($this->user)) {
            
            
            $handle = isset($_REQUEST['handle']) ? $_REQUEST['handle'] : null;
            $password = isset($_REQUEST['passwd']) ? $_REQUEST['passwd'] : null;
            $logout = isset($_REQUEST['logout']) ? $_REQUEST['logout'] : false;
            //$this->user->init($handle, $password, $logout);
            $this->user = LiveUser::singleton($this->configuration,$handle,$password,$logout);
            //echo "TEST";
        }
        return $this->user;
    }
    
    public function getAdmin() {
        if (is_null($this->admin)) {
            $this->admin =& LiveUser_Admin::factory($this->configuration);
            $this->admin->setAdminContainers();            
        }
        return $this->admin;
    }
    
    public function getPermAdmin() {
        if (is_null($this->permAdmin)) {
            $admin = $this->getAdmin();
            //$admin->setAdminPermContainer();
            $this->permAdmin = $admin->perm;
        }
        return $this->permAdmin;
    }
    
    public function getAuthAdmin() {
        if (is_null($this->authAdmin)) {
            $admin = $this->getAdmin();
            $this->authAdmin = $admin->auth;
        }
        return $this->authAdmin;
    }
    
    private function getConfiguration($settings) {
        $dsn=    $settings['database']['type'].'://'.
             $settings['database']['user'].':'.
             $settings['database']['pass'].'@'.
             $settings['database']['host'].'/'.
             $settings['database']['name'];

        $db = DB::connect($dsn);
    
        if (DB::isError($db)) {
            echo $db->getMessage() . ' ' . $db->getUserInfo();
        }
    
        $db->setFetchMode(DB_FETCHMODE_ASSOC);
    
    
        $conf =
        array(
            'autoInit' => true,
            'session'  => array(
                'name'     => 'PHPSESSION',
                'varname'  => 'ludata'
            ),
            'login' => array(
                'method'   => 'post',
                'force'    => false,
            ),
            'logout' => array(
                'trigger'  => 'logout',
                'redirect' => 'index.php',
                'destroy'  => true,
                'method'   => 'get',
            ),
            'authContainers' => array(
                array(
                    'type'          => 'DB',
                    'loginTimeout' => 0,
                    'expireTime'   => 0,
                    'idleTime'     => 0,
                    'allowDuplicateHandles'  => 1,
                    
                    //'passwordEncryptionMode' => 'PLAIN',
                    'storage' => array(
                        'dsn' => $dsn,
                        'prefix'     => 'liveuser_',
                        'alias' => array(
                            'auth_user_id' => 'auth_user_id',
                            'lastlogin' => 'lastlogin',
                            'is_active' => 'is_active',
                            'name'      => 'name',
                            'email'     => 'email'
                        ),
                        'fields' => array(
                            'lastlogin' => 'timestamp',
                            'is_active' => 'boolean',
                            'name'      => 'text',
                            'email'     => 'text' 
                        ),
                        'tables' => array(
                            'users' => array(
                                'fields' => array(
                                    'lastlogin' => false,
                                    'is_active' => false,
                                    'name'      => '',
                                    'email'     => '',
                                ),
                            ),
                        ),
                    )
                )
            ),
            'permContainer' => array(
                'type'      => 'Medium',
                'storage'   => array(
                    'DB' => array(
                        //'connection' => $db,
                        'dsn'        => $dsn, 
                        'prefix'     => 'liveuser_',
                    )
                )
            )
        );
        
        return $conf;        
    }
}
?>
