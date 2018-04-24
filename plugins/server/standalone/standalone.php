<?
require_once($_SERVER['DOCUMENT_ROOT']."dsgvo/dsgvo.inc.php");

echo getTrackingCodes("de", "lonex.de");

echo getDsgvoPage("de", "lonex.de");

$consent['info']       = "Diese Seite verwendet Cookies. Ganz ganz schlimm.";
$consent['more']       = "Datenschutz-Erklärung";
$consent['dismiss']    = "Ach du heilige Makrele";
$consent['url']        = "/datenschutzerklaerung/";

echo getDsgvoConsent($consent);


?>