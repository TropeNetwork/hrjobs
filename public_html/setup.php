<?php

require_once 'Config.php';
require_once 'DB.php';
require_once 'HTML/Template/Sigma.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';

$config = new Config;
$root =& $config->parseConfig(dirname(__FILE__).'/../config/config.xml', 'XML');

if (PEAR::isError($root)) {
    die('Error while reading configuration: ' . $root->getMessage());
}

$settings = $root->toArray();
$settings = $settings['root']['conf'];
$initialized = $settings['setup']['initialized'];

$hrconf = new Config;
$hrroot =& $hrconf->parseConfig(dirname(__FILE__).'/../config/hradmin.xml', 'XML');
if (PEAR::isError($hrroot)) {
    die('Error while reading configuration: ' . $hrroot->getMessage());
}
$hrsettings = $hrroot->toArray();
$hrsettings = $hrsettings['root']['conf'];


$tpl =& new HTML_Template_Sigma('skins/default/');
if ($initialized) {
    include_once 'configuration.inc';
    include_once 'hradmin.config.inc';
    if (!$usr->isLoggedIn()) {
        $tpl->loadTemplateFile('login.html');
        $tpl->setVariable('base',HTML_BASE);
        $tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
        $tpl->show();
        exit;
    }  else {
        header('Location: index.php');
    }
}
$select_db_type = array(
    'mysql'     => 'MySQL (for MySQL <= 4.0)',
    'mysqli'    => 'MySQL (for MySQL >= 4.1)',
    'pgsql'     => 'PostgreSQL',
    'dbase'     => 'dBase',
    'fbsql'     => 'FrontBase',  
    'ibase'     => 'InterBase',  
    'ifx'       => 'Informix',    
    'msql'      => 'Mini SQL',   
    'mssql'     => 'Microsoft SQL Server',
    'oci8'      => 'Oracle 7/8/9',   
    'odbc'      => 'ODBC',   
    'sqlite'    => 'SQLite', 
    'sybase'    => 'Sybase', 
);
$tpl->loadTemplateFile('setup.html');
$form = new HTML_QuickForm('setup','POST');

$form->addElement('text','base', _("Base"),
            array('maxlength'=>'60',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('text','theme', _("Base"),
            array('maxlength'=>'60',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('text','skin', _("Skin"),
            array('maxlength'=>'60',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('select','db_type', _("DB Type"),$select_db_type);
$form->addElement('text','db_host', _("Host"),
            array('maxlength'=>'60',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('text','db_name', _("Name"),
            array('maxlength'=>'20',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('text','db_user', _("User"),
            array('maxlength'=>'20',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('text','db_pass', _("Password"),
            array('maxlength'=>'20',
                  'size'=>'20',
                  'class'=>'formFieldLong'));

$form->addElement('submit','save',_("Continue"));
$form->addElement('submit','db_check',_("Check Connection"));
//$form->addElement('submit','db_create',_("Create Database"));
$defaults = array(
    'base'     => $settings['html']['base'],
    'theme'     => $settings['theme']['base'],
    'skin'     => $settings['theme']['skin'],
    'db_type'     => $settings['database']['type'],
    'db_host'     => $settings['database']['host'],
    'db_user'     => $settings['database']['user'],
    'db_name'     => $settings['database']['name'],
    'db_pass'     => $settings['database']['pass'],
);
$form->setDefaults($defaults);

$form->registerRule ('db_connect', 'callback', 'db_connect');
$form->registerRule ('db_exists', 'callback', 'db_exists');
$form->registerRule ('db_select', 'callback', 'db_select');
$form->addRule('base',          "Base darf nicht leer sein", 'required');
$form->addRule('theme',         "Base darf nicht leer sein", 'required');
$form->addRule('skin',          "Skin darf nicht leer sein", 'required');
$form->addRule('db_host',       "Host darf nicht leer sein", 'required');
$form->addRule('db_name',       "Name darf nicht leer sein", 'required');
$form->addRule('db_user',       "User darf nicht leer sein", 'required');
//$form->addRule('db_pass',       "Passwort darf nicht leer sein", 'required');
//$form->addRule('db_admin_pass', "Passwort darf nicht leer sein", 'required');

    
$form->addRule(array(
    'db_type',
    'db_host', 
    'db_user',
    'db_pass'), 'Keine Verbindung zum Server!', 'db_connect');

$form->addRule(array(
    'db_type',
    'db_host', 
    'db_name',
    'db_user',
    'db_pass'), 'Keine Vebindung zur Datenbank!', 'db_select');        

if ($form->validate()) {
    if ($form->exportValue('save')) {
        $settings['html']['base'] = $form->exportValue('base');
        $settings['theme']['base'] = $form->exportValue('theme');
        $settings['theme']['skin'] = $form->exportValue('skin');
        $settings['database']['type'] = $form->exportValue('db_type');
        $settings['database']['host'] = $form->exportValue('db_host');
        $settings['database']['name'] = $form->exportValue('db_name');
        $settings['database']['user'] = $form->exportValue('db_user');
        $settings['database']['pass'] = $form->exportValue('db_pass');
        $settings['setup']['initialized'] = false;
        $settings = setupHrAdmin($settings);
        $config = new Config;
        $root =& $config->parseConfig($settings, 'phparray');
        $res = $config->writeConfig(dirname(__FILE__).'/../config/config.xml', 'XML');
        if (PEAR::isError($res)) {
            $tpl->setVariable('errors','<div class="error">'.$res->getMessage().'</div>');
        } else {
            header("Location: setupuser.php");
            exit;
        }
    }
}

$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$form->accept($renderer);

$tpl->setVariable('title',"Setup");
$tpl->show();

function setupHrAdmin($settings = array()) {
    global $conf;
    include_once 'hradmin.config.inc';
    $appid = setupApplication($admin->perm);
    if (PEAR::isError($appid)) {
        die('impossible to initialize: ' . $appid->getMessage());
    }
    $settings['application'] = $appid;
    $admin->perm->setCurrentApplication($appid);
    $areaid = setupArea($admin->perm,$appid);
    if (PEAR::isError($areaid)) {
        die('impossible to initialize: ' . $areaid->getMessage());
    }
    $settings['area'] = $areaid;
    $settings = setupRights($admin->perm,$areaid,$settings);
    $settings['groups']['users'] = setupGroup($admin->perm,'USERS');
    $settings['groups']['admins'] = setupGroup($admin->perm,'ADMINS');
    setGroupRights($admin,$settings['groups']['users'],array($settings['rights']['login']=>3));
    setGroupRights($admin,$settings['groups']['admins'],array($settings['rights']['system']=>3,$settings['rights']['login']=>3));
    return $settings;
}
function setupGroup($adminPerm,$group_name) {
    $groups = $adminPerm->getGroups(array(
        'with_rights'=>true,
        'fields'     => array(
            "group_define_name", 
            "description", 
            "name", 
            "group_id"
         )
    ));

    foreach($groups as $group) {
        if ($group['group_define_name']==$group_name) {
            return $group['group_id'];
        }
    }
    
    $data   = array(
       'group_define_name' => $group_name,
       'is_active'         => 'Y',
    );  
    $group_id = $adminPerm->addGroup($data);
        
    if (DB::isError($group_id)) {
        var_dump($group_id);
        exit;
    }
    $data   = array(
        'section_id'   => $group_id,
        'section_type' => LIVEUSER_SECTION_GROUP, 
        'language_id'  => 0,
        'name'         => $group_name, 
        'description'  => $group_name);
    $adminPerm->addTranslation($data);
    return $group_id;
}

function setGroupRights($admin,$group_id,$newRights = array()) {
    $params = array('fields'  => array('right_id','right_level'),
                    'filters' => array('group_id' => $group_id));
                            
    $rights = $admin->perm->getRights($params);
    
    if (is_array($rights)) {
        foreach ($rights as $right => $level) {
            $filters  = array('right_id' => $right,
                              'group_id' => $group_id);
            $removed = $admin->perm->revokeGroupRight($filters);
        }
    }
    if (!empty($newRights)) {
        foreach ($newRights as $newRight => $level) {
            if ((int)$level > 0) {                
            
                $data = array('right_level' => $level);
                $filters = array('right_id' => $newRight,
                                 'group_id' => $group_id);
  
                if (!$admin->perm->updateGroupRight($data, $filters)) {
                    $admin->perm->grantGroupRight(array(
                        'right_level'   => $level,
                        'right_id'      => $newRight,
                        'group_id'      => $group_id
                    ));
                }
            }
        }    
    }
}

function setupApplication($adminPerm) {
    $apps = $adminPerm->getApplications();
    foreach($apps as $app) {
        if ($app['application_define_name']=='HRJOBS') {
            return $app['application_id'];
        }
    }
    $data = array(
        'application_define_name'   => 'HRJOBS',
    );
    $app_id = $adminPerm->addApplication($data);
    $data = array(
        'name'                      => 'HRjobs',
        'description'               => 'Jobportal',
        'language_id'               => '0',
        'section_id'   => $app_id,
        'section_type' => LIVEUSER_SECTION_APPLICATION,
    );
    $adminPerm->addTranslation($data);
    return $app_id;
}
function setupArea($adminPerm,$appid) {
    $areas = $adminPerm->getAreas(array('application_id' => $appid ));
    foreach($areas as $area) {
        if ($area['area_define_name']=='FRONTEND') {
            return $area['area_id'];
        }
    }
    $data = array(
        'application_id'    => $appid,
        'area_define_name'  => 'FRONTEND'
    );
    $area_id = $adminPerm->addArea($data);
    if (DB::isError($area_id)) {
        var_dump($area_id);
        exit;
    } 
    $data      = array(
        'section_id'    => $area_id,
        'section_type'  => LIVEUSER_SECTION_AREA, 
        'language_id'   => '0',
        'name'          => 'Frontend', 
        'description'   => 'Frontend für das Jobportal'
    );                
    $adminPerm->addTranslation($data);
    return $area_id;
}
function setupRights($adminPerm,$areaid,$settings) {
    $rights = $adminPerm->getRights(array('area_id' => $areaid));
    //print_r($rights);
    foreach($rights as $right) {
        if ($right['right_define_name']=='LOGIN') {
            $login=true;
        }
        if ($right['right_define_name']=='SYSTEM') {
            $admin=true;
        }
    }
    if (!$login) {
        $res = addRight($adminPerm,$areaid,'LOGIN',"Login","Login Recht");
        if (PEAR::isError($res)) {
            print 'impossible to initialize: ' . $res->getMessage();
        } else {
            $settings['rights']['login'] = $res;
        }
    }
    if (!$admin) {
        $res = addRight($adminPerm,$areaid,'SYSTEM',"System","System Recht");
        if (PEAR::isError($res)) {
            print 'impossible to initialize: ' . $res->getMessage();
        } else {
            $settings['rights']['system'] = $res;
        }       
    }
    return $settings;
}

function addRight($adminPerm,$area_id,$right,$name,$description) {
    $data = array(
        'area_id'           => $area_id,
        'right_define_name' => $right
    );
    $right_id = $adminPerm->addRight($data);
    if (DB::isError($right_id)) {
        var_dump($right_id);
        exit;
    } 
    $data      = array(
        'section_id'    => $right_id,
        'section_type'  => LIVEUSER_SECTION_RIGHT, 
        'language_id'   => '0',
        'name'          => $name, 
        'description'   => $description
    );                
    $adminPerm->addTranslation($data);
    return $right_id;
}

function db_create($params) {
    $host = $params[0];
    $name = $params[1];
    $user = $params[2];
    $pass = $params[3];
    $link = mysql_connect($host, $user, $pass);
    if (!$link) {
        return false;
    }  
    $db_list = mysql_list_dbs();
    $i = 0;
    $cnt = mysql_num_rows($db_list);
    $exists = false;
    while ($i < $cnt) {
        if (mysql_db_name($db_list, $i)===$name) {
            $exists = true;
            break;
        }
        $i++;
    }  
    if (!$exists) {
        $db_selected = mysql_select_db('mysql', $link);
        if (!$db_selected) {
            die('Select to mysql db failed: '. mysql_error());
        }
        if (!mysql_query( 'CREATE DATABASE '.$name)) {
            die('Create failed: '. mysql_error());
        }
    }
    $db_selected = mysql_select_db($name, $link);
    if (!$db_selected) {
        mysql_close($link);
        return false;
    }
    $handle = fopen ("../etc/mysql/create_tables.sql", "r");
    while (!feof($handle)) {
        $line = fgets($handle);   
        $buffer .= $line;
        if (substr($line,strlen($line)-2,2)===";\n" ) {
            if (!mysql_query($buffer)) {
                die('Create tables failed: '. mysql_error());
            }
            $buffer = '';
        }
        
    }
    fclose($handle);
    mysql_close($link);
    return true;
}
function db_connect($params) {
    $type = $params[0];
    $host = $params[1];
    $user = $params[2];
    $pass = $params[3];
    $dsn = "$type://$user:$pass@$host";
    $db =& DB::connect($dsn, $options);
    if (PEAR::isError($db)) {
        return false;
    }

    $db->disconnect();
    return true;
}

function db_select($params) {
    $type = $params[0];
    $host = $params[1];
    $name = $params[2];
    $user = $params[3];
    $pass = $params[4];
    $dsn = "$type://$user:$pass@$host/$name";
    $db =& DB::connect($dsn, $options);
    if (PEAR::isError($db)) {
        return false;
    }

    $db->disconnect();
    return true;
}

function db_exists($params) {
    $host = $params[0];
    $name = $params[1];
    $user = $params[2];
    $pass = $params[3];
    $link = mysql_connect($host, $user, $pass);
    if (!$link) {
        mysql_close($link);
        return false;
    }
    $db_list = mysql_list_dbs();
    $i = 0;
    $cnt = mysql_num_rows($db_list);
    $exists = false;
    while ($i < $cnt) {
        if (mysql_db_name($db_list, $i)===$name) {
            $exists = true;
            break;
        }
        $i++;
    }
    mysql_close($link);
    return $exists;
}
?>