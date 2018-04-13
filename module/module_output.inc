<?php // dsgvo_module_output - Diese Zeile nicht löschen ?>

<section class="modul modul-privacy">
    <?php
$lang = rex_clang::getCurrent()->getCode();
$dsgvos = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE status = 1 AND lang = :lang ORDER by Prio',[':lang'=>$lang]);
foreach($dsgvos as $dsgvo) {
    ?>
    <div class="dsgvo dsgvo-<?php echo '$dsgvo["keyword"]'; ?>">
        <div class="headline">
            <h2><?php echo $dsgvo["name"]; ?></h2>
        </div>
        <div class="content">
            <?php 
    if($dsgvo["custom_text"]) {
        echo markitup::parseOutput ('textile', $dsgvo["custom_text"]);
    } else {
        echo markitup::parseOutput ('textile', $dsgvo["text"]);
    } ?>
        </div>
        <div class="source">
            <a href="<?php echo $dsgvo['source_url']?>"><?php echo $dsgvo['source']?></a>
        </div>
        <?php if($dsgvo['code']) { ?>
        <div class="revocation">
            <p>Einwilligung zur Datenweitergabe.</p>
            <a href class="revoke-<?php echo '$dsgvo["keyword"]'; ?>">Widerrufen</a>
            <script>
                $('.revoke-<?php echo '$dsgvo["keyword"]'; ?>').click(function() {
                    Cookies.set('dsgvo-<?php echo '$dsgvo["keyword"]'; ?>', '1', { expires: 365 });
                })
            </script>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</section>
