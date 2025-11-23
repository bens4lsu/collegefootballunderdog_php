<?php
    class DogAllPicks
    {
    
        private $bigSqlQuery;
        private $db;
        private $allPicksQuery;  // use this for anything new.  bigSqlQuery is a mess.
        
        
        public $poolId;
        
        public $currentWeek;
        public $currentWeekDesc;
    	public $currentWeekEnds;

        function __construct($pool, $db)
        {
            $this->db = $db;
            $this->poolId = $pool->id;
            
            //Week Info
            $row = $this->db->cbSqlQuery('SELECT * FROM `DWeeks` WHERE DATE(NOW()) <= WeekDateEnd and idPoolSubtypes = '.$pool->subtypeId.' order by WeekDateEnd limit 0,1');
            
            if ($this->db->numRowsAffected == 0) {
            	$this->currentWeek = -1;
            	$this->currentWeekDesc = '  &mdash; Season Complete &mdash;';
            }
            else {
            	$this->currentWeek = $row['idDWeeks'];
            	$this->currentWeekDesc = $row['WeekName'];
            	$this->currentWeekEnds = new DateTime($row['WeekDateEnd']);
            }
            
         
        	$this->allPicksQuery = "select *,  @s1 := case when @c1 <> tt.idPoolUserEntries or @w1 <> tt.idDWeeks then 1 else @s1+1 end as `Rank`
					, @c1 := tt.idPoolUserEntries as IdSet
					, @w1 := tt.idDWeeks as WeekSet 
					, case when @s1 <> 1 then 1 else 0 end as BonusPickUsed
				from (
					select dp.idDPicks
						, dp.idPoolUserEntries
						, dg.idDWeeks
						, dw.WeekName
						, case when dp.TeamPicked = 'A' then dg.idDFootballTeamsHome else dg.idDFootballTeamsAway end as idDFootballTeamsFav
						, case when dp.TeamPicked = 'A' then dg.idDFootballTeamsAway else dg.idDFootballTeamsHome end as idDFootballTeamsDog
						, case when dp.TeamPicked = 'A' then th.TeamName else ta.TeamName end as Favorite
						, case when dp.TeamPicked = 'A' then ta.TeamName else th.TeamName end as  Underdog
						, dp.Spread
						, dg.IsNeutralSite
						, dg.ScoreHome
						, dg.ScoreAway
						, dg.Kickoff
						, pu.Name
						, ta.TeamName as AwayTeam
						, th.TeamName as HomeTeam
						, dp.TeamPicked
						, case when dg.ScoreHome > dg.ScoreAway and dp.TeamPicked = 'H' then Spread 
						       when dg.ScoreHome < dg.ScoreAway and dp.TeamPicked = 'A' then Spread
						       else 0 end as PointsEarned
						, case when dg.ScoreHome > dg.ScoreAway and dp.TeamPicked = 'H' then 1 
						       when dg.ScoreHome < dg.ScoreAway and dp.TeamPicked = 'A' then 1
						       else 0 end as Winners
						from DPicks2 dp
							join DGames2 dg on dp.idDGames = dg.idDGames
							join DFootballTeams th on dg.idDFootballTeamsHome = th.idDFootballTeams
							join DFootballTeams ta on dg.idDFootballTeamsAway = ta.idDFootballTeams
							join DWeeks dw on dg.idDWeeks = dw.idDWeeks
							join PoolUserEntries pue on dp.idPoolUserEntries = pue.idPoolUserEntries
							join PoolUsers pu on pue.idPoolUsers = pu.idPoolUsers
							cross join (select @s1 := 0) s
							cross join (select @c1 := 0) c
							cross join (select @w1 := 0) w 
						where pue.idPools = '.$this->poolId.') tt
				order by tt.idDWeeks, tt.idPoolUserEntries";

        }
        
        private function GetFlattenedPicksSql($i=1)
        {
            
            //$this->db->cbSqlQuery('select @s'.$i.' := 0; select @c'.$i.' := 0; select @w'.$i.' := 0;');
            
            /*
            return 'select dp.idDPicks
            , dp.idPoolUserEntries
            , dg.idDWeeks
            , dw.WeekName
            , dg.idDFootballTeamsFav
            , dg.idDFootballTeamsDog
            , fav.TeamName as Favorite
            , dog.TeamName as Underdog
            , dg.Spread
            , dg.idSiteTypes
            , dg.ScoreFav
            , dg.ScoreDog
            , @s'.$i.' := case when @c'.$i.' <> dp.idPoolUserEntries or @w'.$i.' <> dg.idDWeeks then 1 else @s'.$i.'+1 end as Rank
                , @c'.$i.' := idPoolUserEntries as IdSet
                , @w'.$i.' := dg.idDWeeks
                from DPicks dp
                join DGames dg on dp.idDGames = dg.idDGames
                join DFootballTeams fav on dg.idDFootballTeamsFav = fav.idDFootballTeams
                join DFootballTeams dog on dg.idDFootballTeamsDog = dog.idDFootballTeams
                join DWeeks dw on dg.idDWeeks = dw.idDWeeks
                cross join (select @s'.$i.' := 0) s
                cross join (select @c'.$i.' := 0) c
                cross join (select @w'.$i.' := 0) w
              order by dg.idDWeeks, dp.idPoolUserEntries';
              */
              return "select *,  @s'.$i.' := case when @c'.$i.' <> tt.idPoolUserEntries or @w'.$i.' <> tt.idDWeeks then 1 else @s'.$i.'+1 end as `Rank`
						, @c'.$i.' := tt.idPoolUserEntries as IdSet
						, @w'.$i.' := tt.idDWeeks as WeekSet from (
							select dp.idDPicks
								, dp.idPoolUserEntries
								, dg.idDWeeks
								, dw.WeekName
								, dg.idDFootballTeamsFav
								, dg.idDFootballTeamsDog
								, fav.TeamName as Favorite
								, dog.TeamName as Underdog
								, dg.Spread
								, dg.idSiteTypes
								, dg.ScoreFav
								, dg.ScoreDog
								, dg.Kickoff
									from DPicks2 dp
									join DGames2 dg on dp.idDGames = dg.idDGames
									join DFootballTeams fav on dg.idDFootballTeamsFav = fav.idDFootballTeams
									join DFootballTeams dog on dg.idDFootballTeamsDog = dog.idDFootballTeams
									join DWeeks dw on dg.idDWeeks = dw.idDWeeks
									cross join (select @s'.$i.' := 0) s
									cross join (select @c'.$i.' := 0) c
									cross join (select @w'.$i.' := 0) w ) tt
					  order by tt.idDWeeks, tt.idPoolUserEntries";
        }
    
        function PrintStandings()
        {
            $standings = $this->db->getStandingsTable($this->poolId);
            print '<table class="standings"><tr><th>Name</th><th>Points</th><th># Winners</th><th>Bonus Picks Used</th></tr>';

            foreach ($standings as $s){
                print '<tr><td class="name"><a href="DFramePersonDeets.php?pueid='.$s['idPoolUserEntries'].'">'.$s['Name'].'</a></td><td class="score">'.$s['Score'].'</td><td class="winners">'.$s['Winners'].'</td><td class="bonuses">'.$s['BonusesUsed'].'</td></tr>';
            }
            print '</table>';
            //print $this->bigSqlQuery;
        }
        
        function UserAlreadyPicked($poolUserId, $weekId)
        {
            $check = $this->db->testUserAlreadyPicked($poolUserId, $weekId);
            return isset($check['Picked']) && $check['Picked'] >= 1;
        }
        
        function PrintPicks ($poolUserId = null, $weekId = null)
        {
            $picksql = 'select * from vwAllPicks bigsql ';
            if (isset ($poolUserId)) {
                $picksql .= ' where idPoolUserEntries = '.$poolUserId;
            }
            if (isset ($poolUserId) && isset ($weekId)) {
                $picksql .= ' and idDWeeks = '.$weekId;
            }
            else if (isset ($weekId)){
                $picksql .= ' where idDWeeks = '.$weekId;
            }

            $pr = $this->db->cbSqlQuery($picksql, true);
            if ($pr) {
                foreach ($pr as $p){
                    $pickType = $p['IsBonusPick'] == 0 ? 'Regular Pick for' : 'Bonus Pick Used on ';
                    $info = $pickType.$p['WeekName'];
                    $info .= $p['IsNeutralSite'] == 1 ? '<br><span class="moreinfo">Neutral Site.</span>' : '';
                    $info .= $p['Winners'] == 1 ? '<br><span class="resultgood">WINNER!</span>' : '';
                    $homeTeam = $p['HomeTeam'];
                    $awayTeam = $p['AwayTeam'];
                    $homeSpread = $p['TeamPicked'] == 'H' ? '&nbsp;' : '-'.$p['Spread'];
                    $awaySpread = $p['TeamPicked'] == 'H' ? '-'.$p['Spread'] : '&nbsp;';
                    $homeScore = $p['ScoreHome'];
                    $awayScore = $p['ScoreAway'];
                    print '<table class="game picked"><tr><th colspan="3" class="thtopinfo">'.$info.'</th></tr>';
                    print '<tr><td class="team">'.$awayTeam.'</td><td class="spread">'.$awaySpread.'</td><td class="spread">'.$awayScore.'</td></tr>';
                    print '<tr><td class="team">'.$homeTeam.'</td><td class="spread">'.$homeSpread.'</td><td class="spread">'.$homeScore.'</td></tr></table>';
                }
            }
            
            if (!$pr){
                $sql1 = $this->GetUserFromEntry($poolUserId);
                $sql2 = $this->GetWeekFromID($weekId);
                print '<p class="picked">'.$sql1['Name'].' made no pick for '.$sql2['WeekName'].'</p>';
            }

        }
        
        function CountBonusPicksUsed ($idPoolUserEntries)
        {
            $sql = "select SUM(IsBonusPick) as C from vwAllPicks where idPoolUserEntries = ".$idPoolUserEntries;
            $count = $this->db->cbSqlQuery ($sql);  
            return ($count['C']);
        }
        
        function IsBonusPickUsedThisWeek ($idPoolUserEntries, $idDWeeks)
        {
            $c = $this->db->getCountOfPicksUsedThisWeek($idPoolUserEntries, $idDWeeks);
            return $c['C'] == 2;
        }
        
        function PrintAllPicksForWeek($weekId)
        {
            $sql = 'select * from vwAllPicks where idDWeeks = '.$weekId;
            $this->PrintAllPicksHTMLGenerator($sql);
        }
        
        function PrintAllPicksIfTheGameIsAboutToStart($weekId, $interval) 
        {
            $sql = 'select * from vwAllPicks allpicks where idDWeeks = '.$weekId. ' AND Kickoff - INTERVAL '.$interval.' <= NOW()';           
            $this->PrintAllPicksHTMLGenerator($sql);
        }
        
        function PrintAllPicksHTMLGenerator($sql)
        {
        	$data = $this->db->cbSqlQuery($sql, true);
        	if ($data) {
                foreach ($data as $row){
                    $addClass='';
                    if ($row['Winners'] == 1){
                        $addClass = ' winner';
                    }
                    if ($row['ScoreAway'] + $row['ScoreHome'] > 0 && $row['Winners'] == 0){
                        $addClass = ' loser';
                    }
                    if (isset($row['BonusPickUsed']) && $row['BonusPickUsed'] == 1){
                        $bonusText =  ' (Bonus Pick)';
                    }
                    else {
                        $bonusText = '';
                    }
                    print '<div class="pick"><span class="'.$addClass.'">'.$row['Name'].$bonusText.' - '.$row['Underdog'].' over '.$row['Favorite'].' for '.$row['Spread'];
                
                    print '</span><br>';
                    print '</div>';
                }
            }
        }
                
        function PrintAllPicksForUser ($pueid)
        // picks stop before current week, in case viewer hasn't submitted his pick
        {
            $stopWeek = $this->currentWeek == -1 ? PHP_INT_MAX : $this->currentWeek;
            $W = $this->db->getAllPicksForUser($stopWeek, $this->poolId, $pueid);
            if ($W) {
                foreach ($W as $w){
                    $this->PrintPicks($pueid,$w['idDWeeks']);
                }
            }
        }
        
        /*function UserMadePickThisWeek($idPoolUserEntries, $idDWeeks)
        {
            $sql = 'select count(1) as C from ('.$this->GetFlattenedPicksSql().') picks where  idPoolUserEntries = '.$idPoolUserEntries.' and idDWeeks = '.$idDWeeks;
            $r = $this->db->cbSqlQuery ($sql);
            print 'ooooooo '.$r['C'];
            return ($r['C'] > 0);
        }*/
        
        function GetUserFromEntry($idPoolUserEntries)
        {
            return $this->db->cbSqlQuery('select pu.idPoolUsers, Name from PoolUsers pu join PoolUserEntries pue on pu.idPoolUsers = pue.idPoolUsers where pue.IdPoolUserEntries = '.$idPoolUserEntries);
        }
        
        function GetWeekFromID($idDWeeks)
        {
            return $this->db->cbSqlQuery('select WeekName, WeekDateStart, WeekDateEnd from DWeeks where idDWeeks = '.$idDWeeks);
        }    
        
        function isWeekInThePast($idDWeeks)
        {
        	$today = new DateTime();
        	$weekInQuestion = $this->GetWeekFromID($idDWeeks);
        	$weekEndDate = new DateTime($weekInQuestion['WeekDateEnd']);
        	return $today > $weekEndDate; 
        }   
    }
    
    
    
?>
