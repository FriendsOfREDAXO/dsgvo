<?php

$params['rex-api-call'] = 'dsgvo';
$params['api_key']      = '';
$params['domains']      = 'example.com';
$params['version']      = 'standalone';
$params['subversion']   = '20180511';
$params['langs']        = 'de';
$params['html']        = '1';
$params['timestamp']    = time();

$url = 'http://dsgvo.example.com/?'.urldecode(http_build_query($params));

$filename = "dsgvo.json";

if($_GET['api_key'] == $params['api_key']) {
    if(getDsgvoFromServer() == "success") {
        $params['status'] = '1';
    } else {
        $params['status'] = '0';
    }
    echo json_encode($params);
}

function getDsgvoFromServer() {
    global $url, $filename;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Ausgabe verhindern
    $resp = curl_exec($curl);

    if (!curl_errno($curl)) { 
        file_put_contents(dirname(__FILE__)."/".$filename, $resp);
        return "success";
    } else {
        return curl_error($curl);
    }
    curl_close($curl);
}

function getLocalPool() {
    global $filename;
    return json_decode(file_get_contents(dirname(__FILE__)."/".$filename));
}

function getTrackingCodes($lang, $domain) { // Stellt Tracking Codes für den Header zusammen

    $output = new Template();
    $output->dsgvo_pool = getLocalPool();
    $output->lang = $lang;
    $output->domain = $domain;

    return $output->render('dsgvo-tracking.tpl.php');
}

function getDsgvoPage($lang, $domain) { // Stellt Texte für die DSGVO-Seite zusammen

    $output = new Template();
    $output->dsgvo_pool = getLocalPool();
    $output->lang = $lang;
    $output->domain = $domain;
    $output->consent = "Einwilligen";
    $output->revoke = "Widerrufen";
    $output->source = "Quelle:";

    return $output->render('dsgvo-page.tpl.php');

}
function getDsgvoConsent($consent) { // Stellt Texte für die DSGVO-Seite zusammen

    $output = new Template();
    $output->consent = $consent;

    return $output->render('dsgvo-consent.tpl.php');

}

// Mini Template-Engine, um Fragmente ähnlich zu REDAXO zu verwenden. Quelle:
// http://chadminick.com/articles/simple-php-template-engine.html#sthash.tbm78ED0.dpbs

class Template {
  private $vars  = array();

  public function __get($name) {
    return $this->vars[$name];
  }

  public function __set($name, $value) {
    if($name == 'view_template_file') {
      throw new Exception("Cannot bind variable named 'view_template_file'");
    }
    $this->vars[$name] = $value;
  }

  public function render($view_template_file) {
    if(array_key_exists('view_template_file', $this->vars)) {
      throw new Exception("Cannot bind variable called 'view_template_file'");
    }
    extract($this->vars);
    ob_start();
    include($view_template_file);
    return ob_get_clean();
  }
}
?>