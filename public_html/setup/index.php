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
    'PEAR'                  => '1.3.5',
    'DB'                    => '1.7.6',
    'Config'                => '1.10.3',
    'HTML_QuickForm'        => '3.2.4',
    'HTML_Template_Sigma'   => '1.1.2',
    'Pager'                 => '2.2.7',
    'LiveUser'              => '0.15.1',
    'LiveUser_Admin'        => '0.2.1',
    'Var_Dump'              => '1.0.2',
    'Log'                   => '1.8.7',
    'XML_Tree'              => '2.0.0',  
    'XML_Util'              => '1.1.1',
);
$required_extentions = array(
    'dom'       => '',
    'xml'       => '',
    'gettext'   => '',
);

echo '<html><head><title>Requirements</title><link rel="stylesheet" href="../skins/default/style.css" /></head><body>';
echo '<table class="panel"><tr><td>';

echo '<h1>PHP Requirements</h1>';
$version_ok = phpversion()>= '5.0.0';
$ok = $version_ok;
echo '<h2>PHP version is <font color="'.($version_ok?'green':'red').'">'.phpversion().($version_ok?' OK':' Failed').'</font> (required is 5.0.0)</h2>';
echo '<table><tr><th>Extention</th><th>Result</th></tr>';
foreach ($required_extentions as $extention => $version) {
    echo '<tr><td>'.$extention.'</td>';
    if (!extension_loaded($extention)) {
        echo '<td><font color="red">Failed</font></td>';
        $ok = false;
        continue;        
    }
    echo '<td><font color="green">OK</font></td>';
}
echo '</table><br>';

echo '<h1>PEAR Requirements</h1>';
echo '<table><tr><th>Package</th><th>Required version</th><th>Installed version</th><th>Result</th></tr>';
foreach ($required_packages as $package => $version) {
    echo '<tr><td>'.$package.'</td><td>'.$version.'</td><td>';
    if (!$reg->packageExists($package)) {
        echo 'Not installed!</td><td><font color="red">Failed</font></td>';
        $ok = false;
        continue;
    }
    $info = $reg->packageInfo($package);
    if ($info['version']<$version) {
        echo $info['version'].'</td><td><font color="red">Failed</font></td>';
        $ok = false;
        continue;
    }
    echo $info['version'].'</td><td><font color="green">OK</font></td>';
}
echo '</table><br>';
echo '<form action="setup.php" method="get">';
echo '<input type="submit" value="Continue" '.($ok?'':'disabled="1" ').'/>';
echo '</form>';
echo '</td></tr></table>';
echo '</body>';
?>
