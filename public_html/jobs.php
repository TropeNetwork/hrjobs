<?php 

include_once 'skin.inc';
require_once 'class/DBTableList.php';
require_once 'class/DBTableList/Renderer/Sigma.php';
require_once 'class/Database.php';
require_once 'class/HiringOrg.php';
require_once 'class/JobPosition.php';
require_once 'class/HttpParameter.php';
require_once 'class/OrgUser.php';

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
                return '<img src="'.IMAGES_DIR.'/active.png" alt="Aktiv" />';
            }
            if ($column==JobPositionPosting::STATUS_INACTIVE) {
                return '<img src="'.IMAGES_DIR.'/inactive.png" alt="Inaktiv" />';
            }
        }
        return $column;
    }
}
class JobRowRenderer implements DBTableList_Renderer_Sigma_RowRenderer {
    public function renderRow(& $tpl,$row) {
        $tpl->setVariable('org_id',$row['organization_org_id']);
        if ($row['is_template']==1) {
            $tpl->setVariable('status','<img title="Vorlage" src="'.IMAGES_DIR.'/pending.png" alt="vorlage" />');
        } else if ($row['job_status']==='inactive') {
            $tpl->setVariable('status','<img title="Inaktiv" src="'.IMAGES_DIR.'/inactive.png" alt="inaktiv" />');
        } else if (strtotime($row['start_date'])>time()) {
            $tpl->setVariable('status','<img title="Anstehend" src="'.IMAGES_DIR.'/pending.png" alt="anstehend" />');
        } else if ($row['end_date']!=='0000-00-00' && strtotime($row['end_date'])<time()) {
            $tpl->setVariable('status','<img title="Abgelaufen" src="'.IMAGES_DIR.'/timeout.png" alt="abgelaufen" />');
        } else if ($row['job_status']==='active') {
            $tpl->setVariable('status','<img title="Aktiv" src="'.IMAGES_DIR.'/publish.png" alt="aktiv" />');
        }
    }
}
$templates = HttpParameter::getParameter('templates');
$org_usr = new OrgUser($usr->getProperty('authUserId'));

$list = new DBTableList(DSN, 10,'job');
$list->setTable('job_posting, organization org');
$list->setColumns(array (
    'job_id'                => 'Job Id.',
    'organization_org_id'   => 'Organisation',
    'job_title'             => 'Titel',
    'start_date'            => 'Start',
    'end_date'              => 'Ende',
    'job_status'            => 'Status',
    'is_template'           => 'Vorlage'
));

$list->orderby('job_id');
if (!checkRights(HRADMIN_RIGHT_SYSTEM)) {
    $where = "organization_org_id=org.org_id AND org.organization_group_id=".$org_usr->getGroupId()." AND ";
} else {
    $where = '';
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
    $tpl->setVariable('title', "Vorlagen");
} else {
    $tpl->setVariable('title', "Stellenanzeigen");
}

$tpl->show();

?>