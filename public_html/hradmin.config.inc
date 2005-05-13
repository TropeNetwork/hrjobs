<?php

require_once 'DB.php';
require_once 'LiveUser.php';
require_once 'LiveUser/Admin.php';
require_once 'Log.php';

$conf = new Config;
$root =& $conf->parseConfig(dirname(__FILE__).'/../config/hradmin.xml', 'XML');
if (PEAR::isError($root)) {
    die('Error while reading configuration: ' . $root->getMessage());
}
$hradmin_settings = $root->toArray();
$hradmin_settings = $hradmin_settings['root']['conf'];
$dsn = 'mysql://'.$hradmin_settings['database']['user'].':'.
                  $hradmin_settings['database']['pass'].'@'.
                  $hradmin_settings['database']['host'].'/'.
                  $hradmin_settings['database']['name'];     
                  
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
                'name'          => 'DB_Local',
                'loginTimeout'  => 0,
                'expireTime'    => 3600,
                'idleTime'      => 1800,
                'dsn'           => $dsn,
                'allowDuplicateHandles' => false,
                //'passwordEncryptionMode' => 'MD5',
                'authTable'     => 'liveuser_users',                
                'authTableCols' => array(
                     'required' => array(
                        'auth_user_id' => array('type' => 'text', 'name' => 'auth_user_id'),
                        'handle'       => array('type' => 'text', 'name' => 'handle'),
                        'passwd'       => array('type' => 'text', 'name' => 'passwd')
                     ),
                     'optional' => array(
                        'lastlogin'      => array('type' => 'timestamp', 'name' => 'lastlogin'),
                        'is_active'      => array('type' => 'boolean',   'name' => 'is_active'),
                        'owner_user_id'  => array('type' => 'integer',   'name' => 'owner_user_id'),
                        'owner_group_id' => array('type' => 'integer',   'name' => 'owner_group_id')
                     ),
                    'custom' => array (
                        'name'  => array('type' => 'text', 'name' => 'name'),
                        'email' => array('type' => 'text', 'name' => 'email')
                     )
                )
            )
        ),
        'permContainer' => array(
            'type'      => 'Medium',
            'storage'   => array(
                        'DB' => array(
                            'connection' => $db,
                            'dsn'        => $dsn, 
                            'prefix'     => 'liveuser_'
                         )
            ),
            'groupTableCols' => array(
                     'required' => array(
                        'group_id'          => array('type' => 'integer', 'name' => 'group_id'),
                        'group_define_name' => array('type' => 'text', 'name' => 'group_define_name'),
                     ),
                     'optional' => array(
                        'is_active'      => array('type' => 'boolean',   'name' => 'is_active'),
                        'owner_user_id'  => array('type' => 'integer',   'name' => 'owner_user_id'),
                        'owner_group_id' => array('type' => 'integer',   'name' => 'owner_group_id')
                     ),
                    'custom' => array (
                     )
                )
        )
    );

$usr = LiveUser::factory($conf);

$handle = isset($_REQUEST['handle']) ? $_REQUEST['handle'] : null;
$password = isset($_REQUEST['passwd']) ? $_REQUEST['passwd'] : null;
$logout = isset($_REQUEST['logout']) ? $_REQUEST['logout'] : null;
$usr->init($handle, $password, $logout);

$admin =& LiveUser_Admin::factory($conf);

$admin->setAdminContainers();
?>