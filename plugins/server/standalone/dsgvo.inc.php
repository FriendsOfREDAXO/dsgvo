<?

$key = 'test';
$url = 'http://dsgvo.pixelfirma.de/?rex-api-call=dsgvo&version=standalone1&projects=standalone.pixelfirma.de&language=de&keywords=facebook_like,cookies,cdn,newsletter';
$filename = "dsgvo.json";

// später rauslöschen
$lang = "de";
$domain = "standalone.pixelfirma.de";

if($_GET['key'] == $key) {
    getDsgvoFromServer();
}

echo getTrackingCodes($lang, $domain);
echo getDsgvoPage($lang, $domain);
echo getDsgvoConsent($lang, $domain);

function getDsgvoFromServer() {
    global $url, $filename;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Ausgabe verhindern
    $resp = curl_exec($curl);

    if (!curl_errno($curl)) { 
        file_put_contents($filename, $resp);
        return true;
    } else {
        return curl_error($curl);
    }
    curl_close($curl);
}

function getLocalPool() {
    global $filename;
    return json_decode(file_get_contents($filename));
}

function getTrackingCodes($lang, $domain) { // Stellt Tracking Codes für den Header zusammen

    $dsgvo_pool = getLocalPool();

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

    return $output->render('dsgvo-page.tpl.php');

}
function getDsgvoConsent($lang, $domain) { // Stellt Texte für die DSGVO-Seite zusammen

    $output = new Template();
    $output->dsgvo_pool = getLocalPool();
    $output->lang = $lang;
    $output->domain = $domain;

    return $output->render('dsgvo-consent.tpl.php');

}

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