<?php include("lp_source.php"); 

    if ($totalvotes == 1) {
        $vc = $totalvotes.' vote cast.';
    }
    else {
        $vc = $totalvotes.' votes cast.';
    }
?>
<html>
<head>
	<title>A Little Something to Make It Interesting</title>
	<meta charset="UTF-8" />
	<style>
	    body {font-family: Lucida Grande,Lucida Sans,Arial,sans-serif}
        table {margin: 15px;border-collapse: collapse;}
        thead tr {background-color:#e3e4fd;}
        tfoot tr {background-color:#cccccc;}
        td, th {padding: 8px; border: 1px solid black;}
        input {margin-bottom: 8px;}
        table img {margin-top:10px;}

	</style>

</head>
<body class="mainbox" >

    <p class="notice">Now that our spreads have been automated, I see something that I didn't know before.  When we put spreads in on a Tuesday, we were just getting games with FBS teams.  FBS are all the teams you regularly see on TV:  all of the power conferences, plus the Group of 5 conferences (your MAC, AAC, etc.), Notre Dame, BYU, and a couple of others.  
    </p>            
    <p class="notice">Later in the week, the program is picking up spreads from FCS games.  These are your Ivy Leagues and schools that you thought only did basketball.
    </p>            
    <p class="notice">Here's an example of games we would see this week:
    </p>            
    <img src="/images/poogames.png">
    
    <p class="notice">Sometimes, a few of those games get into the list too -- if both teams happen to be listed in the system because they played bigger schools some time in the past.
    </p>            
    <p class="notice">I need to make it work consistently -- so we see all of the poo games, or none of them.  Let me know which way you think it should work.
    </p>            
    
    <table class="poll">
        <thead>
            <tr><th><?php echo($question); ?></th></tr>
        </thead>
        <tbody>
            <tr><td>
    
                <?php if($votingstep==1) { echo($step1str); } 
                  if($votingstep==2) { echo($step2str); } 
                  if($votingstep==3) { echo($step3str); }
                ?>
            </td></tr>
        </tbody>
        <tfoot>
            <tr><td><?php echo($vc); ?></td>
        </tfoot>
    </table>
    
    
    
</body>
</html>
            
            