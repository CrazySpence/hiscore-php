<?php
//Set paths to your hiscore files, shguld work with a/b/bootleg versions of the same game
//Defaults are set for retropie/mame2003

$n942      = "/home/pi/RetroPie/roms/mame-libretro/mame2003/hi/1942.hi";
$galaga    = "/home/pi//RetroPie/roms/mame-libretro/mame2003/hi/galagab2.hi";
$mspacman  = "/home/pi//RetroPie/roms/mame-libretro/mame2003/hi/mspacman.hi";
$pacman    = "/home/pi//RetroPie/roms/mame-libretro/mame2003/hi/pacman.hi";
$wonderboy = "/home/pi//RetroPie/roms/mame-libretro/mame2003/hi/wboy.hi";
$gngoblins = "/home/pi//RetroPie/roms/mame-libretro/mame2003/hi/gng.hi";
?>

<html>
<head>
    <title>phpscore</title>
    <link rel="stylesheet" type="text/css" href="galaga.css" />
</head>

<?php
if(isset($_GET["1942"])) {
    show1942();
    exit(1);
}
if(isset($_GET["galaga"])) {
    showGalaga();
    exit(1);
}

if(isset($_GET["pacman"])) {
    showPacman();
    exit(1);
}

if(isset($_GET["mspacman"])) {
    showMsPacman();
    exit(1);
}

if(isset($_GET["wonderboy"])) {
    showWonderboy();
    exit(1);
}
?>
<?php

showMenu();
?>
</body>
</html>
<?php

//Functions below

function showMenu() {
?>
   <body class="menu">
      SELECT GAME
      <br/>
      <br/>
      <a border=0 href="/?galaga"><img src="img/galagaico.jpg"/></a>
      <a border=0 href="/?1942"><img src="img/1942ico.jpg" /></a>
      <br/>
      <a border=0 href="/?pacman"><img src="img/pacmanico.png" /></a>
      <a border=0 href="/?mspacman"><img src="img/mspacmanico.png" /></a>
<?php
}

function showPacman() {
    global $pacman;
    $data = pacman($pacman);
   ?>
   <body class="pacman">
      <div class="pacmanhi">
          <?php echo $data; ?>
      </div>
   <?php
}

function showMsPacman() {
    global $mspacman;
    $data = ms_pacman($mspacman);
   ?>
   <body class="mspacman">
      <div class="mspacmanhi">
         <?php echo $data; ?>
      </div>
<?php
}

function show1942() {
    global $n942;
    $data = nineteen42($n942);
    ?>
    <body class="n942">
       <div>
           <img class="n942" src="img/1942.jpg">
           <br/>
       </div>
       <div class="n942hi">
          <div style="color:RED;">
             TOP 5 RANKING SCORE !!
          </div>
          <br/>
          <table class="n942">
              <thead>
                 <th></th>
                 <th></th>
                 <th></th>
                 <th></th>
              </thead>
              <tbody>
    <?php

    for ($i = 0; $i < 5; $i++) {
        echo "<tr>";
        if($i == 0) {
            echo "<td style='color:white;'>TOP: </td>\n";
        } else {
            echo "<td style='color:white;'>" . addOrdinalNumberSuffix($i+1) . ": </td>\n";
        }
        echo "<td style='color: gold;'>" . $data["score"][$i] . "</td>\n<td style='color: lightblue;'>" . $data["initials"][$i] . "</td>\n<td style='color: red;'>" . ltrim($data["level"][$i],'0') . "</td>\n";
        echo "</tr>\n";
    }
    echo "</tbody>\n</table>\n</div>\n";
}

function showGalaga()
{
    global $galaga;
    $data = galaga($galaga);
    echo '<body class="galagabody">';
    echo '<div class="galagahi">';
    echo "TOP:" . $data["top"] . "\n<br/>\n";
    for ($i = 0; $i < 5; $i++)
        echo $data["score"][$i] . " " . $data["initials"][$i] . "\n<br/>\n";
    echo '</div>';
}

function showWonderboy()
{
    global $wonderboy;
    $data = wonderboy($wonderboy);
    ?>
    <body class="wonderboy">
    <div>
           <img class="n942" src="img/wonderboy.png">
           <br/>
       </div>
    <div class="wonderboyhi">
    <?php
    for ($i = 0; $i < 7; $i++)
        echo $data["score"][$i] . " " . $data["initials"][$i] . "\n<br/>\n";
    echo '</div>';
}

function showGngoblins()
{
    global $gngoblins;
    $data = gngoblins($gngoblins);

    echo '<body class="gngoblinsbody">';
    echo '<div class="gngoblinshi">';
    for ($i = 0; $i < 10; $i++)
        echo $data["score"][$i] . " " . $data["initials"][$i] . "\n<br/>\n";
    echo '</div>';
}

function wonderboy($file) {
    //Wonder boy is a 21 line file arranged in a fairly easy to parse manner
    //Null is 20
    //Interesting score storage it seems they are cut by a factor of 10 probably to save RAM

    if (!$file) {
        echo "ERROR: No file specified";
        exit();
    }
    $fp = fopen($file,"r") or die("cannot open file!");

    $bytes = array();
    $scoretable = array("score" => array(),"initials" => array(),"top" => 0,"level" => array());

    while(!feof($fp)) {
        $buf = fread($fp,1);
        array_push($bytes,bin2hex($buf));
    }

    reset($bytes); //Start from the beginning
    $position = 0;

    for($lineCounter = 0; $lineCounter < 21; $lineCounter++ ) {
        //Walk the bytes
        $index = $lineCounter; //hexdec($bytes[$position]);
        $score = sprintf("%c%c%c%c%c%c",hexdec($bytes[$position+2]),hexdec($bytes[$position+3]),hexdec($bytes[$position+4]),hexdec($bytes[$position+5]),hexdec($bytes[$position+6]),hexdec($bytes[$position+7]));
        $score = $score * 10;
        $temp = array();
        for ($k=8; $k<11 ; $k++) {

            if(hexdec($bytes[$position+$k] != 20)){
                $strtemp = sprintf("%c",hexdec($bytes[$position + $k]));
                array_push($temp, $strtemp);
            } else {
                array_push($temp," ");
            }
        }
        $name = sprintf("%s%s%s",$temp[0],$temp[1],$temp[2]);
        $scoretable["score"][$index] = ltrim($score,'0');
        $scoretable["initials"][$index] = $name;
        $scoretable["level"][$index] = $level;

        $position = $position + 16;
    }
    return $scoretable;
}

function gngoblins($file) {

}

function nineteen42($file) {
    //1942 hiscore parse
    //1942 has a 26 line long Hex data file arranged into a fairly readable format
    //First byte [0] is the index of the score 00-25. 55 means it's the TOP score
    //[1-4] is the score value
    //[5-12(c)] is the name
    //13 is the level reached
    //14-15 seem to be 00 in all cases
    if (!$file) {
        echo "ERROR: No file specified";
        exit();
    }
    $fp = fopen($file,"r") or die("cannot open file!");

    $bytes = array();
    $scoretable = array("score" => array(),"initials" => array(),"top" => 0,"level" => array());

    while(!feof($fp)) {
        $buf = fread($fp,1);
        array_push($bytes,bin2hex($buf));
    }

    reset($bytes); //Start from the beginning
    $position = 0;

    for($lineCounter = 0; $lineCounter < 26; $lineCounter++ ) {
        //Walk the bytes
        $index = hexdec($bytes[$position]);
        $score = sprintf("%s%s%s%s",$bytes[$position+1],$bytes[$position+2],$bytes[$position+3],$bytes[$position+4]);
        $level = $bytes[$position+13];

        $temp = array();
        for ($k=5; $k<13 ; $k++) {

            if(hexdec($bytes[$position+$k] != 30)){
               $strtemp = sprintf("%c", (55 + hexdec($bytes[$position + $k])));
               array_push($temp, $strtemp);
            } else {
                array_push($temp," ");
            }
        }
        $name = sprintf("%s%s%s%s%s%s%s%s",$temp[0],$temp[1],$temp[2],$temp[3],$temp[4],$temp[5],$temp[6],$temp[7]);
        $scoretable["score"][$index] = ltrim($score,'0');
        $scoretable["initials"][$index] = $name;
        $scoretable["level"][$index] = $level;

        $position = $position + 16;
    }
     return $scoretable;

}

function ms_pacman($file) {
    //shell function, really you could just use pacman($file) but for readability this makes more sense
    return pacman($file);
}

function pacman($file) {
    /*
         This function should handle hiscores for any standard pacman hardware
         so I made it take a file parameter for the clones and sequels the default
         is puckman
    */
    if (!$file) {
        echo "ERROR: No file specified";
        exit();
    }
    $bytes = array();
    $fp = fopen($file,"r") or die("cannot open file!");
    while(!feof($fp)) {
        $buf = fread($fp,1);
        array_push($bytes,bin2hex($buf));
    }
    fclose($fp);
    $bytes = array_reverse($bytes);

    $hk = 0; //100k number
    $tk = 0; //10k
    $k  = 0; //1k
    $hd = 0; //you get the idea
    $t  = 0; //if you don't
    $o  = 0; //stop reading this

    if($bytes[2] != 40) /* 40 is the null value used by the hardware */
        $hk = $bytes[2];
    if($bytes[3] != 40)
        $tk = $bytes[3];
    if($bytes[4] != 40)
        $k = $bytes[4];
    if($bytes[5] != 40)
        $hd = $bytes[5];
    if($bytes[6] != 40)
        $t = $bytes[6];
    if($bytes[7] != 40)
        $o = $bytes[7];

    $hiscore = (($hk * 100000) + ($tk * 10000) + ($k * 1000) + ($hd * 100) + ($t * 10) + $o);
    return $hiscore;
}

function galaga($file) {
    /*
        Notes: galaga stores all the scores one after another 6 numbers max each
        24 is used for null so if your score was 30000 it would be stored
        00 00 00 00 03 24 there are only 5 stored scores and right after the scores
        are the initials stored A to Z starting at hex value 00 and ending at hex value 24
        the very last set of 6 values after the 5 initials sets is the top score by itself
    */
    if (!$file) {
        echo "ERROR: No file specified";
        exit();
    }
    $scoretable = array("score" => array(),"initials" => array(),"top" => 0);
    $bytes = array();
    $fp = fopen($file,"r") or die("cannot open file!");
    while(!feof($fp)) {
        $buf = fread($fp,1);
        array_push($bytes,bin2hex($buf));
    }
    fclose($fp);
    reset($bytes);
    for ($i=0; $i<5 ; $i++) {
        /*
           parse and combine the 5 TOP SCORES
        */

        $temp = array();
        for ($k=0; $k<6 ; $k++) {
            array_push($temp,current($bytes));
            next($bytes);
        }
        $temp = array_reverse($temp);

        //init.
        $hk = 0;
        $tk = 0;
        $k  = 0;
        $hd = 0;
        $t  = 0;
        $o  = 0;

        if($temp[0] != 24) /* 24 is the null value used by the hardware */
            $hk = $temp[0];
        if($temp[1] != 24)
            $tk = $temp[1];
        if($temp[2] != 24)
            $k = $temp[2];
        if($temp[3] != 24)
            $hd = $temp[3];
        if($temp[4] != 24)
            $t = $temp[4];
        if($temp[5] != 24)
            $o = $temp[6];

        $hiscore = (($hk * 100000) + ($tk * 10000) + ($k * 1000) + ($hd * 100) + ($t * 10) + $o);
        array_push($scoretable["score"],$hiscore);
    }
    for ($i=0; $i<5 ; $i++) {
        /*
           Parse and combine the Initials data
        */
        $temp = array();
        for ($k=0; $k<3 ; $k++) {
            /*
                 This is me being lazy what I **should** have done and will have to for other games
                 is made a static array character map but there are only 2 extra characters so I
                 took the easy way out this time
            */
            $strtemp = sprintf("%c",(55 + hexdec(current($bytes))));
            $strtemp = str_replace("a",".",$strtemp);
            $strtemp = str_replace("["," ",$strtemp);
            array_push($temp,$strtemp);
            next($bytes);
        }
        array_push($scoretable["initials"],sprintf("%s%s%s",$temp[0],$temp[1],$temp[2]));
    }
    $temp = array();
    for ($i=0; $i<6 ; $i++) {
        /*
           This is the last of the data in the file, the TOP SCORE
        */
        array_push($temp,current($bytes));
        next($bytes);
    }
    $temp = array_reverse($temp);

    if($temp[0] != 24) /* 24 is the null value used by the hardware */
        $hk = $temp[0];
    if($temp[1] != 24)
        $tk = $temp[1];
    if($temp[2] != 24)
        $k = $temp[2];
    if($temp[3] != 24)
        $hd = $temp[3];
    if($temp[4] != 24)
        $t = $temp[4];
    if($temp[5] != 24)
        $o = $temp[6];

    $hiscore = (($hk * 100000) + ($tk * 10000) + ($k * 1000) + ($hd * 100) + ($t * 10) + $o);
    $scoretable["top"] = $hiscore;

    return $scoretable;
}

function addOrdinalNumberSuffix($num) {
    //This snippet was from https://www.if-not-true-then-false.com/2010/php-1st-2nd-3rd-4th-5th-6th-php-add-ordinal-number-suffix/
    //Thanks snippet JR person
    if (!in_array(($num % 100),array(11,12,13))){
        switch ($num % 10) {
            // Handle 1st, 2nd, 3rd
            case 1:  return $num.'st';
            case 2:  return $num.'nd';
            case 3:  return $num.'rd';
        }
    }
    return $num.'th';
}
?>