<?php // dsgvo_module_output - Diese Zeile nicht löschen ?>
<section class="modul modul-privacy">
<?
    // Für mehrere YRewrite-Domains ggf. weiter eingrenzen mit `WHERE domain = "domain.de"`
    $lang = rex_clang::getCurrent()->getCode();
    $dsgvo_pool = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE status = 1 AND lang = :lang ORDER by prio',[':lang'=>$lang]);

    // Eindeutschen
    $output->setVar("consent", "Einwilligen");
    $output->setVar("revoke", "Widerrufen");
    $output->setVar("source", "Quelle:");
    $output->parse('dsgvo-page.fragment.inc.php');
?>
</section>
