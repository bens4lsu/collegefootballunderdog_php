<?php
    include ('page-init.php');
    include ('./includes/classDogAllPicks.php');
    //include ('./includes/classDogGamesForWeek.php');
    include ('./includes/classDogGame.php');
    include ('./includes/classGamesConstructType.php');
    include ('./includes/classWatchlist.php');
    include ('./includes/classGamesForWeekFromAPI.php');

    
    //Which week is it now?
    //$row = $db->cbSqlQuery('SELECT * FROM `DWeeks` WHERE NOW() < WeekDateEnd and idPoolSubtypes = '.$pool->subtypeId.' order by WeekDateEnd limit 0,1');
    //$weekDescription = $row['WeekName'];
      
    $dog = new DogAllPicks($pool, $db);
    if (isset($user->arr_Pools[$poolId])){
        $poolUserId = $user->arr_Pools[$poolId];
    }
    $weekId = $dog->currentWeek;
    $weekDescription = $dog->currentWeekDesc;
    //$games = new GamesForWeek($weekId, $db, GamesConstructType::fromLines, $user->isAdmin);
    $games = new GamesForWeekFromAPI($poolUserId);
    //print_r($games);
    $watchlist = new Watchlist ($weekId, $poolUserId, $db);

    $initTab = isset($_GET['tab']) ? $_GET['tab'] : 0;
    
    if ($user->personId == 4) {
        include('DMatchErrors.php');
    }
?>	

<html>
<head>
	<title>A Little Something to Make It Interesting</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="./css/jquery-ui-1.9.0.custom.min.css" />
	<link rel="stylesheet" href="./css/jquery.dataTables.css" />
	<link rel="stylesheet" href="./css/infodrivenlife.css" />

	<script language="javascript" type="text/javascript"  src="https://code.jquery.com/jquery-1.9.1.js"></script>
	<script language="javascript" type="text/javascript" src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script language="javascript" type="text/javascript" src="./js/jquery.dataTables.min.js"></script>
   	<script language="javascript" type="text/javascript" src="./js/jquery.dataTables.columnFilter.js"></script>
   	<script language="javascript" type="text/javascript" src="./js/jquery.MultiFile.pack.js"></script>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>

	<style>
	    body {font-family: Lucida Grande,Lucida Sans,Arial,sans-serif}
        #buttonlockpick{margin-top:20px;}
        #frAllPicks, #frPlayerStandings, #frPoll {float:left; margin-left:5px; margin-top:8px; width:762px; height:1000px; }
        #resizable {float:left; width:320px; height:800px; margin-top:8px; margin-right:5px;}
        #frWeeks {width:100%; height:100%;}
        .tlevbox {position:relative;}
        table.game td, th {border:1px solid #000000; line-height:18px;}
        table.game td a {text-decoration: none; }
        .wl-img {height:14px; display:inline;margin-top: 8px;}
        th{background-color:#5C9CCC;color:#FFFFFF;font-weight:bold;}
        table.game {border-collapse:collapse; margin-bottom:1.5em;margin-left:20px;}
        table.picked{margin-left:40px;}
        .team {min-width:215px;}
        .spread{min-width:55px; text-align:center;}
        .radioselect{vertical-align:middle;}
        .gameid{vertical-align:middle;min-width:34px;text-align:center;}
        .topinfo{font-size:85%; background-color:#D1D1D1; width:540px;padding:0.4em;}
            
        .messmeta{font-size:85%;}
        .messname{font-weight:bold; color:#032C49;}
        .messtime{font-weight:bold; color:#490403;}
        .messlevel{border:2px solid #D1D1D1; padding:7px;}
        .message{padding-bottom:13px;}
                
        .messreply img{height:11px; padding-right:4px; display:inline}
        .messreply a {text-decoration:none; font-size:75%; color:#490403;}
            
        #poolSelector {display:inline-block; }
        #displayOptions {display: inline-block; margin-left:25px;}
        #logout {display:inline-block; margin-left:25px;}
            
        .poolSelectOption_IsOver {color:#C6C6C6;}
        
        #displayOptions {margin-bottom:16px;}
        #displayOptions input[type="radio"] {margin-right: 8px;}
        
        .notes-header {font-weight: bold; margin-bottom: 2em;}
        .notes-body {margin-left: 3em; margin-bottom:3em; }
        .notes-body li {margin-bottom: 1em;}
        .notes-body img {border: 2px solid #6b9bc8; margin-top:0.7em; margin-bottom:0.7em;display:block;}

	</style>

</head>
<body class="mainbox" >

	<script type="text/javascript">
	
	    var watches = <?php print $watchlist->jsonArray(); ?>;
	    
	    function watchlistMod(action, gameId) {
	        $.ajax({
				url: "DWatchlist.php",
				dataType:"json",
				async: false,
				data: {'action' : action,
				    'game' : gameId,
				    'userEntry' : <?php print $poolUserId; ?>
				},
				type: "post",
				success : function(o, t){
                    if (o.status == 'error'){
                        alert('Error updating watchlist:  ' + o.message);
                    }
				},
				error: function(o) {
                    alert('Error updating watchlist on system database.  Please try again later.');
                }
			});
	    }
	    
		$(document).ready(function() {

            $("#tabs").tabs({active: <?php print $initTab; ?>});
			
			var mTable = $("#main_table").dataTable( {
				"iDisplayLength" : 25	
			} )
			
			$(".resizable").resizable({handles: 'e, w'});

/*				.columnFilter({
					aoColumns: [ null,
								{type: "select", values: <?php //print $sopts; ?> },
								{type: "number-range" },
								{type: "number-range" },
								null,
								{type: "number-range" },
								{type: "number-range" },
								{type: "number-range" },
								{type: "number-range" },
								{type: "number-range" },
								{type: "number-range" },
								{type: "number-range" }]						
				}); 
			mTable.fnPageChange('last');
*/

			$('#frmBigPicker').on('submit', function() {
				$(this).find('#buttonlockpick').prop('disabled',true);
			});
			
			$('#scrollToTop').click(function(){ 
                $('html,body').animate({ scrollTop: 0 }, 'slow');
                return false; 
            });
            
            $('#buttonlockpick').button({'disabled' : true});
            
            $("input[name='picks']").change(function(){
                $('#buttonlockpick').button('enable');
            });
            
            
			// watchlist icon
			
			$.each(watches, function(key, value){
			    var watchlistImgSelector = "#wl" + value;
			    $(watchlistImgSelector).attr('src', 'images/watchlist-y.png');
			});
			
			$('.wl-img').click(function(){
						    
			    var wl_id = $(this).attr('id');
			    var id = parseInt(wl_id.substr(2), 10);
			    
			    if ($.inArray(id, watches) == -1) {
			        watches.push(id);
			        $('#' + wl_id).attr('src', 'images/watchlist-y.png');
			        $('#radioShowWlOnly').prop('disabled',false);
			        
			        watchlistMod('set', id);
			        
			    }
			    else {
			        watches = $.grep(watches, function(value) {
			            return value != id;
			        });
			        $('#' + wl_id).attr('src', 'images/watchlist-n.png');
			        if (watches.length == 0) {
			            $('#radioShowWlOnly').prop('disabled',true);
			        }
			        if (watches.length == 0 && $('#radioShowAll').prop('checked') == false) {
			            alert("No games remain on watch list.  View will now switch to show all games.");
			            $('#radioShowAll').prop('checked', true);
			            $("table.game").css("display", "block");
			        }
			        else if ($('#radioShowAll').prop('checked') == false) {
			            $('table#game-' + id).css('display', 'none');
			        }
			        
			        watchlistMod('unset', id);
			    }
			});
			
			// watchlist radio
			$('#displayOptions input').change(function() {
			    $("table.game").css("display", "block");
                            if ($('#radioShowWlOnly').prop('checked')) {
                                $("table.game").css("display", "none");
                                $.each(watches, function(key, value){
                                    $("table#game-" + value).css("display", "block");
                                });
                            }  
			});
			
			if (watches.length == 0) {  // nothing in watchlist, so don't allow show my watchlist
                $('#radioShowWlOnly').prop('disabled',true);
                $('#radioShowAll').prop('checked', true);
            }
            else {
                $('#radioShowWlOnly').prop('disabled',false);
            }
		});	


	</script>

    <?php if ($showPoolSelector){ ?>
        <div id="poolSelector">
            <?php print $user->printPoolSelectionForm(); ?>
        </div>
    <?php } ?>
            
    <div id="logout"><a href="login.php">Log Out</a></div>
            
    <div id="tabs">

        <ul>
                <li><a href="#tabs-1">Make Pick</a></li>
                <li><a href="#tabs-2">Standings</a></li>
                <li><a href="#tabs-3">All Picks</a></li>
                <li><a href="#tabs-4">Messages</a></li>
                <li><a href="#tabs-5">System Notes</a></li>
        </ul>
			
        <div id="tabs-1" class="tlevbox">
            <p class="topinfo">Making Picks: The visitor is listed on top and home team on bottom.<br><br>The negative number will be next to the favorite, indicating the number of points they are giving.  As an additional visual clue, the team you are picking is in bold.<br><br>New weeks show up Tuesday morning.  Lines move in real time, based on an online lookup.</p>
            
            <p>Server Time:  <?php print date("n/d/Y g:i A"); ?>
                <?php
                    // see if pick is already made
                    if (! $user->hasCurrentPool || $pool->isOver) {  ?>
                        
                        <h4>No Current Games</h4>
                        <p>You are not set up for any current games.  Please contact your league commissioner to be added to a game that is configured.</p>
                        
                        <?php
                    }
                    else if ($dog->UserAlreadyPicked($poolUserId, $weekId)){
                        print '<h4>Your Pick for '.$weekDescription.' is locked in!</h4>';
                        $dog->PrintPicks($poolUserId, $weekId);

                        if ($pool->arrConfigOptions['NUMBONUSPICKS'] > $dog->CountBonusPicksUsed($poolUserId)
                            && ! $dog->IsBonusPickUsedThisWeek($poolUserId, $weekId))
                        {
                            ?><h4>Add Bonus Pick, if desired</h4>
                            
                            <div id="displayOptions">
                                <input type="radio" name="showAllOrWL" value="all" id="radioShowAll" checked>Show All Available Picks</input>
                                <input type="radio" name="showAllOrWL" value="wl" id="radioShowWlOnly">Show My Watch List Only</input>
                            </div><?php
                            $games->printPickTable();
                        }
                    }
                    else { ?>
                        <h4>Make Pick for <?php print $weekDescription; ?></h4>
                            
                        <div id="displayOptions">
                            <input type="radio" name="showAllOrWL" value="all" id="radioShowAll" checked>Show All Available Picks</input>
                            <input type="radio" name="showAllOrWL" value="wl" id="radioShowWlOnly">Show My Watch List Only</input>
                        </div>
                        <?php
                        $games->printPickTable();
                    }       
                ?>
                
                <div><a href="#" id="scrollToTop">Jump to Top</a></div>  

            </div>

            <div id="tabs-2" class="tlevbox">
                <div><iframe src="DFrameStandings.php" id="frPlayerStandings" name="frPlayerStandings"></iframe></div>
                <div style="clear:both;"></div>

            </div>
			
            <div id="tabs-3" class="tlevbox" >
                <div id="resizable"><iframe src="DFrameWeeks.php?week=<?php print $weekId; ?>" id="frWeeks" name="frWeeks"></iframe></div>
                <div><iframe src="DFrameAllPicks.php?week=<?php print $weekId; ?>" id="frAllPicks" name="frAllPicks"></iframe></div>
                <div style="clear:both"></div>
			
            </div>

            <div id="tabs-4" class="tlevbox">
                <p class="messreply"><a href="PAddMessage.php?pool=<?php print $poolId; ?>">Add New Message</a></p>
                <?php $pool->PrintMessages(); ?>
                <p class="messreply"><a href="PAddMessage.php?pool=<?php print $poolId; ?>">Add New Message</a></p>
			
            </div>
			
            <div id="tabs-5" class="tlevbox">
                <?php include ("systemNotes.php"); ?>
                <div style="clear:both;"></div>
            </div>
	</div>
	
</body>
</html>
