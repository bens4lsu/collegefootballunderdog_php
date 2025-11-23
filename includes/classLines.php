<?php

ini_set("memory_limit", "140M");
include('classSelectorDOM.php');


class Lines {

    public $url;
    public $data = array();
    
    function __construct($url = 'https://www.espn.com/college-football/lines')
    {
        $this->url = $url;
    
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
        $options = array(

            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $this->url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $data['errno']   = $err;
        $data['errmsg']  = $errmsg;
        $data['content'] = $content;
        $this->data = $data;
    }
    
    
    function currentLines() {
        $this->currentLines_vegasInsider2022();
    }
    
    /*    

    function currentLines_espn() {
        $allGames = array();
        $doc = $this->data['content'];
        $sd = new SelectorDOM($doc);
        $selectorForDateHeader = '#fittPageContainer div.margin-date';
        $day = '';
        foreach ($sd->select($selectorForDateHeader) as $arrDay) {
            foreach($arrDay['children'] as $l1) {
                if ($l1['attributes']['class'] == 'Table__Title margin-subtitle') {
                    $day = $l1['text'];
                }
                else if (isset($l1['children'][0]['children'][0]['children'][0]['children'][1]['attributes']['class']) &&
                            $l1['children'][0]['children'][0]['children'][0]['children'][1]['attributes']['class'] == 'Table__Scroller') {
                    $gameData = $l1['children'][0]['children'][0]['children'][0]['children'][1]['children'][0]['children'][1]['children'];
                    $gameTime = $l1['children'][0]['children'][0]['children'][0]['children'][1]['children'][0]['children'][0]['children'][0]['children'][0]['text'];
                    $time = $day.' '.$gameTime;
                    $gameTime = date('m/d/Y g:i A', strtotime($gameTime));
                    $awayTeam = $gameData[0]['children'][0]['text'];
                    $homeTeam = $gameData[1]['children'][0]['text'];
                    $awayTeamUrl = $gameData[0]['children'][0]['children'][0]['children'][0]['children'][0]['attributes']['href'];
                    $homeTeamUrl = $gameData[1]['children'][0]['children'][0]['children'][0]['children'][0]['attributes']['href'];
                    
                    $potentialSpread = $gameData[0]['children'][2]['text'];
                    $spread = is_numeric($potentialSpread) && $potentialSpread <= 0 ? $potentialSpread : $gameData[1]['children'][2]['text'];
                    if (is_numeric($spread)){
                        $spread = $spread * -1;
                    }
                    
                    $underdog = is_numeric($potentialSpread) && $potentialSpread <= 0 ? 'H' : 'A';
                    
                    $allGames[] = array(
                        'awayTeam'   =>   $awayTeam,
                        'homeTeam'   =>   $homeTeam,
                        'gameTime'   =>   $time,
                        'awayUrl'    =>   $awayTeamUrl,
                        'homeUrl'    =>   $homeTeamUrl,
                        'spread'     =>   $spread,
                        'underdog'   =>   $underdog
                    );
                    
                }
            }
            
        }
        return $allGames;
    }
    
    function currentLines_vegasInsider() {
        $doc = $this->data['content'];
        //print_r($doc);
        $sd = new SelectorDOM($doc);
        $allGames = array();
        $i=0;
        foreach ($sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child') as $game){
            if (! (isset($game['attributes']['class']) 
                && strpos($game['attributes']['class'], 'game-notes') > 0)
                && isset($game['children'][2]['text'])
                && isset($game['children'][4]['text'])
                && isset($game['children'][0]['text'])
                && isset($sd->select('table.frodds-data-tbl tr td.cellTextNorm:nth-child(3)')[$i]['text'])
            ){
                $awayTeam =  $game['children'][2]['text'];
                $homeTeam =  $game['children'][4]['text'];
                $gameTime =  $game['children'][0]['text'];
                $awayUrl  =  isset($game['children'][2]['children'][0]['attributes']['href']) ? $game['children'][2]['children'][0]['attributes']['href'] : null;
                $homeUrl  =  isset($game['children'][4]['children'][0]['attributes']['href']) ? $game['children'][4]['children'][0]['attributes']['href'] : null;
                $spreadString  = trim($sd->select('table.frodds-data-tbl tr td.cellTextNorm:nth-child(3)')[$i]['text']);
                if (substr($spreadString, 0, 3) == chr(0xc2).chr(0xa0).'-'){
                    $spread = substr($spreadString, 3, strpos($spreadString, chr(0xc2).chr(0xa0), 3) - 1);
                    $underdog = 'H';
                }
                else {
                    $spread = substr($spreadString, 
                                     strpos($spreadString, '-', strpos($spreadString, '-') + 1) + 1, 
                                     strrpos($spreadString, chr(0xc2).chr(0xa0)) - strpos($spreadString, '-', strpos($spreadString, '-') + 1) + 1);
                    $underdog = 'A';
                }
                $spread = str_replace(chr(0xc2).chr(0xbd), '.5', $spread);
                if ($sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child') &&
                    array_key_exists($i +1, $sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child')) &&
                    strpos($sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child')[$i+1]['attributes']['class'], 'game-notes') > 0)
                {
                    $note = $sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child')[$i+1]['text'];
                }
                else {
                    $note = '';
                }
    
                $allGames[$i] = array(
                    'awayTeam'   =>   $awayTeam,
                    'homeTeam'   =>   $homeTeam,
                    'gameTime'   =>   $gameTime,
                    'awayUrl'    =>   $awayUrl,
                    'homeUrl'    =>   $homeUrl,
                    'spreadString' => $spreadString.'::'.bin2hex(trim($spreadString)),
                    'spread'     =>   $spread,
                    'note'       =>   $note,
                    'underdog'   =>   $underdog
                );
                $i++;
            }
        }
        return $allGames;
    }
    
    function currentLines_vegasInsider2021() {
        $doc = $this->data['content'];
        $sd = new SelectorDOM($doc);
        $allGames = array();
        $i=0;
        foreach ($sd->select('.oddsGameCell') as $game){
            if (! (isset($game['attributes']['class']) 
                 && strpos($game['attributes']['class'], 'game-notes') > 0)
                 && isset($game['children'][2]['text'])
                 && isset($game['children'][4]['text'])
                 && isset($game['children'][0]['text'])
                 && isset($sd->select('table.frodds-data-tbl tr td.cellTextNorm:nth-child(3)')[$i]['text'])
             ){
                 $awayTeam =  $game['children'][2]['text'];
                 $homeTeam =  $game['children'][4]['text'];
                 $gameTime =  $game['children'][0]['text'];
                 $awayUrl  =  isset($game['children'][2]['children'][0]['attributes']['href']) ? $game['children'][2]['children'][0]['attributes']['href'] : null;
                 $homeUrl  =  isset($game['children'][4]['children'][0]['attributes']['href']) ? $game['children'][4]['children'][0]['attributes']['href'] : null;
                 $spreadString  = trim($sd->select('table.frodds-data-tbl tr td.cellTextNorm:nth-child(3)')[$i]['text']);
                 if (substr($spreadString, 0, 3) == chr(0xc2).chr(0xa0).'-'){
                     $spread = substr($spreadString, 3, strpos($spreadString, chr(0xc2).chr(0xa0), 3) - 1);
                     $underdog = 'H';
                 }
                 else {
                     $spread = substr($spreadString, 
                                      strpos($spreadString, '-', strpos($spreadString, '-') + 1) + 1, 
                                      strrpos($spreadString, chr(0xc2).chr(0xa0)) - strpos($spreadString, '-', strpos($spreadString, '-') + 1) + 1);
                     $underdog = 'A';
                 }
                 $spread = str_replace(chr(0xc2).chr(0xbd), '.5', $spread);
                 if ($sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child') &&
                     array_key_exists($i +1, $sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child')) &&
                     strpos($sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child')[$i+1]['attributes']['class'], 'game-notes') > 0)
                 {
                     $note = $sd->select('table.frodds-data-tbl tr td.cellTextNorm:first-child')[$i+1]['text'];
                 }
                 else {
                     $note = '';
                 }
     
                 $allGames[$i] = array(
                     'awayTeam'   =>   $awayTeam,
                     'homeTeam'   =>   $homeTeam,
                     'gameTime'   =>   $gameTime,
                     'awayUrl'    =>   $awayUrl,
                     'homeUrl'    =>   $homeUrl,
                     'spreadString' => $spreadString.'::'.bin2hex(trim($spreadString)),
                     'spread'     =>   $spread,
                     'note'       =>   $note,
                     'underdog'   =>   $underdog
                 );
                 $i++;
             }
        }
        return $allGames;
    }
    
    
    */
    
    
    
    function currentLines_vegasInsider2022() {
        $doc = $this->data['content'];
        $sd = new SelectorDOM($doc);
        print_r($doc);
    }
}