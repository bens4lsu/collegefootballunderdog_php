<?php
include_once('../config.php');
class Db
{
    public $db_connection;
    public $db_select;
    public $error;
    public $numRowsReturned;
    
    function __construct()
    {
        $this->db_connection = mysql_connect (DB_SERVER, DB_USER, DB_PASSWORD) or die ($this->error = 'Error on db_connection: '.mysql_error());
        $this->db_select = mysql_select_db (DB_DATABASE, $this->db_connection) or die ($this->error = 'Error on db_select: '.mysql_error());
    }

    function cbSqlQuery ($sql, $bReturnEntireResource = false) {
        $this->error = null;
        $sqlList = mysql_query ($sql) or die ($this->error = $sql.'<br />'.mysql_error());
        if (!is_bool($sqlList)){                           //bms 9/8/11.  Some queries properly return no data.
            $this->numRowsReturned = mysql_num_rows($sqlList);
            if ($bReturnEntireResource){
                return $sqlList;
            }
            else {
                $sqlArray = mysql_fetch_array($sqlList);
                return $sqlArray;
            }
        }
        else {
            $this->numRowsReturned = 0;
            return mysql_insert_id($sqlList);
        }
    }
    
    public function findGame($gameDate, $homeTeamName, $awayTeamName) {
        $sql = 'SELECT g.idDGames, w.idDWeeks
                FROM DGames2 g 
                    JOIN DFootballTeams aw ON g.idDFootballTeamsAway = aw.idDFootballTeams 
                    JOIN DFootballTeams hm ON g.idDFootballTeamsHome = hm.idDFootballTeams 
                    JOIN DWeeks w ON g.idDWeeks = w.idDWeeks
                WHERE aw.TeamName = \''.$awayTeamName.'\' AND hm.TeamName = \''.$homeTeamName.'\' 
                    AND w.WeekDateStart <= \''.$gameDate.'\ AND w.WeekDateEnd >= \''.$gameDate.'\'';
                
        return $cbSqlQuery($sql);
    }
    
    public function createGame($gameDate, $homeTeamName, $awayTeamName, $isNeutralSite) {
        $sql = 'INSERT DGames2 (idDWeeks, idDFootballTeamsAway, idDFootballTeamsHome, IsNeutralSite, Kickoff)
                SELECT w.idDWeeks, aw.idDFootballTeams, hm.idDFootballTeams, \''.$isNeutralSite.'\' , \''.$gameDate.'\' 
                    FROM DFootballTeams aw
	                    JOIN DFootballTeams hm ON aw.TeamName = \''.$awayTeamName.'\'  AND hm.TeamName = \''.$homeTeamName.'\'  
                        JOIN DWeeks w ON w.WeekDateStart <= \''.$gameDate.'\'  AND w.WeekDateEnd >= \''.$gameDate.'\'' ;
        
        return $cbSqlQuery($sql);
    }

}
?>