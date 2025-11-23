<?php

$json = file_get_contents("http://localhost:8086/getLines");
$returnData = json_decode($json);
$matchErrors = $returnData->teamNameMatchErrors;
if ($matchErrors) {
    print("Match Errors:<br>");
    foreach ($matchErrors as $team) {
        print("  ".$team."<br>");
    }
}

?>

