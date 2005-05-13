<?php 

include_once 'skin.inc';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'Database.php';
require_once 'HiringOrg.php';
require_once 'JobPosition.php';
require_once 'HttpParameter.php';
require_once 'OrgUser.php';

class JobColumnRenderer implements DBTableList_Renderer_Sigma_ColumnRenderer {
    public function renderColumn($name,$column) {
        if ($name=='organization_org_id') {
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
        $tpl->setVariable('org_id',$row['organization_org_id']);
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
$org_usr = new OrgUser($usr->getProperty('authUserId'));

$list = new DBTableList(DSN, 10,'job');
$list->setTable('job_posting, organization org');
$list->setColumns(array (
    'job_id'                => _("Job Id."),
    'organization_org_id'   => _("Organization"),
    'job_title'             => _("Titel"),
    'start_date'            => _("Start"),
    'end_date'              => _("End"),
    'job_status'            => _("Status"),
    'is_template'           => _("Template")
));

$list->orderby('job_id');
if (!checkRights(HRJOBS_RIGHT_SYSTEM)) {
    $where = "job_posting.organization_org_id=org.org_id AND org.organization_group_id=".$org_usr->getGroupId()." AND ";
} else {
    $where = "job_posting.organization_org_id=org.org_id AND ";
}
if ($templates) {
    $list->where($where."job_status!='".JobPositionPosting::STATUS_DELETED."' and is_template=1");
} else {
    $list->where($where."job_status!='".JobPositionPosting::STATUS_DELETED."' and is_template=0");
}

$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'jobs.html',
    'contentmain',
    'job',
    new JobColumnRenderer(),
    new JobRowRenderer()
);

$list->accept($listrenderer);
if ($templates) {
    $tpl->setVariable('title', _("Template"));
} else {
    $tpl->setVariable('title', _("Job Posting"));
}

$tpl->show();

?>