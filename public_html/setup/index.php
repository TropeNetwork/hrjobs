<?php
$ok = true;

// get include_path
$include_path = split(PATH_SEPARATOR,ini_get('include_path'));
if (!is_array($include_path)) {
    die('Can`t get "include_path"! Check your configuration!');
}
// get pear install dir
foreach ($include_path as $path) {
    if (file_exists($path.'/PEAR.php')) {
        $pear_path = $path;
    }
}
if (is_null($pear_path)) {
    die('Can`t get "PEAR_INSTALL_DIR" in your "include_path"! Check your configuration!');
}
// load pear registry
require_once 'PEAR/Registry.php';
$reg = new PEAR_Registry($pear_path);

$required_packages = array(
    'PEAR'                  => array('>=', '1.3.5'),
    'DB'                    => array('>=', '1.7.6'),
    'Config'                => array('>=', '1.10.3'),
    'HTML_QuickForm'        => array('>=', '3.2.4'),
    'HTML_Template_Sigma'   => array('>=', '1.1.2'),
    'Pager'                 => array('>=', '2.2.7'),
    'LiveUser'              => array('=', '0.16.0'),
    'LiveUser_Admin'        => array('=', '0.3.0'),
    'Var_Dump'              => array('>=', '1.0.2'),
    'Log'                   => array('>=', '1.8.7'),
    'XML_Tree'              => array('>=', '2.0.0'),  
    'XML_Util'              => array('>=', '1.1.1'),
);
$required_extentions = array(
    'dom'       => '',
    'xml'       => '',
    'gettext'   => '',
);

echo '<html><head><title>Requirements</title><link rel="stylesheet" href="../skins/default/style.css" /></head><body>';
echo '<table><tr><td><br>';
echo '<table class="panel"><tr><td>';

echo '<h1>PHP Requirements</h1>';
$version_ok = phpversion()>= '5.0.0';
$ok = $version_ok;
echo '<h2>PHP version is '.phpversion().' (required is 5.0.0)</h2>';
echo '<table><tr><td><div class="'.($version_ok?'ok':'error').'">'.($version_ok?' OK':' Failed').'</div></td></tr></table>';
echo '<table><tr><th>Extention</th><th>Result</th></tr>';
foreach ($required_extentions as $extention => $version) {
    echo '<tr><td>'.$extention.'</td>';
    if (!extension_loaded($extention)) {
        echo '<td><div class="error">Failed</div></td>';
        $ok = false;
        continue;        
    }
    echo '<td><div class="ok">OK</div></td>';
}
echo '</table><br>';

echo '<h1>PEAR Requirements</h1>';
echo '<p>PEAR_INSTALL_DIR='.$pear_path.'</p>';
echo '<table><tr><th>Package</th><th>Required version</th><th>Installed version</th><th>Result</th></tr>';
foreach ($required_packages as $package => $requirement) {
    $op = $requirement[0];
    $version = $requirement[1];
    echo '<tr><td>'.$package.'</td><td>'.$op.$version.'</td><td>';
    if (!$reg->packageExists($package)) {
        echo 'Not installed!</td><td><div class="error">Failed</div></td>';
        $ok = false;
        continue;
    }
    $info = $reg->packageInfo($package);
    
    switch ($op) {
		case '=':
			if ($info['version']!=$version) {
                echo $info['version'].'</td><td><div class="error">Failed</div></td>';
                $ok = false;
                continue;
            } else {
                echo $info['version'].'</td><td><div class="ok">OK</div></td>';
            }
            break;
    	case '>=':
            if ($info['version']<$version) {
                echo $info['version'].'</td><td><div class="error">Failed</div></td>';
                $ok = false;
            } else {
                echo $info['version'].'</td><td><div class="ok">OK</div></td>';
            }
            break;
		default:
			break;
	}
        
}
echo '</table><br>';
echo '<form action="setup.php" method="get">';
echo '<input type="submit" value="Continue" '.($ok?'':'disabled="1" ').'/>';
echo '</form>';
echo '</td></tr></table>';
echo '</td></tr></table>';
echo '</body>';
?>
