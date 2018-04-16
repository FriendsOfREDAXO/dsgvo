<div class="uk-width-xxlarge uk-align-center">
    <?php foreach ($this->dsgvo_pool as $dsgvo_item) { ?>
    <div class="uk-card uk-card-default uk-card-small uk-margin-top" data-dsgvo="<?= $dsgvo_item['keyword'] ?>">
    <div class="uk-card-header uk-background-default">
        <h1 class="dsgvo-item-title uk-card-title">
        <? if ($dsgvo_item['source_url']) { ?>
                <a class="dsgvo-item-source_url uk-align-right uk-margin-remove uk-button uk-button-default uk-button-small" href="<?= $dsgvo_item['source_url'] ?>" title="<?= $dsgvo_item['source'] ?>">
                <?= $this->source ?> <?= $dsgvo_item['source'] ?>
                </a>
        <?php } ?>
        <?= $dsgvo_item['name'] ?>
</h1>
        </div>
    <div class="uk-card-body">
        <? if ( !$dsgvo_item['custom_text'] ) { ?>
            <p class="dsgvo-item-text"><?= html_entity_decode($dsgvo_item['text']) ?></p>
        <? } else { ?>
            <p class="dsgvo-item-custom_text"><?= $dsgvo_item['custom_text'] ?></p>
        <? } ?>
        </div>
        <? if($_COOKIE["dsgvo_".$dsgvo_item['keyword']] != -1) {
            $display[-1] = "block";
            $display[1] = "none";
        } else {
            $display[-1] = "none";
            $display[1] = "block";
        }
        ?>
        <? if ($dsgvo_item['code']) { ?>
        <div class="uk-card-footer">
            <button class="uk-button uk-button-default" style="display: <?= $display[-1]; ?>" data-dsgvo-keyword="<?= $dsgvo_item['keyword'] ?>" onClick="dsgvoCookie('<?= $dsgvo_item['keyword'] ?>', -1)"><?= $this->revoke ?></button>
            <button class="uk-button uk-button-default"  style="display: <?= $display[1]; ?>" data-dsgvo-keyword="<?= $dsgvo_item['keyword'] ?>" onClick="dsgvoCookie('<?= $dsgvo_item['keyword'] ?>', 1)"><?= $this->consent ?></button>
        </div>
        <?php } ?>
    </div>
    <?
        }
    ?>
</div>
<script>
    function dsgvoCookie(keyword, status) {
        var elem = document.querySelectorAll('[data-dsgvo-keyword="'+keyword+'"]');
        if(status == 1) {
            Cookies.set("dsgvo_"+keyword, 1, { expires: 365 });
            elem[0].style.display = 'block';
            elem[1].style.display = 'none';
        } else {
            Cookies.set("dsgvo_"+keyword, -1, { expires: 365 });
            elem[0].style.display = 'none';
            elem[1].style.display = 'block';
        }
    }
</script>