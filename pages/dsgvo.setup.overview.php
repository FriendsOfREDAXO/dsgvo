<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {

    $content = rex_i18n::rawMsg('dsgvo_description_all', false);
    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_description_all_title'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

    $tracking_content = 
'<pre class="pre-scrollable">'.
htmlentities('
<!-- DSGVO Tracking und Cookie -->
<?php
$domain = ""; // Hier Domain eintragen
$lang = rex_clang::getCurrent()->getCode();
$dsgvo_pool = rex_sql::factory()->setDebug(0)->getArray("SELECT * FROM rex_dsgvo_client WHERE status = 1 AND lang = :lang AND domain = :domain ORDER by prio",[":lang"=>$lang,":domain"=>$domain]);

$output = new rex_fragment();
$output->setVar("dsgvo_pool", $dsgvo_pool);
$output->setVar("lang", $lang);
$output->setVar("domain", $domain);

echo html_entity_decode($output->parse("dsgvo-tracking.fragment.inc.php"), ENT_HTML5 | ENT_QUOTES);

?>
<!-- / DSGVO Tracking und Cookie -->
').
'</pre>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_setup_overview_template_tracking'), false);
    $fragment->setVar('body', $tracking_content, false);
    echo $fragment->parse('core/page/section.php');

    
    $consent_content = 
'<pre class="pre-scrollable">'.
htmlentities('
<!-- DSGVO Cookie-Einverständnis -->
<?php
// DSGVO Consent-HTML + JS
$output = new rex_fragment();
    
$output->setVar("info", "Um unsere Webseite für Sie optimal zu gestalten und fortlaufend verbessern zu können, verwenden wir Cookies. Durch die weitere Nutzung der Webseite stimmen Sie der Verwendung von Cookies zu. Weitere Informationen zu Cookies erhalten Sie in unserer");
$output->setVar("learn_more", "Datenschutzerklärung");
$output->setVar("dismiss", "OK");
$output->setVar("url", "/datenschutz/");
$output->setVar("html_padding", "Bottom");
echo $output->parse("dsgvo-consent.fragment.inc.php");

?>
<!-- / DSGVO Cookie-Einverständnis -->
').
'</pre>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_setup_overview_template_consent'), false);
    $fragment->setVar('body', $consent_content, false);
    echo $fragment->parse('core/page/section.php');


    $content = rex_i18n::rawMsg('dsgvo_description_ad', false);
    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_description_ad_title'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
        
}

