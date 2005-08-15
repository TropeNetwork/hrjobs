<?php 

include_once 'skin.inc';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'Database.php';
require_once 'HiringOrg.php';
require_once 'JobPosition.php';
require_once 'HttpParameter.php';
require_once 'OrgUser.php';

class JobColumnRenderer implements DBTableList_Renderer_Sigma_ColumnRenderer {
    public function renderColumn($name,$column) {
        if ($name=='org_id') {
            $org = new HiringOrg($column); 
            return $org->getValue('org_name');
        }
        if ($name=='start_date') {
            return strftime('%d.%m.%Y',strtotime($column));
        }
        if ($name=='end_date') {
            if ($column=='0000-00-00') {
                return '';
            }
            return strftime('%d.%m.%Y',strtotime($column));
        }
        if ($name=='job_status') {
            if ($column==JobPositionPosting::STATUS_ACTIVE) {
                return '<img src="'.IMAGES_DIR.'/active.png" alt="'._("Enabled").'" />';
            }
            if ($column==JobPositionPosting::STATUS_INACTIVE) {
                return '<img src="'.IMAGES_DIR.'/inactive.png" alt="'._("Disabled").'" />';
            }
        }
        return $column;
    }
}
class JobRowRenderer implements DBTableList_Renderer_Sigma_RowRenderer {
    public function renderRow(& $tpl,$row) {
        $tpl->setVariable('org_id',$row['org_id']);
        if ($row['is_template']==1) {
            $tpl->setVariable('status','<img title="'._("Template").'" src="'.IMAGES_DIR.'/pending.png" alt="'._("Template").'" />');
        } else if ($row['job_status']==='inactive') {
            $tpl->setVariable('status','<img title="'._("Disabled").'" src="'.IMAGES_DIR.'/inactive.png" alt="'._("Disabled").'" />');
        } else if (strtotime($row['start_date'])>time()) {
            $tpl->setVariable('status','<img title="'._("Pending").'" src="'.IMAGES_DIR.'/pending.png" alt="'._("Pending").'" />');
        } else if ($row['end_date']!=='0000-00-00' && strtotime($row['end_date'])<time()) {
            $tpl->setVariable('status','<img title="'._("Timeout").'" src="'.IMAGES_DIR.'/timeout.png" alt="'._("Timeout").'" />');
        } else if ($row['job_status']==='active') {
            $tpl->setVariable('status','<img title="'._("Enabled").'" src="'.IMAGES_DIR.'/publish.png" alt="'._("Enabled").'" />');
        }
    }
}
$templates = HttpParameter::getParameter('templates');
$org_usr = new OrgUser($usr->getProperty('auth_user_id'));

$form = new HTML_QuickForm('search','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> "._("Required")."</font>");
$form->addElement('text','search', _("Search"),
            array('maxlength'=>'100',
                              'size'=>'40',
                              'class'=>'formFieldLong'));
$form->addElement('submit','submit',_("Search"));
                              

$list = new DBTableList(DSN, 10,'job');
$list->setTable('job_posting, organization org');
$list->setColumns(array (
    'job_id'                => _("JobID"),
    'job_reference'         => _("Reference"),
    'org.org_id'   				=> _("Organization"),
    'job_title'             => _("Titel"),
    'start_date'            => _("Start"),
    'end_date'              => _("End"),
    'job_status'            => _("Status"),
    'is_template'           => _("Template")
));

$list->orderby('job_id');
if (!checkRights(HRJOBS_RIGHT_SYSTEM)) {
    $where = 'job_posting.org_id=org.org_id AND org.group_id='.$org_usr->getGroupId().' AND ';
} else {
    $where = 'job_posting.org_id=org.org_id AND ';
}

if ($form->validate()) {
    $where .= ' (job_title like \'%'.addslashes($form->exportValue('search')).'%\' 
                OR job_id=\''.addslashes($form->exportValue('search')).'\'
                OR job_reference LIKE \'%'.addslashes($form->exportValue('search')).'%\') 
                AND ';
}

if ($templates) {
    $list->where($where.'job_status!=\''.JobPositionPosting::STATUS_DELETED.'\' AND is_template=1');
} else {
    $list->where($where.'job_status!=\''.JobPositionPosting::STATUS_DELETED.'\' AND (is_template IS NULL OR is_template=0)');
}

$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('');

$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'jobs.html',
    'contentmain',
    'job',
    'job_entry',
    new JobColumnRenderer(),
    new JobRowRenderer()
);

$list->accept($listrenderer);
$form->accept($renderer);
if ($templates) {
    $tpl->setVariable('title', _("Template"));
} else {
    $tpl->setVariable('title', _("Job Posting"));
}
$tpl->setVariable('base',HTML_BASE);
$tpl->setVariable('link_new_job','job.php');
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
$tpl->show();

?>