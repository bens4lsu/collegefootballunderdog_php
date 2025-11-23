<?php
    
    class Pool {
        public $id;
        public $subtypeId;
		public $subtypeDescription;
        public $endDate;
        public $createDate;
        public $name;
        public $accessPassword;
        public $arrConfigOptions;
		private $db;
        public $isOver;  //bool
        
		private $arrMembers;  // not initialized -- too much overhead unless we're going to use it.  function GetMembers();
        
        function __construct($poolId, $db){
            $row =$db->cbSqlQuery ("SELECT PoolName, PoolAccessPassword, p.idPoolSubtypes, PoolSubtypeDescription, PoolEndDate, PoolCreateDate, CASE WHEN DATE(NOW()) > PoolEndDate THEN 1 ELSE 0 END AS PoolIsOver FROM Pools p join PoolSubtypes ps on ps.idPoolSubtypes = p.idPoolSubtypes WHERE p.idPools = $poolId");
            $this->id = $poolId;
            $this->subtypeId = $row['idPoolSubtypes'];
            $this->subtypeDescription = $row['PoolSubtypeDescription'];
            $this->endDate = $row['PoolEndDate'];
            $this->createDate = $row['PoolCreateDate'];
			$this->db = $db;
            $this->name=$row['PoolName'];
            $this->isOver = $row['PoolIsOver'] == 1;
            
            // config options
			$this->arrConfigOptions = array();
            $r =$db->cbSqlQuery ('select idPoolConfiguration, ConfigValue from PoolConfiguration where idPools = '.$poolId, true);
            $configs = $db->getPoolConfigurationValues($poolId);
            foreach ($configs as $config){
                $this->arrConfigOptions[$config['idPoolConfiguration']] = $config['ConfigValue'];
            }
        }
        
        function PrintMessages($parent = 0, $lev=0)
        
        {
			$RMess = $this->db->cbSqlQuery ('select idPoolMessages, Message, pu.Name, EntryTime from PoolMessages pm join PoolUsers pu on pm.EnteredByID = pu.idPoolUsers where pm.idPools = '.$this->id.' and pm.ParentIdPoolMessages ='. $parent.' order by EntryTime desc', true);
            
            foreach ($RMess as $mess){
				$indent = $lev*45;
                $messageText = str_replace("\n", "<br>", $mess['Message']);
                print '<div class="messlevel" style="margin-left:'.$indent.'px;a">';
                print '<p class="messagemeta"><span class="messname">'.$mess['Name'].'</span> - <span class="messtime">'.$mess['EntryTime'].'</span></p>';
                print '<p class="message">'.$messageText.'</p>';
				//print '<p>id='.$mess['idPoolMessages'].' parent='.$parent.' lev='.$lev.'</p>';
                // replies to this one
                print '<p class="messreply"><a href="PAddMessage.php?pool='.$this->id.'&parent='.$mess['idPoolMessages'].'"><img src="./images/reply_icon.gif"a lt="">Reply to this</a></p>';
                $this->PrintMessages($mess['idPoolMessages'], $lev+1);
				print '</div>';
            }
        }
        
        function GetMembers()
		{
			$this->arrMembers = array();
			$R = $this->db->cbSqlQuery ('select idPoolUsers from PoolUserEntries where idPools = '.$this->id, true);
			foreach ($R as $u){
				$U = new Person($u['idPoolUsers'], $this->db);
				$this->arrMembers[$u['idPoolUsers']] = $U;
			}
		}
		
		function EmailAllMembers($subject, $message)
		{
			if (! isset($this->arrMembers)){
				$this->GetMembers();
			}
            $sys = new Sys();
			foreach ($this->arrMembers as $user){
				$to = $user->email;
				//print $to.'<br>';
				$headers = "From: gottakeepitautomated@collegefootballunderdog.com\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				$sys->sendAnEmail($to, $subject, $message, $headers);
			}
		}
		       
        function GetMessageInfo($messageId)
        {
            return $this->db->cbSqlQuery('select idPoolMessages, Message, pu.Name, EntryTime from PoolMessages pm join PoolUsers pu on pm.EnteredByID = pu.idPoolUsers where pm.idPools = '.$this->id.' and pm.idPoolMessages ='.$messageId);
        }
    }
    
    
    ?>