<?php
require_once 'configuration.inc';
require_once 'HTML/Template/Sigma.php';
require_once "class/Categories.php";
require_once 'class/HttpParameter.php';
$mode = HttpParameter::getParameter('mode');

$tpl =& new HTML_Template_Sigma(TEMPLATE_DIR);
$tpl->loadTemplateFile('browser.html');
$tpl->setVariable("base",HTML_BASE);
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
$script = '
<script type="text/javascript">
<!--
            function addElement(elName) {
                if (parent.typoWin && parent.typoWin.setFormValueFromBrowseWin)    {
                        parent.typoWin.setFormValueFromBrowseWin("'.$mode.'",document.formBrowser);
                        parent.typoWin.focus();
                        parent.browserWin="";
                        parent.close();
                } else {
                    alert("Error - refderence to main window is not set properly!");
                    parent.close();
                }
            }
-->
</script>
';      
include_once 'hradmin.inc';
if (!checkRights(HRADMIN_RIGHT_LOGIN)) {
    header("Location: noright.php");
}
$cat=Categories::getAllCategories(0,$mode);

$main = "<form name=\"formBrowser\" action=\"#\">\n<select name=\"cat\" multiple=\"multiple\">\n";
foreach($cat AS $key=>$val){
    $main .= "<option value=\"$key\">$val</option>\n";
}
$main .= "</select><br/><br/>\n<input type=\"submit\" value=\"Hinzufügen\" onclick=\"return addElement(formBrowser);\">\n</form>";

$tpl->touchBlock('main');
$tpl->setVariable('title', _("Browser"));
$tpl->setVariable('script', $script);
$tpl->setVariable('contentmain', $main);
$tpl->show();
?>