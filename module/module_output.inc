<?php // dsgvo_module_output - Diese Zeile nicht löschen ?>
<section class="modul modul-privacy">
<?php
    // Für mehrere YRewrite-Domains ggf. weiter eingrenzen mit `WHERE domain = "domain.de"`
    $lang = rex_clang::getCurrent()->getCode();
    $dsgvo_pool = array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE status = 1 AND domain = :domain AND lang = :lang ORDER by prio',[':domain'=> "REX_VALUE[1]", ':lang'=> "REX_VALUE[2]"]));

foreach($dsgvo_pool AS $key => $dsgvo_item) {
    $dsgvo_pool[$key]['text'] = markitup::parseOutput ('textile', $dsgvo_item['text']);
}

    $output = new rex_fragment();
    // ggf. Sprache anpassen
    $output->setVar("dsgvo_pool", $dsgvo_pool);
    $output->setVar("lang", $lang);
    $output->setVar("domain", $domain);
    $output->setVar("consent", "Einwilligen");
    $output->setVar("revoke", "Widerrufen");
    $output->setVar("source", "Quelle:");
    echo $output->parse('dsgvo-page.fragment.inc.php');
?>
</section>
