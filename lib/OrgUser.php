<?php
require_once 'HiringOrg.php';
class OrgUser {
    private $orgs = null;
    private $values = array();
    private $group_id = -1;
    public function __construct($id) 
    {
        if (isset($id)) {
            $this->setValue('organization_user_id',$id);
            $this->load($id);
        }
    }

    public function getOrganizations() {
        if ($this->orgs==null) {
            $db = Database::getConnection(DSN);
            if (!$this->getValue('organization_user_id')) {
                $this->setValue('organization_user_id',0);
            }
            $query="SELECT org.org_id " .
            		" FROM organization org, " .
                    "      organization_group og, " .
                    "      organization_user ou " .
                    "WHERE ou.organization_group_id=og.group_id " .
                    "  AND og.group_id=org.organization_group_id " .
                    "  AND ou.organization_user_id=".$this->getValue('organization_user_id');
            $data = $db->getCol($query);
            foreach($data as $id) {
                $this->orgs[$id] = new HiringOrg($id);
            }
        }
        return $this->orgs;
    }
    
    public function setValue($name, $value) {
        $this->values[$name] = $value;
    }
    public function getValue($name) {
        return $this->values[$name];
    }
    
    public function getGroupId() {
        if ($this->group_id<=0) {
            $db = Database::getConnection(DSN);
            if (!$this->getValue('organization_user_id')) {
                $this->setValue('organization_user_id',0);
            }
            $query="SELECT organization_group_id " .
                    " FROM organization_user " .
                    "WHERE organization_user_id=".$this->getValue('organization_user_id');
            $this->group_id = $db->getOne($query);
        }
        return $this->group_id;
    }
    
    public function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT * FROM organization_user WHERE organization_user_id=".$id;
        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }
    
    public function save() {
        $db = Database::getConnection(DSN);
        if (!isset($this->values['organization_user_id'])){
            return "failed";
        } else {
            foreach($this->values as $key=>$val){
                $query="UPDATE organization_user SET $key='".addslashes($val)."' 
                             WHERE organization_user_id=".$this->values['organization_user_id'];
                $res = $db->query($query);                
            }       
            if (DB::isError($res)) {
                return "failed";
            }
        }
        return "updated";
    }
    
    public function hasRightOnOrganization($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT org_id FROM organization WHERE organization_group_id=".$this->getGroupId()." AND org_id=".$id;
        $res = $db->getOne($query);
        if ($res) {
            return true;
        }
        return false;
    }
    
    public function hasRightOnJob($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT org.org_id FROM organization org, job_posting job WHERE job.organization_org_id=org.org_id AND org.organization_group_id=".$this->getGroupId()." AND job.job_id=".$id;
        $res = $db->getOne($query);
        if ($res) {
            return true;
        }
        return false;
    }
    
    public function hasRightOnContact($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT org.org_id FROM organization org, contact con WHERE con.organization_org_id=org.org_id AND org.organization_group_id=".$this->getGroupId()." AND con.contact_id=".$id;
        $res = $db->getOne($query);
        if ($res) {
            return true;
        }
        return false;
    }
    
    public function hasRightOnUser($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT organization_user_id FROM organization_user WHERE organization_group_id=".$this->getGroupId()." AND organization_user_id=".$id;
        $res = $db->getOne($query);
        if ($res) {
            return true;
        }
        return false;
    }
}

?>