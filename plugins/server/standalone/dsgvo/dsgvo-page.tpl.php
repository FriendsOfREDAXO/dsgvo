<div class="dsgvo-privacy dsgvp-privacy-standalone">
    <?php foreach ($dsgvo_pool as $dsgvo_item) { ?>
    <div class="dsgvo-item" data-dsgvo="<?= $dsgvo_item->keyword ?>">
    <div class="dsgvo-item-title">
        <h2>
        <?php if ($dsgvo_item->source_url) { ?>
                <a class="dsgvo-item-source_url" href="<?= $dsgvo_item->source_url ?>" title="<?= $dsgvo_item->source ?>">
                <?= $this->source ?> <?= $dsgvo_item->source ?>
                </a>
        <?php } ?>
        <?= $dsgvo_item->name ?>
</h1>
        </div>
    <div class="dsgvo-item-body">
        <?php if ( !$dsgvo_item->custom_text ) { ?>
            <p class="dsgvo-item-text"><?= html_entity_decode($dsgvo_item->text) ?></p>
        <?php } else { ?>
            <p class="dsgvo-item-custom_text"><?= $dsgvo_item->custom_text ?></p>
        <?php } ?>
        </div>
        <?php if($_COOKIE["dsgvo_".$dsgvo_item->keyword] != -1) {
            $display[-1] = "block";
            $display[1] = "none";
        } else {
            $display[-1] = "none";
            $display[1] = "block";
        }
        ?>
        <?php if ($dsgvo_item->code) { ?>
        <div class="uk-card-footer">
            <button style="display: <?= $display[-1]; ?>" data-dsgvo-keyword="<?= $dsgvo_item->keyword ?>" onClick="dsgvoCookie('<?= $dsgvo_item->keyword ?>', -1)"><?= $this->revoke ?></button>
            <button style="display: <?= $display[1]; ?>" data-dsgvo-keyword="<?= $dsgvo_item->keyword ?>" onClick="dsgvoCookie('<?= $dsgvo_item->keyword ?>', 1)"><?= $this->consent ?></button>
        </div>
        <?php } ?>
    </div>
    <?php
        }
    ?>
</div>
<script>
DsgvoCookies = Cookies.noConflict();

    function dsgvoCookie(keyword, status) {
        var elem = document.querySelectorAll('[data-dsgvo-keyword="'+keyword+'"]');
        if(status == 1) {
            DsgvoCookies.set("dsgvo_"+keyword, 1, { expires: 365 });
            elem[0].style.display = 'block';
            elem[1].style.display = 'none';
        } else {
            DsgvoCookies.set("dsgvo_"+keyword, -1, { expires: 365 });
            elem[0].style.display = 'none';
            elem[1].style.display = 'block';
        }
    }
</script>