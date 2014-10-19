<?php
/**
 * @package	OOB.specific.functionality
 * @subpackage	com_turnering
 * @copyright	Copyright (C) 2011-14 INC Trampa
 * @author      René Gedde
 * @license	GNU General Public License version 2
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');
$mode=""; $kreds=""; $dbuId="";$matchId="";

// Find ud af, hvad der skal vises
if(isset($_GET['m'])) {$mode = $_GET['m'];}  // list, show, foraar
if(isset($_GET['k'])) {$kreds = $_GET['k'];}  // ØOB-kredsnr
if(isset($_GET['id'])) {$dbuId = $_GET['id'];}  // DBU-kredsnr

// for at fange internt link tilbage til kampplanen
if(isset($_GET['matchid'])) {$matchId = $_GET['matchid'];}  // DBU-kredsnr
if ($matchId != '') {
	$poolId = $_GET['poolid']; 
//    header("Location: turnering?m=sk&o=" . $kreds . "&k=" . $poolId);
	showKampInfo();
}

if ($mode == 'noshow') {
    echo '<h2>Spilleplan og stilling</h2>';
    echo '<p>Der er endnu ikke udarbejdet turneringsplaner eller stillinger.<br>';
    echo 'Men hvis du checker i den nærmeste tid, vil den formentlig snart dukke op...</p>';
}

// Her afgøres om vi skal vise hele turneringen eller kun en kreds
switch (substr($mode, 0, 1))
{
    case 'l':
        listTurnering();
        break;
    case 's':
        showKreds();
        break;
    default:
}

function listTurnering() {
    
    $type = substr($_GET['m'], 1, 1);
    $visForaar = substr($_GET['m'], 2, 1);
    if ($visForaar == 'f') {
        $foraar = true;
    } else {
        $foraar = false;
    }

    // Basis-url til selv-reference
    $baseUrl = 'index.php/component/turnering';
    
    // Opret et Joomla database object
    //$db =& JFactory::getDBO();
	$temp = JFactory::getDBO();
	$db =& $temp;

    // Check hvor på året vi er og hent den tilhørende del af turneringen
    if (time() < strtotime('3-7-' . date("Y")) or ($foraar)) {
        $foraar = true;
        $query = "SELECT * FROM #__dbu_kredse WHERE kreds < 20 ORDER BY alder,kreds;";
    } else {
        $foraar = false;
        $query = "SELECT * FROM #__dbu_kredse WHERE (kreds > 20) OR (Gennemgaaende) ORDER BY alder,kreds;";
    }

    $db->setQuery($query);
    $result = $db->query();

    $num_rows = $db->getNumRows();

    // Hent hele resultatet
    $kredsListe = $db->loadAssocList();

    // initiér alders-tæller
    $alder = 0;

    // Overskrift
    echo '<p><h2>ØOB turnering ';
    if ($foraar) {
        echo ' forår ';
    } else {
        echo ' efterår ';
    }
    
    echo date("Y");

    echo '</h2></p>';
        
if (!empty($kredsListe)) {	
    //start første linie
    echo "<table border='3'><tr>";

    //gennemløb alle kredse
    for ($kredsNr = 0; $kredsNr <= $num_rows; $kredsNr++) {
        if ($kredsNr != $num_rows) { //hvis vi ikke har nået den sidste kreds, så:
            if ($kredsListe[$kredsNr]['alder'] != $alder) {
                if ($kredsNr > 0){echo '</td>';}
                echo "<td style='padding: 1em; vertical-align:top;'><b>" . $kredsListe[$kredsNr]['alder'] . "</b><br />";
            }
            echo "<a href=" . "?m=s" . $type . "&o=" . $kredsListe[$kredsNr]['kreds'] . "&k=" . $kredsListe[$kredsNr]['dbukredsnr'] . ">";
            
            //Hvis der er indtastet noget i label-feltet, så vis det - ellers vis bare kredsnummeret
            if (isset($kredsListe[$kredsNr]['Label'])) {
                echo "kreds " . $kredsListe[$kredsNr]['Label'] . "</a><br/>";
            } else {
                echo "kreds " . $kredsListe[$kredsNr]['kreds'] . "</a><br/>";
            }
            $alder = $kredsListe[$kredsNr]['alder'];
        }
    }
    //slut sidste linie og tabellen
    echo "</td></tr></table>";
}

} //listTurnering

function showKreds() {
    // OBS: Disse SKAL matche DBU-sourcen 100% !
    $dbuKampeURL    = "http://www.dbujylland.dk/turneringer_og_resultater/resultatsoegning/programComplete.aspx?poolid=";
    $startStringInKampeSource = '<div id="div2"></div>';
    $endStringInKampeSource = '</table>';

    $dbuStillingURL = "http://www.dbujylland.dk/turneringer_og_resultater/resultatsoegning/position.aspx?poolid=";
    $startStringInStillingSource = '<div id="div2"></div>';
    $endStringInStillingSource = '</table>';

    // hent parametre for at finde ud af hvad der skal vises
    $type = substr($_GET['m'], 1, 1);
    $oobKreds = $_GET['o']; // OOB kredsnr
    $dbuKreds = $_GET['k']; // DBU kreds id

    if ($type == 'k') { // hent kampene
        echo '<h3>Spilleplan for kreds ' . $oobKreds . '</h3><br>';
        $str = file_get_contents($dbuKampeURL . $dbuKreds);
        // Find start og slut på tabellen
        $startPos = strpos($str, $startStringInKampeSource) + strlen($startStringInKampeSource);
        $endPos = strpos($str, $endStringInKampeSource, $startPos) + strlen($endStringInKampeSource);
        $strResult = substr($str, $startPos, $endPos - $startPos);
        $strResult = str_replace('Bestil sms på denne kamp', '', $strResult);
        // omdøber linket til kampnummeret
        $strResult = str_replace('matchInfo.aspx?', 'index.php/component/turnering?k=' . $oobKreds . '&', $strResult);

        //Vis knap med link til spilleplanen efter stillingen
        $strResult = $strResult . '<p><a href="index.php/component/turnering?m=ss&o=' . $oobKreds . '&k=' . $dbuKreds . '" class="button">Se stillingen for kreds ' . $oobKreds . '</a>';

        } else { // hent stillingen
        
        echo '<h3>Stillingen for kreds ' . $oobKreds . '</h3><br>';
        $str = file_get_contents($dbuStillingURL . $dbuKreds);

        // Find start og slut på tabellen
        $startPos = strpos($str, $startStringInStillingSource) + strlen($startStringInStillingSource);
        $endPos = strpos($str, $endStringInStillingSource, $startPos) + strlen($endStringInStillingSource);
        $strResult = substr($str, $startPos, $endPos - $startPos);

        // nulstiller klasser på kolonner, så de kan vises
        $strResult = str_replace('"c01', '"', $strResult);
        $strResult = str_replace('"c02', '"', $strResult);
        $strResult = str_replace('c07', 'c06', $strResult);

        // Flytter score-kolonnen (sletter og indsætter)
        //$strResult = str_replace('<th class="c09" colspan="3">Score</th>', '', $strResult);
        $strResult = str_replace('<th class="c12">P</th>', '<th class="c12" colspan="2">Score</th><th class="c12">P</th>', $strResult);
        
        // omdøber linket til holdets kampe
        $strResult = str_replace('programTeam.aspx?', 'index.php/component/turnering?k=' . $oobKreds . '&', $strResult);
        
        //Vis knap med link til spilleplanen efter stillingen
        $strResult = $strResult . '<p><a href="index.php/component/turnering?m=sk&o=' . $oobKreds . '&k=' . $dbuKreds . '" class="button">Se spilleplanen for kreds ' . $oobKreds . '</a>';
    }
    
    // vis resultatet
    echo $strResult;
}

function showKampInfo() {
    echo '<h2>Viser kampinfo for kamp nr ' . $matchId . '</h2>';

}
?>

