<?php

include_once ('config.php');


class Db extends PDO 
{
	private $dsn = DB_DSN;
	private $user = DB_USER;
	private $password = DB_PASSWORD;
	private $stmt;
	public $error;
	public $numRowsAffected; 
	public $insertIdentity;  
	
	public function __construct(){
		$options = array (/*PDO::ATTR_PERSISTENT => true,*/ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
		$i = 0;
		ini_set("default_socket_timeout", 10);
		while ($i < 150){    //  PDO very occasionally gets an error "Mysql server disappeared"
		                   //  An attempt or two to retry the connection usually resolves the issue.
            try {
                parent::__construct($this->dsn, $this->user, $this->password, $options);
                $this -> exec("SET CHARACTER SET utf8");
                $this->setAttribute( PDO::ATTR_PERSISTENT, true );
                break;
            }
            catch (PDOException $e){
                $this->error = $e->getMessage();
                print $this->error;
                $i++;
            }
        }
	}
	
	
	private function substitueArrayValues($statement, array $arrays) {
	//  allows an array to be bound to arrays to use with "in" clause of query.
	//
	//  use:
	//		$query = '
    //			SELECT     *
    //			FROM       test
    //			WHERE      field1 IN :array1
    //			OR        field2 IN :array2
    //			OR        field3 = :value
    //		';

    //		$pdo_query = $db_link->prepare_with_arrays(
    //			$query,
    //			array(
    //				':array1' => array (values => array('A','B','C'), type=>'quoted'
    //				':array2' => array (values => array(7,8,9), type=>'unquoted'
    //			)
    //		);
    //		$pdo_query->bindValue(':value', '10');
	//		$pdo_query->execute();

        foreach($arrays as $token => $data) {
        	$replaceText = '(';
            foreach($data['values'] as $value) {
                if ($data['type'] == 'quoted'){
                	$replaceText .= "'$value', ";
                }
                else {
                	$replaceText .= "$value, ";
                }
            }
            $replaceText = substr($replaceText, 0, strlen($replaceText) - 2);
            $replaceText .= ')';
            $statement = str_replace ($token, $replaceText, $statement);
        }
        return $statement;
    }
    
    
	/*  Private format functions  */
	
	private function prepDateForInsert($dateIn, $timeIn = '00:00', $returnDateOnly = false){
	    if ($dateIn === null || $dateIn == ''){
	        return null;
	    }
	    if ($returnDateOnly){
            return date ("Y-m-d", strtotime($dateIn.' '.$timeIn));
	    }
		return date ("Y-m-d H:i", strtotime($dateIn.' '.$timeIn));
	}
	
	private function prepDateForInsert_vi($inputString){
	    // input looks like mm/dd  h:mm  PM
	    $currentYear = date("Y");
	    $currentMonth = date("m");
	    $month = substr($inputString, 0, 2);
	    $day = substr($inputString, 3, 2);
	    $year = $month >= 8 || ($month == 1 && $currentMonth == 1) ? $currentYear : $currentYear + 1;
	    $hour = substr($inputString, 7, strpos($inputString, ':') - 7);
	    $minute = substr($inputString, strpos($inputString, ':') + 1, 2);
	    $ampm = substr($inputString, strpos($inputString, ':') + 4, 2);
	    $datestring = "$year-$month-$day $hour:$minute$ampm";
	    return date("Y-m-d H:i", strtotime($datestring));
	}
	
	private function stripMaskCharacters($strIn){
            $strOut = str_replace (' ', '', $strIn);
            $strOut = str_replace ('-', '', $strOut);
            $strOut = str_replace ('(', '', $strOut);
            $strOut = str_replace (')', '', $strOut);
            return $strOut;
	}
	
	/*  Execute functions  */
	
	private function selectRowAssoc(){
            try {
                $this->stmt->execute();
                $this->numRowsAffected = $this->stmt->rowCount();
                $result = $this->stmt->fetch(PDO::FETCH_ASSOC);
                $this->stmt = null;
                if ($result === true){
                    return true;
                }
                return $result;
            }
            catch (PDOException $e){
                $this->error = $e->getMessage();
                $this->stmt = null;
                return false;			
            }
	}
	
	private function selectRowsArrayAssoc(){
            try {
                $this->stmt->execute();
                $this->numRowsAffected = $this->stmt->rowCount();
                $result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->stmt = null;
                if ($result === true){
                    return true;
                }
                return $result;			
            }
            catch (PDOException $e){
                $this->error = $e->getMessage();
                $this->stmt = null;
                return false;
            }
	}
	
	private function selectRowsIntoJsonArray(){
            if ($result = $this->selectRowsArrayNum()){
                $this->stmt = null;
                if ($result === true){
                    return true;
                }
                $indexedRows = array();
                foreach ($result as $row){
                        $indexedRows[] = $row;
                }
                $keyedData = array ("data" => $indexedRows);
                //$jsonData = json_encode($keyedData, JSON_UNESCAPED_UNICODE);
                $jsonData = json_encode($keyedData);
                str_replace('/r', '<br>', $jsonData);
                return $jsonData;
            }
            else {
                    $this->stmt = null;
                    return false;
            }
	}
	
	private function selectFirstRowIntoJsonObject(){
            if ($result = $this->selectRowsArrayAssoc()){
                $this->stmt = null;
                    $jsonData = json_encode($result[0]);
                    str_replace('/r', '<br>', $jsonData);
                    return $jsonData;
            }
            else {
                $this->stmt = null;
                    return false;
            }
	}
	
	private function selectRowsArrayNum(){
            try {
                $this->stmt->execute();
                $this->numRowsAffected = $this->stmt->rowCount();
                $result = $this->stmt->fetchAll(PDO::FETCH_NUM);
                if ($result === true || is_array($result) && empty($result)){
                    return true;
                }
                return $result;
            }
            catch (PDOException $e){
                //print_r($e);
                $this->error = $e->getMessage();
                $this->stmt = null;
                return false;
            }
	}
	
	private function modifyData($withInsertId = true){
            try {
                    $this->stmt->execute();
                    $this->numRowsAffected = $this->stmt->rowCount();
                    $this->insertIdentity = $this->lastInsertId();
                    return true;
            }
            catch (PDOException $e){
                    $this->error = $e->getMessage();
                    return false;
            }
	}
	
	
	/*  error/troubleshooting  */
	
	public function getStatement(){
	    return $this->stmt->queryString;
	}
	
	
	/* backward compatibility  */
	
	function cbSqlQuery ($sql, $bReturnEntireResource = false) {
        $this->error = null;
        $this->stmt = $this->prepare($sql);
        if ($bReturnEntireResource) {
            return $this->selectRowsArrayAssoc($sql);
        }
        else {
            return $this->selectRowAssoc($sql);
        }
    }
	
	/*  Public functions - selects */
	
    public function findGame($gameDate, $homeTeamName, $awayTeamName) {
        $sql = 'SELECT g.idDGames, w.idDWeeks
                FROM DGames2 g 
                    JOIN DFootballTeams aw ON g.idDFootballTeamsAway = aw.idDFootballTeams 
                    JOIN DFootballTeams hm ON g.idDFootballTeamsHome = hm.idDFootballTeams 
                    JOIN DWeeks w ON g.idDWeeks = w.idDWeeks
                WHERE aw.TeamName = :awayTeamName AND hm.TeamName = :homeTeamName
                    AND w.WeekDateStart <= DATE(:gameDate1)  AND w.WeekDateEnd >= DATE(:gameDate2)'; 
        $this->stmt = $this->prepare($sql);
        $insertDate = $gameDate;
        $this->stmt->bindParam(':gameDate1', $insertDate, PDO::PARAM_STR);
	    $this->stmt->bindParam(':gameDate2', $insertDate, PDO::PARAM_STR);
	    $this->stmt->bindParam(':awayTeamName', $awayTeamName, PDO::PARAM_STR);
	    $this->stmt->bindParam(':homeTeamName', $homeTeamName, PDO::PARAM_STR);   
        return $this->selectRowAssoc();
    }
    
    public function createGame($gameDate, $homeTeamName, $awayTeamName, $isNeutralSite, $spread, $underdog) {  
        $sql = 'INSERT DGames2 (idDWeeks, idDFootballTeamsAway, idDFootballTeamsHome, IsNeutralSite, Kickoff)
                SELECT w.idDWeeks, aw.idDFootballTeams, hm.idDFootballTeams, :isNeutralSite , :gameDate3 - INTERVAL 1 HOUR
                FROM DFootballTeams aw
                    JOIN DFootballTeams hm ON aw.TeamName = :awayTeamName AND hm.TeamName = :homeTeamName 
                    JOIN DWeeks w ON w.WeekDateStart <= DATE(:gameDate1)  AND w.WeekDateEnd >= DATE(:gameDate2)';    
                    
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':gameDate1', $gameDate, PDO::PARAM_STR);
        $this->stmt->bindParam(':gameDate2', $gameDate, PDO::PARAM_STR);
        $this->stmt->bindParam(':gameDate3', $gameDate, PDO::PARAM_STR);
        $this->stmt->bindParam(':isNeutralSite', $isNeutralSite, PDO::PARAM_INT);
        $this->stmt->bindParam(':awayTeamName', $awayTeamName, PDO::PARAM_STR);
        $this->stmt->bindParam(':homeTeamName',$homeTeamName, PDO::PARAM_STR);
        $result1 = $this->modifyData();
        if (! $result1) {
                return $result1;
        }
        
        $gameId = $this->insertIdentity;
                
        $sql = 'INSERT INTO DGamesSpreadHistoric
                    (`idDGames`, `OpenSpreadTeam`, `OpenSpread`)
                    VALUES (:gameId, :underdog, :spread);';
            $this->stmt = $this->prepare($sql);
            $this->stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        $this->stmt->bindParam(':underdog', $underdog, PDO::PARAM_STR);
        $this->stmt->bindParam(':spread',$spread, PDO::PARAM_STR);  
        $result2 = $this->modifyData(); 
        
        // have to let the system know the gameId.
        $this->insertIdentity = $gameId;
        return $result2;
    }
    
    public function getUsersPoolList($personId){
        $sql = 'SELECT pue.idPoolUserEntries, pue.idPools 
                    , CASE WHEN w.DateEnd > NOW() THEN 1 ELSE 0 END AS IsCurrent
                FROM PoolUserEntries pue 
                    JOIN Pools p ON pue.idPools = p.idPools 
                    JOIN (SELECT idPoolSubtypes, MAX(WeekDateEnd) AS DateEnd FROM DWeeks GROUP BY idPoolSubtypes) w ON p.idPoolSubtypes = w.idPoolSubtypes
                WHERE pue.idPoolUsers = :personId ORDER BY p.IsComplete, p.PoolName';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':personId', $personId, PDO::PARAM_INT);
        return $this->selectRowsArrayAssoc();
    }
    
    public function getDatesForWeek($weekId){
        $sql = 'SELECT WeekDateStart, WeekDateEnd FROM DWeeks WHERE idDWeeks = :weekId';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':weekId', $weekId, PDO::PARAM_INT);
        return $this->selectRowAssoc();
    }
    
    public function getPoolConfigurationValues ($poolId) {
        $sql = 'SELECT idPoolConfiguration, ConfigValue from PoolConfiguration where idPools = :poolId';
        $this->stmt = $this->prepare($sql);
	$this->stmt->bindParam(':poolId', $poolId, PDO::PARAM_INT);
	return $this->selectRowsArrayAssoc();
    }
    
    public function getGameInfo($gameId){
        $sql = 'SELECT g.idDGames, g.idDWeeks, g.idDFootballTeamsAway, g.idDFootballTeamsHome, g.isNeutralSite, g.Kickoff, 
                    g.ScoreHome, g.ScoreAway, ta.TeamName AS AwayTeamName, th.TeamName AS HomeTeamName,
                    ta.TeamUrl AS AwayTeamUrl, th.TeamURL AS HomeTeamUrl
                FROM DGames2 g
                    JOIN DFootballTeams ta ON g.idDFootballTeamsAway = ta.idDFootballTeams
                    JOIN DFootballTeams th ON g.idDFootballTeamsHome = th.idDFootballTeams
                WHERE idDGames = :gameId';
        $this->stmt = $this->prepare($sql);
	$this->stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
	return $this->selectRowAssoc();
    }
    
    public function savePick($gameId, $spread, $user, $team){
        $sql = 'INSERT DPicks2 (idPoolUserEntries, idDGames, TeamPicked, Spread)
                VALUES (:user, :gameId, :team, :spread)';
        $this->stmt = $this->prepare($sql);
	$this->stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
	$this->stmt->bindParam(':user', $user, PDO::PARAM_INT);
	$this->stmt->bindParam(':team', $team, PDO::PARAM_STR);
	$this->stmt->bindParam(':spread', $spread, PDO::PARAM_STR);
	return $this->modifyData();         
    }
    
    public function testUserAlreadyPicked($user, $week){
        $sql = 'SELECT COUNT(1) AS Picked 
                FROM DPicks2 p
                    JOIN DGames2 g ON p.idDGames = g.idDGames
                WHERE p.idPoolUserEntries = :user AND g.idDWeeks = :week';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':week', $week, PDO::PARAM_INT);
	$this->stmt->bindParam(':user', $user, PDO::PARAM_INT);
	return $this->selectRowAssoc();
    }
    
    public function getGamesWithPicks($weekId) {
        $sql = 'SELECT DISTINCT g.idDGames
                FROM DGames2 g
                    JOIN DPicks2 p ON g.idDGames = p.idDGames
                WHERE idDWeeks = :weekId';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':weekId', $weekId, PDO::PARAM_INT);
	return $this->selectRowsArrayAssoc();
    }

    public function getGamesForWeek($weekID) {
        $sql = 'SELECT DISTINCT g.idDGames
                FROM DGames2 g
                WHERE idDWeeks = :weekId';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':weekId', $weekId, PDO::PARAM_INT);
        return $this->selectRowsArrayAssoc();
    }

    
    public function getGameQuality($gameId, $poolId) {
        $sql = 'SELECT SUM(FBSFlag) AS Q, 
	                CASE WHEN SUM(FBSFlag) >= cfg.ConfigValue THEN 1 ELSE 0 END AS ShowGameFlag
                FROM DFootballTeams t
                    JOIN DGames2 g ON (t.idDFootballTeams = g.idDFootballTeamsAway OR t.idDFootballTeams = g.idDFootballTeamsHome)
                    CROSS JOIN PoolConfiguration cfg ON cfg.idPoolConfiguration = \'MINGAMEQUALITY\' AND cfg.idPools = :poolId
                WHERE g.idDGames = :gameId;';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        $this->stmt->bindParam(':poolId', $poolId, PDO::PARAM_INT);
	return $this->selectRowAssoc();
    }
    
    public function getStandingsTable($poolId) {
        $sql = 'SELECT idPoolUserEntries, `Name`, SUM(PointsEarned) AS Score, SUM(IsBonusPick) AS BonusesUsed, SUM(Winners) AS Winners
                FROM vwAllPicks WHERE idPools = :poolId
                GROUP BY idPoolUserEntries,`Name`
                ORDER BY SUM(PointsEarned) DESC';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':poolId', $poolId, PDO::PARAM_INT);
	return $this->selectRowsArrayAssoc();
    }
    
    public function saveMessage ($pool, $parent, $message, $enteredBy){
        $sql = 'insert PoolMessages (idPools, ParentIdPoolMessages, Message, EnteredById) values (:pool, :parent, :message, :enteredBy)';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':pool', $pool, PDO::PARAM_INT);
        $this->stmt->bindParam(':parent', $parent, PDO::PARAM_INT);
        $this->stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $this->stmt->bindParam(':enteredBy', $enteredBy, PDO::PARAM_INT);
        return $this->modifyData(); 
    }
    
    public function getAllPicksForUser($stopWeek, $poolId, $poolUserId) {
    
    	// print($stopWeek).'<br>';
//     	print ($poolId).'<br>';
//     	print ($poolUserId).'<br>';
    
        $sql = 'select w.* from DWeeks w join Pools p on w.idPoolSubtypes = p.idPoolSubtypes join PoolUserEntries pue on p.idPools = pue.idPools 
                where w.idDWeeks < :stopWeek and p.idPools = :poolId and pue.idPoolUserEntries = :pueid
                order by w.idDWeeks';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':stopWeek', $stopWeek, PDO::PARAM_INT);
        $this->stmt->bindParam(':poolId', $poolId, PDO::PARAM_INT);
        $this->stmt->bindParam(':pueid', $poolUserId, PDO::PARAM_INT);
        return $this->selectRowsArrayAssoc();
    }
    
    public function addWatchlist($poolUserId, $gameId) {
        $sql = 'INSERT DWatchlist (idPoolUserEntries, idDGames) VALUES (:poolUserId, :gameId)';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':poolUserId', $poolUserId, PDO::PARAM_INT);
        $this->stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        return $this->modifyData(); 
    }
    
    public function deleteWatchlist($poolUserId, $gameId) {
        $sql = 'DELETE FROM DWatchlist WHERE idPoolUserEntries = :poolUserId AND idDGames = :gameId';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':poolUserId', $poolUserId, PDO::PARAM_INT);
        $this->stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        return $this->modifyData(); 
    }
    
    public function getWatchlist($poolUserId, $weekId) {
        $sql = 'SELECT w.idDGames
                FROM DWatchlist w
	                JOIN DGames2 g ON w.idDGames = g.idDGames
                WHERE w.idPoolUserEntries = :poolUserId
                    AND g.idDWeeks = :weekId';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':poolUserId', $poolUserId, PDO::PARAM_INT);
        $this->stmt->bindParam(':weekId', $weekId, PDO::PARAM_INT);
        return $this->selectRowsArrayNum(); 
    }
    
    public function getCountOfPicksUsedThisWeek($idPoolUserEntries, $idDWeeks) {
        $sql = 'SELECT COUNT(*) AS C FROM vwAllPicks WHERE idPoolUserEntries = :id1 AND idDWeeks = :id2';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':id1', $idPoolUserEntries, PDO::PARAM_INT);
        $this->stmt->bindParam(':id2', $idDWeeks, PDO::PARAM_INT);
        return $this->selectRowAssoc(); 
    }
    
    public function getGameTime($gameId) {
        $sql = 'SELECT Kickoff from DGames2 WHERE idDGames = :gameId';
        $this->stmt = $this->prepare($sql);
        $this->stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        return $this->selectRowAssoc(); 
    }
}	
