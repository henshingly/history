<?php
/** Liga Manager Online 4
  *
  * This program is free software; you can redistribute it and/or
  * modify it under the terms of the GNU General Public License as
  * published by the Free Software Foundation; either version 2 of
  * the License, or (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  * General Public License for more details.
  *
  * REMOVING OR CHANGING THE COPYRIGHT NOTICES IS NOT ALLOWED!
  *
  * History Table Addon for LigaManager Online
  * Copyright (C) 2005 by Marcus Schug
  * langer77@gmx.de
  *
  * @author <a href="mailto:langer77@gmx.de">Marcus Schug</a>*
  * @version 1.0
  *
  * History:
  * 1.0: initial Release
  *
  *
  * Update: 30. July 2024
  * 1.5: New release for the LMO PHP8.
  * Customisations, updates installed, bugs removed and new functions added.
  *
  * @author <a href="https://github.com/dwblmo">DwB</a> and <a href="https://github.com/henshingly">Henshingly</a>
  *
  */

$liga = basename($fileName, '.l98');
$file = $folder . '/' . $liga . '.l98';
$action = "admin";
$array = array();
$deflang = "";
$deflang = isset($_POST["xdeflang"])?trim($_POST["xdeflang"]):$deflang;

require(PATH_TO_LMO . "/lmo-langload.php");
$defdateformat = "";

// Open league file
include PATH_TO_LMO . "/lmo-openfile.php";

$fp = fopen(PATH_TO_LMO . '/' . $diroutput . $folder . '/' . basename($file) . '-tab.csv', "wb"); // Create Table CSV
if ($st > 0) {
  $rounds = $wert;
  $act = $st;
}
else {
  $act = $stx;
}
for ($i1 = 0; $i1 < $anzsp; $i1 ++) {
  if (isset($goala[$act-1][$i1]) && $goala[$act-1][$i1] == "-1") $goala[$act-1][$i1] = "_";
  if (isset($goalb[$act-1][$i1]) && $goalb[$act-1][$i1] == "-1") $goalb[$act-1][$i1] = "_";
}

$endtab = $anzst;
include(PATH_TO_LMO . "/lmo-calctable.php");
for ($i1 = 0; $i1 < $anzsp; $i1 ++) {
  if (isset($goala[$act-1][$i1]) && $goala[$act-1][$i1] == "_") $goala[$act-1][$i1] = "-1";
  if (isset($goalb[$act-1][$i1]) && $goalb[$act-1][$i1] == "_") $goalb[$act-1][$i1] = "-1";
}
$x = 0;
$j = 1;
foreach ($tab0 as $y) {
  $x ++;
  $tabledata = explode('|', chunk_split($y, 8, "|"));

  fwrite($fp, $teams[$tabledata[4] - 50000000] . '|');                               //TeamLongName    (long Teamname)
  fwrite($fp, $teamk[$tabledata[4] - 50000000] . '|');                               //TeamAbbrevation (short TeamnName)
  fwrite($fp, applyFactor($punkte[$tabledata[4] - 50000000], $pointsfaktor) .'|');   //Points+
  if ($minus == 2)
  {
    fwrite($fp, applyFactor($negativ[$tabledata[4] - 50000000], $pointsfaktor));     //Points-
  }
  fwrite($fp, '|');
  fwrite($fp, applyFactor($etore[$tabledata[4] - 50000000], $goalfaktor) . '|');     //Goals+
  fwrite($fp, applyFactor($atore[$tabledata[4] - 50000000], $goalfaktor) . '|');     //Goals-
  fwrite($fp, $spiele[$tabledata[4] - 50000000] . '|');                              //Games
  fwrite($fp, $siege[$tabledata[4] - 50000000] . '|');                               //Win
  fwrite($fp, $unent[$tabledata[4] - 50000000] . '|');                               //Draw
  fwrite($fp, $nieder[$tabledata[4] - 50000000] . '|');                              //Loss
  if (($tabledata[4] - 50000000) == $favteam) {                                      //Marking Fav Team
    fwrite($fp, "F");
  }
  if (($x == 1) && ($champ != 0)) {
    fwrite($fp, "M");
    $j = 2;
  }
  if (($x >= $j) && ($x < $j+$anzcl) && ($anzcl > 0)) {
    fwrite($fp, "C");
  }
  if (($x >= $j + $anzcl) && ($x < $j + $anzcl + $anzck) && ($anzck > 0)) {
    fwrite($fp, "Q");
  }
  if (($x >= $j + $anzcl + $anzck) && ($x < $j + $anzcl + $anzck + $anzuc) && ($anzuc > 0)) {
    fwrite($fp, "U");
  }
  if (($x <= $anzteams - $anzab) && ($x > $anzteams - $anzab - $anzar) && ($anzar > 0)) {
    fwrite($fp, "R");
  }
  if (($x <= $anzteams) && ($x > $anzteams - $anzab) && ($anzab > 0)) {
    fwrite($fp, "A");
  }
  fwrite($fp, '|' . $teamm[$tabledata[4] - 50000000]);                               //TeamShortName (Medium-length Teamname)
  fwrite($fp, "\n");
}
fclose($fp);
?>