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

// Function for adding leagues
function add_saison(&$m_tabelle, $path, $l_file) {
  $l_tabelle = array();
  $handle = fopen ($path . $l_file . '-tab.csv', "rb");
  while (($data = fgetcsv ($handle, 1000, "|")) !== FALSE ) {
    $l_tabelle[] = $data;
  }
  fclose ($handle);

  $l_anzteams = count($l_tabelle);
  for ($i = 0; $i < $l_anzteams; $i ++) {
    if (array_key_exists($l_tabelle[$i][0], $m_tabelle)) {
      $m_tabelle[$l_tabelle[$i][0]][2] = intval($m_tabelle[$l_tabelle[$i][0]][2]) + intval($l_tabelle[$i][2]);
      $m_tabelle[$l_tabelle[$i][0]][3] = intval($m_tabelle[$l_tabelle[$i][0]][3]) + intval($l_tabelle[$i][3]);
      $m_tabelle[$l_tabelle[$i][0]][4] = intval($m_tabelle[$l_tabelle[$i][0]][4]) + intval($l_tabelle[$i][4]);
      $m_tabelle[$l_tabelle[$i][0]][5] = intval($m_tabelle[$l_tabelle[$i][0]][5]) + intval($l_tabelle[$i][5]);
      $m_tabelle[$l_tabelle[$i][0]][6] = intval($m_tabelle[$l_tabelle[$i][0]][6]) + intval($l_tabelle[$i][6]);
      $m_tabelle[$l_tabelle[$i][0]][7] = intval($m_tabelle[$l_tabelle[$i][0]][7]) + intval($l_tabelle[$i][7]);
      $m_tabelle[$l_tabelle[$i][0]][8] = intval($m_tabelle[$l_tabelle[$i][0]][8]) + intval($l_tabelle[$i][8]);
      $m_tabelle[$l_tabelle[$i][0]][9] = intval($m_tabelle[$l_tabelle[$i][0]][9]) + intval($l_tabelle[$i][9]);
      // Team is no longer in the league
      if ($m_tabelle[$l_tabelle[$i][0]][11] == 0) {
        if ($m_tabelle[$l_tabelle[$i][0]][10] == '') {
          $m_tabelle[$l_tabelle[$i][0]][10] = $l_tabelle[$i][10];
        }
      }
      if (strpos($l_tabelle[$i][10], 'M') !== FALSE) {
        $m_tabelle[$l_tabelle[$i][0]][12] ++;
      }
      if (strpos($l_tabelle[$i][10], 'A') !== FALSE) {
        $m_tabelle[$l_tabelle[$i][0]][13] ++;
      }
      $m_tabelle[$l_tabelle[$i][0]][14] ++;
    }
    else {
      $m_tabelle[$l_tabelle[$i][0]] = $l_tabelle[$i];
      $m_tabelle[$l_tabelle[$i][0]][11] = 0;
      $m_tabelle[$l_tabelle[$i][0]][12] = 0;
      $m_tabelle[$l_tabelle[$i][0]][13] = 0;
      $m_tabelle[$l_tabelle[$i][0]][14] = 1;
      if (strpos($m_tabelle[$l_tabelle[$i][0]][10], 'M') !== FALSE) {
        $m_tabelle[$l_tabelle[$i][0]][12]++;
      }
      if (strpos($m_tabelle[$l_tabelle[$i][0]][10], 'A') !== FALSE) {
        $m_tabelle[$l_tabelle[$i][0]][13] ++;
      }
    }
  }
}

// Comparison functions

/*
 * Compares column i of rows a and b.
 * The comparison then goes over all columns
 * until a different value is found.
 * @param i Column in which the comparison is made.
 * @param a, b, lines to be compared
 */
function getcmp($a, $b, $i) {
  if ($a[$i] < $b[$i]) {
    return (int)'1';
  }
  elseif ($a[$i] > $b[$i]) {
    return (int)'-1';
  }
  else {
    if ($i = 15) {
      return 0;
    }
    else {
      return getcmp((int)$a, (int)$b, (int)$i + 1);
    }
  }
}

/*
 * Compares row1 and row2 starting from column 2 (Points+)
 * @param row1, row2, rows to be compared
 */
function cmp0($row1, $row2) {
  return getcmp($row1, $row2, 2);
}

/*
 * Compares row1 and row2 starting from column 7 (Win)
 * @param row1, row2, rows to be compared
 */
function cmp1($row1, $row2) {
  return getcmp($row1, $row2, 7);
}

/*
 * Compares row1 and row2 starting from column 8 (Draw)
 * @param row1, row2, rows to be compared
 */
function cmp2($row1, $row2) {
  return getcmp($row1, $row2, 8);
}

/*
 * Compares row1 and row2 starting from column 9 (Lost)
 * @param row1, row2, rows to be compared
 */
function cmp3($row1, $row2) {
  return getcmp($row1, $row2, 9);
}

/*
 * Compares row1 and row2 starting from column 6 (Games)
 * @param row1, row2, rows to be compared
 */
function cmp4($row1, $row2) {
  return getcmp($row1, $row2, 6);
}

/*
 * Compares row1 and row2 starting from column 4 (Goals+)
 * @param row1, row2, rows to be compared
 */
function cmp5($row1, $row2) {
  return getcmp($row1, $row2, 4);
}

/*
 * Compares row1 and row2 for the difference goals
 * @param row1, row2, rows to be compared
 */
function cmp6($row1, $row2) {
  $diff1 = $row1[4] - $row1[5];
  $diff2 = $row2[4] - $row2[5];
  if ($diff1 < $diff2) {
    return '1';
  }
  elseif ($diff1 > $diff2) {
    return '-1';
  }
  else {
    return '0';
  }
}

/*
 * Compares row1 and row2 in the Points/Game column
 * @param row1, row2, rows to be compared
 */
function cmp7($row1, $row2) {
  if ($row1[2] > 0)
    $durch_a = $row1[2] / $row1[6];
  else
    $durch_a = 0;
  if ($row2[2] > 0)
    $durch_b = $row2[2] / $row2[6];
  else
    $durch_b = 0;
  if ($durch_a < $durch_b) {
    return '1';
  }
  elseif ($durch_a > $durch_b) {
    return '-1';
  }
  else {
    return 0;
  }
}

/*
 * Compares row1 and row2 for the difference points
 * @param row1, row2, rows to be compared
 */
function cmp8($row1, $row2) {
  $diff1 = $row1[2] - $row1[3];
  $diff2 = $row2[2] - $row2[3];
  if ($diff1 < $diff2) {
    return '1';
  }
  elseif ($diff1 > $diff2) {
    return '-1';
  }
  else {
    return '0';
  }
}

/*
 * Compares row1 and row2 starting from column 2 (Points+)
 * @param row1, row2, rows to be compared
 */
function cmp9($row1, $row2) {
  $summe1 = $row1[7] * 3 + $row1[8];
  $summe2 = $row2[7] * 3 + $row2[8];
  if ($summe1 < $summe2) {
    return '1';
  }
  elseif ($summe1 > $summe2) {
    return '-1';
  }
  else {
    return '0';
  }
}

/*
 * Compares row1 and row2 starting from column 12 (Championships)
 * @param row1, row2, rows to be compared
 */
function cmp10($row1, $row2) {
  return getcmp($row1, $row2, 12);
}

/*
 * Compares row1 and row2 starting from column 13 (Descend)
 * @param row1, row2, rows to be compared
 */
function cmp11($row1, $row2) {
  return getcmp($row1, $row2, 13);
}

/*
 * Compares row1 and row2 starting from column 14 (Seasons)
 * @param row1, row2, rows to be compared
 */
function cmp12($row1, $row2) {
  return getcmp($row1, $row2, 14);
}

/*
 * Function to scan the folder and create the output-files
 * @param string $folder
 */
function addLeague($folder, &$m_tabelle) {
  global $dirliga;
  global $diroutput;
  $dirlist = scandir(PATH_TO_LMO . '/' . $diroutput . $folder);
  for ($iscan = 0; $iscan < count($dirlist); $iscan ++) {
    /*
     * check whether it is a directory,
     * if so, continue recursively
     * if no, create a file in the output directory
     */
    if ((is_dir(PATH_TO_LMO . '/' . $diroutput . $folder . '/' . $dirlist[$iscan]))) {
      if (!($dirlist[$iscan] == '.' OR $dirlist[$iscan] == '..')) {
        scan_folder($folder . '/' . $dirlist[$iscan]);
      }
    }
    else {
      if (is_file(PATH_TO_LMO . '/' . $diroutput . $folder . '/' . $dirlist[$iscan])) {
        add_saison($m_tabelle, PATH_TO_LMO . '/' . $diroutput . $folder . '/', basename($dirlist[$iscan], '-tab.csv'));
      }
    }
  }
}

/*
 * Function to check recursively if dirname is exists in directory's tree
 *
 * @param string $dir_name
 * @param string [$path]
 * @return bool
 */
function dir_exists($dir_name = false, $path = './') {
  if (!$dir_name) return false;
  if (is_dir($path . $dir_name)) return true;
  $tree = glob($path . '*', GLOB_ONLYDIR);
  if ($tree && count($tree) > 0) {
    foreach ($tree as $dir) {
      if (dir_exists($dir_name, $dir . '/'))
        return true;
    }
  }
  return false;
}

function scan($folder) {
  scan_folder($folder);
}

/*
 * Function to scan the folder and create the output-files
 * @param string $folder
 */
function scan_folder($folder) {
  global $dirliga;
  global $diroutput;
  $dirlist = scandir(PATH_TO_LMO . '/' . $dirliga . $folder);

  for ($iscan = 0; $iscan < count($dirlist); $iscan ++) {
    /*
     * check whether it is a directory,
     * if so, continue recursively
     * if no, create a file in the output directory
     */
    if ((is_dir(PATH_TO_LMO . '/' . $dirliga . $folder . '/' . $dirlist[$iscan]))) {
      if (!($dirlist[$iscan] == '.' OR $dirlist[$iscan] == '..')) {
        scan_folder($folder . '/' . $dirlist[$iscan]);
      }
    }
    else {
      /*
       * check whether the output dir is available
       * if not, then create it,
       * if yes, create file
       */
      if (dir_exists($folder,PATH_TO_LMO . '/' . $diroutput)) {
        //echo PATH_TO_LMO . '/' . $diroutput . $folder . " exists<br>";
        if (strpos($dirlist[$iscan], '.l98') !== false) {
          if (is_file(PATH_TO_LMO . '/' . $diroutput . $folder . '/' . $dirlist[$iscan] . '-tab.csv')) {
            // TODO check whether data has been updated and then also update the output files.
          }
          else {
            //echo $dirlist[$iscan] . ' does not exist<br>';
            $fileName = PATH_TO_LMO . '/' . $dirliga . $folder . '/' . $dirlist[$iscan];
            include(PATH_TO_ADDONDIR . "/history/lmo-historytab_create.php");
          }
        }
      }
      else {
        //echo PATH_TO_LMO . '/' . $diroutput . $folder . " does not exist<br>";
        FtpMkdir($folder);
      }
    }
  }
}

/*
 * Function to create a Directory with FTP
 * @param string $path
 * @return bool
 */
function FtpMkdir($path) {
  global $cfgarray;
  global $diroutput;
  $connection = ftp_connect($cfgarray['history']['lmo_ftpserver']); // connection
  if (!$connection) die('ERROR: FTP Connection failed.');
  $result = ftp_login($connection, $cfgarray['history']['lmo_ftpuser'], $cfgarray['history']['lmo_ftppass']);
  // check if connection was made
  if ((!$connection) || (!$result)) {
    return false;
    exit();
  }
  else {
    ftp_chdir($connection, $cfgarray['history']['lmo_ftpdir'] . '/' . $diroutput . '/') or die('ERROR: Changing Destination.');
    $dir = preg_split("/\//", $path);
    $path = "";
    $ret = true;
    for ($i = 0; $i < count($dir); $i ++) {
      $path = $dir[$i];
      if (!@ftp_chdir($connection, ftp_pwd($connection).'/'.$path.'/')) {
        if (!ftp_mkdir($connection, ftp_pwd($connection).'/'.$path.'/')) {
          $ret = false;
          break;
        }
        ftp_site($connection, "CHMOD 777 $path") or die("ERROR: FTP SITE CHMOD failed for " . $path);
        ftp_chdir($connection, ftp_pwd($connection) . '/' . $path . '/') or die("ERROR: <b>ftp_chdir</b> failed.");
      }
    }
    return $ret;
  }
}

?>