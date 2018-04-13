<div class="uk-width-xxlarge uk-align-center">
    <?php foreach ($this->dsgvo_pool as $dsgvo_item) { ?>
    <div class="uk-section uk-section-default" data-dsgvo="<?= $dsgvo_item['keyword'] ?>">
        <h1 class="dsgvo-item-title"><?= $dsgvo_item['name'] ?></h1>
        <? if ( !$dsgvo_item['custom_text'] ) { ?>
            <p class="dsgvo-item-text"><?= $dsgvo_item['text'] ?></p>
        <? } else { ?>
            <p class="dsgvo-item-custom_text"><?= $dsgvo_item['custom_text'] ?></p>
        <? } ?>
        <? if ($dsgvo_item['source_url']) { ?>
            <p class="dsgvo-item-source">
                <a class="dsgvo-item-source_url" href="<?= $dsgvo_item['source_url'] ?>" title="<?= $dsgvo_item['source'] ?>">
                <?= $dsgvo_item['source'] ?>
                </a>
            </p>
        <?php } ?>
        
        <? if ($dsgvo_item['code'] && rex_request::cookie("dsgvo_".$dsgvo_item['keyword']) != -1) { ?>
            <button class="uk-button uk-button-default" onClick="DsgvoCookie('dsgvo_<?= $dsgvo_item['keyword'] ?>', -1)">Deaktivieren</button>
        <? } else if ($dsgvo_item['code'] && rex_request::cookie("dsgvo_".$dsgvo_item['keyword']) == -1) { ?>
            <button class="uk-button uk-button-default" onClick="DsgvoCookie('dsgvo_<?= $dsgvo_item['keyword'] ?>', 1)">Aktivieren</button>
        <?php } ?>
        <script>
            function DsgvoCookie(keyword, status) {
                if(Cookies.get("dsgvo_<?= $dsgvo_item['keyword'] ?>") == 1) {
                    Cookie.set("dsgvo_<?= $dsgvo_item['keyword'] ?>", '-1', { expires: 30 });
                } else  {
                    Cookie.set("dsgvo_<?= $dsgvo_item['keyword'] ?>", '1', { expires: 30 });
                }
            }
        </script>
    </div>
    <?
        }
    ?>
</div>