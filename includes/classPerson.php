<?php
    
    include_once ('fnGetCurrentIpAddress.php');
    include_once ('classPool.php');
//    include_once ('fnSPrint_R.php');
    
    class Person
    {
        private $db;
        public $personId;
        public $name;
        public $email;
        public $isAdmin;
        public $arr_Pools;  // array (idPools=>idPoolUserEntries)
        public $lastTimeVerified;
        public $lastIPVerified;
        public $lastPoolIDUsed;
        public $timezone;
        public $hasCurrentPool;
        
        function __construct($personId, $db){
            $this->db = $db;
            
            $personInfo = $this->db->cbSqlQuery('select Name, EmailAddress, IsAdmin, DefaultPoolID, TimezonePHPName from PoolUsers u join LookupTimezones tz on u.idTimezone = tz.idTimezone where idPoolUsers = '.$personId);
            $this->personId = $personId;
            $this->name = $personInfo['Name'];
            
            
            $this->email = $personInfo['EmailAddress'];
            //$this->email = 'bens@theskinnyonbenny.com';
			$this->isAdmin = ($personInfo['IsAdmin'] == 1);
            
            // fill array of pools where this user is a member
            $this->arr_Pools = array();
            $poolInfo = $this->db->getUsersPoolList($personId);
            $this->hasCurrentPool = false;
            foreach ($poolInfo as $pool){
                $this->arr_Pools[$pool['idPools']] = $pool['idPoolUserEntries'];
                $this->hasCurrentPool = $pool['IsCurrent'] == 1 ? true : $this->hasCurrentPool;
            }
            
            if (isset($personInfo['DefaultPoolID']) && $personInfo['DefaultPoolID'] > 0){
                $this->lastPoolIDUsed = $personInfo['DefaultPoolID'];
            }
            else {
            	$this->changeLastPoolID(key($this->arr_Pools));
            }
            
            $this->timezone = $personInfo['TimezonePHPName'];
            $this->lastTimeVerified = time();
            $this->lastIPVerified = cbGetCurrentIpAddress();
            print ($this->arr_Pools);
        }
        
        function printPoolSelectionForm()
        {
            $out = '<form name="frmSelectPool" method="post" action="PSetPool.php"><select onchange="document.forms.frmSelectPool.submit();" name="poolId">';
            foreach ($this->arr_Pools as $idPools=>$idPoolUserEntries){
                $thisPool = new Pool($idPools, $this->db);
                $selectedText = ($this->lastPoolIDUsed == $idPools) ? 'selected' : '';
                //$out .= '<pre>'.sprint_r($thisPool).'</pre>';   // uses function included line 5.  might need to uncomment up there too.
                $class = $thisPool->isOver ? 'poolSelectOption_IsOver' : 'poolSelectOption_IsCurrent';
                $out .= "<option value=\"$idPools\" class=\"$class\" $selectedText>$thisPool->name</option>";
            }
            $out .= '</select></form>';
            //print '<pre>';
            //print_r($this);
            //print '</pre>';
            return $out;
        }
        
        function changeLastPoolID($poolId)
        {
            $this->lastPoolIDUsed = $poolId;
            $this->db->cbSqlQuery("update PoolUsers set DefaultPoolID = $poolId where idPoolUsers = $this->personId");
        }
        
        function unsetDb(){
            $this->db = null;
        }
        
        function setDb($db){
            $this->db = $db;
        }
    }
?>