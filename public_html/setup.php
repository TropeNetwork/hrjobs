<?php

require_once 'Config.php';
require_once 'HTML/Template/Sigma.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';

$conf = new Config;
$root =& $conf->parseConfig(dirname(__FILE__).'/../config/config.xml', 'XML');

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
    } 
}
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
$form->addElement('text','db_admin_host', _("Host"),
            array('maxlength'=>'60',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('text','db_admin_name', _("Name"),
            array('maxlength'=>'20',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('text','db_admin_user', _("User"),
            array('maxlength'=>'20',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('text','db_admin_pass', _("Password"),
            array('maxlength'=>'20',
                  'size'=>'20',
                  'class'=>'formFieldLong'));

$form->addElement('submit','save',_("Weiter"));
$form->addElement('submit','db_check',_("Verbingung prüfen"));
$form->addElement('submit','db_admin_check',_("Verbingung prüfen"));
$form->addElement('submit','db_create',_("Datenbank anlegen"));
$form->addElement('submit','db_admin_create',_("Datenbank anlegen"));
$defaults = array(
    'base'     => $settings['html']['base'],
    'theme'     => $settings['theme']['base'],
    'skin'     => $settings['theme']['skin'],
    'db_host'     => $settings['database']['host'],
    'db_user'     => $settings['database']['user'],
    'db_name'     => $settings['database']['name'],
    'db_pass'     => $settings['database']['pass'],
    'db_admin_host'     => $hrsettings['database']['host'],
    'db_admin_user'     => $hrsettings['database']['user'],
    'db_admin_name'     => $hrsettings['database']['name'],
    'db_admin_pass'     => $hrsettings['database']['pass'],
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
$form->addRule('db_pass',       "Passwort darf nicht leer sein", 'required');
$form->addRule('db_admin_name', "Name darf nicht leer sein", 'required');
$form->addRule('db_admin_host', "Host darf nicht leer sein", 'required');
$form->addRule('db_admin_user', "User darf nicht leer sein", 'required');
$form->addRule('db_admin_pass', "Passwort darf nicht leer sein", 'required');

    
$form->addRule(array(
    'db_host', 
    'db_user',
    'db_pass'), 'Keine Verbindung zum Server!', 'db_connect');
if ($form->exportValue('save') || $form->exportValue('db_check')) {
    $form->addRule(array(
        'db_host', 
        'db_name',
        'db_user',
        'db_pass'), 'Datenbank nicht gefunden!', 'db_exists');
    $form->addRule(array(
        'db_host', 
        'db_name',
        'db_user',
        'db_pass'), 'Keine Vebindung zur Datenbank!', 'db_select');        
}
$form->addRule(array(
    'db_admin_host', 
    'db_admin_user',
    'db_admin_pass'), 'Keine Verbindung zum Server!', 'db_connect');
if ($form->exportValue('save') || $form->exportValue('db_admin_check')) {        
    $form->addRule(array(
        'db_admin_host', 
        'db_admin_name',
        'db_admin_user',
        'db_admin_pass'), 'Datenbank nicht gefunden!', 'db_exists');
    $form->addRule(array(
        'db_admin_host', 
        'db_admin_name',
        'db_admin_user',
        'db_admin_pass'), 'Keine Vebindung zur Datenbank!', 'db_select'); 
}
if ($form->validate()) {
    if ($form->exportValue('db_create')) {
        db_create(array(
            $form->exportValue('db_host'),
            $form->exportValue('db_name'),
            $form->exportValue('db_user'),
            $form->exportValue('db_pass')));
    } elseif ($form->exportValue('save')) {
        $settings['html']['base'] = $form->exportValue('base');
        $settings['theme']['base'] = $form->exportValue('theme');
        $settings['theme']['skin'] = $form->exportValue('skin');
        $settings['database']['host'] = $form->exportValue('db_host');
        $settings['database']['name'] = $form->exportValue('db_name');
        $settings['database']['user'] = $form->exportValue('db_user');
        $settings['database']['pass'] = $form->exportValue('db_pass');
        $hrsettings['database']['host'] = $form->exportValue('db_admin_host');
        $hrsettings['database']['name'] = $form->exportValue('db_admin_name');
        $hrsettings['database']['user'] = $form->exportValue('db_admin_user');
        $hrsettings['database']['pass'] = $form->exportValue('db_admin_pass');
        $settings['setup']['initialized'] = true;
        $conf = new Config;
        $root =& $conf->parseConfig($settings, 'phparray');
        //$res = $conf->writeConfig(dirname(__FILE__).'/../config/config.xml', 'XML');
        if (PEAR::isError($res)) {
            $tpl->setVariable('errors','<div class="error">'.$res->getMessage().'</div>');
        }
        $hrconf = new Config;
        $hrroot =& $hrconf->parseConfig($hrsettings, 'phparray');
        //$res = $hrconf->writeConfig(dirname(__FILE__).'/../config/hradmin.xml', 'XML');
        if (PEAR::isError($res)) {
            $tpl->setVariable('errors','<div class="error">'.$res->getMessage().'</div>');
        } else {
            setupHrAdmin($hrsettings);
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
    require_once 'hradmin.config.inc';
    require_once 'LiveUser/Admin/Perm/Container/DB_Medium.php';
    require_once 'LiveUser/Admin/Auth/Container/DB.php';
    require_once 'LiveUser/Perm/Container/DB/Medium.php';
    $lu_dsn = array('dsn' => $dsn);
    $adminAuth = new
        LiveUser_Admin_Auth_Container_DB(
            $lu_dsn, $conf['authContainers'][0]
        );
    $adminPerm = new
        LiveUser_Admin_Perm_Container_DB_Medium($conf['permContainer']);
    if (!$adminPerm->init_ok) {
        die('impossible to initialize: ' . $adminPerm->getMessage());
    }
    $adminPerm->setCurrentLanguage('DE');
    $appid = setupApplication($adminPerm);
    if (PEAR::isError($appid)) {
        die('impossible to initialize: ' . $appid->getMessage());
    }
    $settings['application'] = $appid;
    $adminPerm->setCurrentApplication($appid);
    $areaid = setupArea($adminPerm,$appid);
    if (PEAR::isError($areaid)) {
        die('impossible to initialize: ' . $areaid->getMessage());
    }
    $settings['area'] = $areaid;
    setupRights($adminPerm,$areaid);
}

function setupApplication($adminPerm) {
    $apps = $adminPerm->getApplications();
    foreach($apps as $app) {
        if ($app['define_name']=='HRJOBS') {
            return $app['application_id'];
        }
    }
    return $adminPerm->addApplication('HRJOBS',"HRJobs","Das Jobportal");
}
function setupArea($adminPerm,$appid) {
    $areas = $adminPerm->getAreas(array('where_application_id'=>$appid ));
    foreach($areas as $area) {
        if ($area['define_name']=='FRONTEND') {
            return $area['area_id'];
        }
    }
    return $adminPerm->addArea($appid,'FRONTEND',"Frontend","Frontend für das Jobportal");
}
function setupRights($adminPerm,$areaid) {
    $rights = $adminPerm->getRights(
        array(
            'where_area_id'        => $areaid
        )
    );
    foreach($rights as $right) {
        if ($right['define_name']=='LOGIN') {
            $login=true;
        }
        if ($right['define_name']=='SYSTEM') {
            $admin=true;
        }
    }
    if (!$login) {
        $res = $adminPerm->addRight($areaid,'LOGIN',"Login","Login Recht");
        if (PEAR::isError($res)) {
            print 'impossible to initialize: ' . $res->getMessage();
        }
    }
    if (!$admin) {
        $res = $adminPerm->addRight($areaid,'SYSTEM',"System","System Recht");
        if (PEAR::isError($res)) {
            print 'impossible to initialize: ' . $res->getMessage();
        }        
    }
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
        $buffer .= fgets($handle);        
    }
    fclose($handle);
    print $buffer;
    if (!mysql_query($buffer)) {
        die('Create tables failed: '. mysql_error());
    }
    mysql_close($link);
    return true;
}
function db_connect($params) {
    $host = $params[0];
    $user = $params[1];
    $pass = $params[2];
    $link = mysql_connect($host, $user, $pass);
    if (!$link) {
        return false;
    }
    mysql_close($link);
    return true;
}

function db_select($params) {
    $host = $params[0];
    $name = $params[1];
    $user = $params[2];
    $pass = $params[3];
    $link = mysql_connect($host, $user, $pass);
    if (!$link) {
        return false;
    }
    $db_selected = mysql_select_db($name, $link);
    if (!$db_selected) {
        mysql_close($link);
        return false;
    }
    mysql_close($link);
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