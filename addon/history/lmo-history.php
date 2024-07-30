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
  *
  * URL-Parameter:
  *
  *    his_liga:     Dateiname der aktuellen Liga
  *
  *    his_ligen:    Ligen die zur Berechnung der ewigen Tabelle genutzt werden sollen,
  *                  außer die aktuelle Liga. //nur im Notfall nutzen
  *
  *    his_folder:   Ordner mit dem Ligenarchiv
  *
  *    his_sort:     Sortiervorgabe der ewigen Tabelle
  *                  0 Standartsortierung nach Punkten
  *                  1 Sortierung nach Spielen
  *                  2 Sortierung nach Siegen
  *                  3 Sortierung nach Toren
  *                  4 Sortierung nach Punkte/Spiel
  *
  *    his_template: Template, dass benutzt werden soll
  *
  *  Beispiel 1: 1.Bundesliga Fussball 2004/2005 mit his_ligen
  *  Sollte nur genutzt werden wenn Zugriff über FTP nicht möglich ist.
  *    his_liga = 1bundesliga2004.l98
  *    his_ligen =1bundesliga2003.l98,1bundesliga2002.l98,1bundesliga2001.l98,1bundesliga2000.l98
  *
  *  Einbindung über IFrame:
  *      <iframe src="<url_to_lmo>/addon/history/lmo-history.php?his_liga=1bundesliga2004.l98&his_ligen=1bundesliga2003.l98,1bundesliga2002.l98,1bundesliga2001.l98,1bundesliga2000.l98"><url_to_lmo>/addon/history/lmo-history.php?his_liga=1bundesliga2004.l98&his_ligen=1bundesliga2003.l98,1bundesliga2002.l98,1bundesliga2001.l98,1bundesliga2000.l98</iframe>
  *    (die Parameter his_sort und his_template bei Bedarf mit &amp;his_sort=<integer>&amp;his_template=<integer> anhängen
  *
  *  Einbindung über include:
  *      $his_liga = '1bundesliga2004.l98'
  *      $his_ligen= '1bundesliga2003.l98,1bundesliga2002.l98,1bundesliga2001.l98,1bundesliga2000.l98'
  *      (auch hier bei Bedarf his_sort und/oder his_template angeben: $a = <integer>;$his_template = '<string>'; )
  *      include ("<pfad_zum_lmo>/addon/history/lmo-history.php.php");
  *
  *  Beispiel 2: 1.Bundesliga Fussball 2004 / 2005 mit his_liga
  *  Sollte nur genutzt werden wenn Zugriff über FTP nicht möglich ist.
  *    his_liga = 1bundesliga2004.l98
  *    his_folder = archiv/bundesliga
  *
  *    Einbindung über IFrame:
  *      <iframe src="<url_to_lmo>/addon/history/lmo-history.php?his_liga=1bundesliga2004.l98&his_folder=archiv/bundesliga</iframe>
  *      <iframe src="<url_to_lmo>/addon/history/lmo-history.php?his_liga=1bundesliga2004.l98&his_ligen=1bundesliga2003.l98,1bundesliga2002.l98,1bundesliga2001.l98,1bundesliga2000.l98"></iframe>
  *      (die Parameter his_sort und his_template bei Bedarf mit &amp;his_sort=<integer>&amp;his_template=<integer> anhängen
  *
  *    Einbindung über include:
  *      $his_liga = '1bundesliga2004.l98'
  *      $his_folder = archiv/bundesliga
  *    // auch hier bei Bedarf his_sort und/oder his_template angeben:
  *      $his_sort = <integer>;
  *      $his_template = '<string>';
  *      include ("<pfad_zum_lmo>/addon/history/lmo-history.php.php");
  *
  * Installation:
  * index.php ins Verzeichnis <lmo_root>/addon/history/ kopieren.
  * lmo-his_liga_create.php ins Verzeichnis <lmo_root>/addon/history/ kopieren.
  * lmo-history.php ins Verzeichnis <lmo_root>/addon/history/ kopieren.
  * lmo-history_func.php ins Verzeichnis <lmo_root>/addon/history/ kopieren.
  * lmo-historytab_create.php ins Verzeichnis <lmo_root>/addon/history/ kopieren.
  * history.tpl.php ins Verzeichnis <lmo_root>/template/history/ kopieren
  * history_all.tpl.php ins Verzeichnis <lmo_root>/template/history/ kopieren
  * history_mid.tpl.php ins Verzeichnis <lmo_root>/template/history/ kopieren
  * *lang.txt-dateien ins Verzeichnis <lmo_root>/lang/history/ kopieren
  * cfg.txt ins Verzeichnis <lmo_root>/config/history/ kopieren
  *
  * Im Adminmenü des LMO's unter ->Optionen ->Addons ->history die 4 notwendigen Angaben
  * eingeben und speichern. Beim nächsten Aufruf des Addons werden die notwendigen
  * CSV-Dateien dann im Output Ordner erstellt.
  *
  * Hinweis:
  * Es ist nicht gestattet den Hinweis auf den Autor zu entfernen!
  * Eigene Templates müssen den Hinweis auf Autor des Scripts enthalten.
  *
  */

require_once(dirname(__FILE__) . '/../../init.php');
require_once(PATH_TO_ADDONDIR . "/classlib/ini.php");
$aktJahr = date("Y");
$output_sprachauswahl = "";

// By Get certain parameters (for IFRAME)
$m_liga       = isset($_GET['his_liga'])     ?  urldecode($_GET['his_liga'])    : '';
$m_ligen      = isset($_GET['his_ligen'])    ?  urldecode($_GET['his_ligen'])   : '';
$m_template   = isset($_GET['his_template']) ?  urldecode($_GET['his_template']): "history";
$m_sort       = !empty($_GET['his_sort'])    ?  urldecode($_GET['his_sort'])    : '0';
$m_headline   = !empty($_GET['his_headline'])?  urldecode($_GET['his_headline']): $text['history'][1];
$archivFolder = isset($_GET['his_folder'])   ?  urldecode($_GET['his_folder'])  : (isset($his_folder) ?  $his_folder : basename($ArchivDir));  // Default

// Directly certain parameters (for include/require)
$m_liga       = isset($his_liga)          ?  $his_liga               : $m_liga;
$m_ligen      = isset($his_ligen)         ?  $his_ligen              : $m_ligen;
$m_template	  = isset($his_template)      ?  $his_template           : $m_template;
$m_sort       = isset($his_sort)          ?  $his_sort               : $m_sort;
$m_headline   = isset($his_headline)      ?  $his_headline           : $text['history'][1];
$archivFolder = isset($_GET['his_folder'])?  $_GET['his_folder']     : (isset($his_folder) ?  $his_folder : basename($ArchivDir));  // Default
$deflang      = isset($_POST["xdeflang"]) ?  trim($_POST["xdeflang"]): $deflang;

if ($deflang == "") $deflang = "Deutsch";

include(PATH_TO_ADDONDIR . "/history/lmo-history_func.php");
include(PATH_TO_ADDONDIR . "/history/lmo-his_liga_create.php");

// Language selection
if ($einsprachwahl==1)
{
  $output_sprachauswahl = getLangSelector();
}

// If IFRAME - complete HTML document
if (basename($_SERVER['PHP_SELF']) == "lmo-history.php")
{?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>History</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
    <style type="text/css">
      html,body {margin:0;padding:0;background:transparent;}
    </style>
</head>
<body><?php
}
/**Format of CSV-File:
  *       0    |         1         |   2   |   3   |  4   |  5   |  6  | 7 |  8 |  9 |    10    |     11
  *TeamLongName|TeamnameAbbrevation|Points+|Points-|Goals+|Goals-|Games|Win|Draw|Loss|  Marking |TeamShortName
  *  Teamname  |     Kurzname      | Pkt.+ | Pkt.- | Tore+| Tore-| Sp. | + | o  | -  |Markierung| Mittelname
**/
if ($cfgarray['history']['lmo_autocreate'] == 1)
{
  scan($archivFolder);
}

if (file_exists(PATH_TO_LMO . '/' . $diroutput . $m_liga . '-tab.csv'))
{
  $template = new HTML_Template_IT(PATH_TO_TEMPLATEDIR . '/history' );
  $template -> loadTemplatefile($m_template . ".tpl.php");

  $m_tabelle = array();
  $handle = fopen (PATH_TO_LMO . '/' . $diroutput . $m_liga . '-tab.csv',"rb");
  while (($data = fgetcsv ($handle, 1000, "|")) !== FALSE)
  {
    $m_tabelle[$data[0]] = $data;
    $m_tabelle[$data[0]][10] = "";
    //Mannschaft in Liga
    $m_tabelle[$data[0]][11] = 1;
    //Meisterschaften
    $m_tabelle[$data[0]][12] = 0;
    //Abstiege
    $m_tabelle[$data[0]][13] = 0;
    //Saisons
    $m_tabelle[$data[0]][14] = 1;
  }
  fclose($handle);
  // Converting the passed leagues into an array
  if ($m_ligen != '')
  {
    $m_ligen = explode(', ', $m_ligen);
    for ($i = 0; $i < count($m_ligen); $i ++)
    {
      add_saison($m_tabelle,PATH_TO_LMO . '/' . $diroutput, $m_ligen[$i]);
    }
  }
  else
  {
    addLeague($archivFolder, $m_tabelle);
  }
  // Rekeying the table
  $r_tabelle = array();
  $keys = array_keys($m_tabelle);
  for ($i = 0; $i < count($keys); $i ++)
  {
    $r_tabelle[] = $m_tabelle[$keys[$i]];
  }
  $m_tabelle = $r_tabelle;
  switch($m_sort)
  {
    case(0):  usort($m_tabelle,'cmp0');   break; // Sorting by points (default)
    case(1):  usort($m_tabelle,'cmp1');   break; // Sorted by victories
    case(2):  usort($m_tabelle,'cmp2');   break; // Sorted by draw
    case(3):  usort($m_tabelle,'cmp3');   break; // Sorted by defeats
    case(4):  usort($m_tabelle,'cmp4');   break; // Sorting by games
    case(5):  usort($m_tabelle,'cmp5');   break; // Sorting by +goals
    case(6):  usort($m_tabelle,'cmp6');   break; // Sorting by diff. +goals/goals
    case(7):  usort($m_tabelle,'cmp7');   break; // Sorting by average points per game
    case(8):  usort($m_tabelle,'cmp8');   break; // Sorting by diff. +points/points
    case(9):  usort($m_tabelle,'cmp9');   break; // Sorting by +points
    case(10): usort($m_tabelle,'cmp10');  break; // Sorting by championships
    case(11): usort($m_tabelle,'cmp11');  break; // Sorted by descent
    case(12): usort($m_tabelle,'cmp12');  break; // Sorting by seasons
  }
  $m_anzteams = count($m_tabelle);
  for ($j = 0; $j < $m_anzteams; $j ++)
  {
    $template -> setCurrentBlock("Inhalt");
    if (basename($_SERVER['PHP_SELF']) == "lmo-history.php")
    {
      $sort_link = URL_TO_ADDONDIR . "/history/lmo-history.php?his_liga=$m_liga&amp;his_headline=$m_headline&amp;his_folder=$archivFolder&amp;his_template=$m_template&amp;";
    }
    else
    {
      $sort_link = $_SERVER['SCRIPT_NAME']."?";
    }
    $template -> setVariable("LINK", $sort_link);
    $template -> setVariable("Headline", $m_headline);
    $template -> setVariable("Head_Teamname", $text[124]);
    $template -> setVariable("copy", $text['history'][0]);
    $template -> setVariable("Head_Championships", $text['history'][7]);
    $template -> setVariable("Head_Descents", $text['history'][8]);
    $template -> setVariable("Head_Playtimes", $text['history'][9]);
    $template -> setVariable("Head_Matches", $text['history'][10]);
    $template -> setVariable("Head_Won", $text['history'][11]);
    $template -> setVariable("Head_Draw", $text['history'][12]);
    $template -> setVariable("Head_Lost", $text['history'][13]);
    $template -> setVariable("Head_Goals", $text['history'][14]);
    $template -> setVariable("Head_Goalsdiff", $text['history'][15]);
    $template -> setVariable("Head_Points", $text['history'][16]);
    $template -> setVariable("Head_Pointsdiff", $text['history'][17]);
    $template -> setVariable("Head_Points_Average", $text['history'][18]);
    $template -> setVariable("TeamIcon", HTML_smallTeamIcon($m_liga, $m_tabelle[$j][0], " alt=''"));
    $m_3punkte = ($m_tabelle[$j][7] * 3) + $m_tabelle[$j][8];
    $template -> setVariable(array("Position"               => "<strong>" . ($j + 1) . "</strong>"));
    $template -> setVariable(array("TeamLong"               => $m_tabelle[$j][0]));
    $template -> setVariable(array("TeamMiddel"             => (isset($m_tabelle[$j][11])?$m_tabelle[$j][11]:'')));
    $template -> setVariable(array("TeamShort"              => $m_tabelle[$j][1]));
    $template -> setVariable(array("3Points"                => $m_3punkte));
    $template -> setVariable(array("PlusPoints"             => $m_tabelle[$j][2]));
    if ($m_tabelle[$j][3] != '')
    {
      $template -> setVariable(array("MinusPoints"          => $m_tabelle[$j][3]));
      if (($t_diff = $m_tabelle[$j][2] - $m_tabelle[$j][3]) > 0) $t_diff = '+' . $t_diff;
      $template -> setVariable(array("Content_Pointsdiff"   => $t_diff));
    }
    $template -> setVariable(array("PlusGoals"              => $m_tabelle[$j][4]));
    $template -> setVariable(array("MinusGoals"             => $m_tabelle[$j][5]));
    if (($m_diff = $m_tabelle[$j][4] - $m_tabelle[$j][5]) > 0) $m_diff = '+' . $m_diff;
    $template -> setVariable(array("Content_Goalsdiff"      => $m_diff));
    $template -> setVariable(array("Content_Matches"        => $m_tabelle[$j][6]));
    $template -> setVariable(array("Content_Won"            => $m_tabelle[$j][7]));
    $template -> setVariable(array("Content_Draw"           => $m_tabelle[$j][8]));
    $template -> setVariable(array("Content_Lost"           => $m_tabelle[$j][9]));
    $style = '';
    if ($m_tabelle[$j][6] > 0)
    {
      $m_durch = round($m_tabelle[$j][2] / $m_tabelle[$j][6], 2);
    }
    else
    {
      $m_durch = 0;
    }
    $template -> setVariable(array("Content_Points_Average" => $m_durch));
    $template -> setVariable(array("Content_Championships"  => $m_tabelle[$j][12]));
    $template -> setVariable(array("Content_Descents"       => $m_tabelle[$j][13]));
    $template -> setVariable(array("Content_Playtimes"      => $m_tabelle[$j][14]));
    $style = '';
    if (strpos($m_tabelle[$j][10], 'F') !== FALSE)  //FavTeam
    {
      $style .= "font-weight:bolder;";
      $template -> setVariable(array("Style" => $style));
    }
    //Legende
    $template -> setVariable(array("Meister"                => $text['history'][2]));
    $template -> setVariable(array("Abstieg"                => $text['history'][3]));
    $template -> setVariable(array("aktJahr"                => $aktJahr));
    $template -> parseCurrentBlock();
  }
  $template -> setVariable("Language_Selection", $output_sprachauswahl);
  $template -> show();
}
else
{
  echo getMessage($text['history'][5] . " " . $his_liga, TRUE);
}

// If IFRAME - complete HTML document
if (basename($_SERVER['PHP_SELF']) == "lmo-history.php")
{?>
</body>
</html><?php
}?>