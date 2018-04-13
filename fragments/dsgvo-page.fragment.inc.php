<div class="dsgvo-items">
    <?php foreach ($this->dsgvo_pool as $dsgvo_item) { ?>
    <div class="dsgvo-item">
        <h1 class="dsgvo-item-title"><?= $dsgvo_item['name'] ?></h1>
        <p class="dsgvo-item-text"><?= $dsgvo_item['text'] ?></p>
        <p class="dsgvo-item-custom_text"><?= $dsgvo_item['custom_text'] ?></p>
        <p class="dsgvo-item-source"><a class="dsgvo-item-source_url" href="<?= $dsgvo_item['source_url'] ?>" title="<?= $dsgvo_item['source'] ?>"><?= $dsgvo_item['source'] ?></a></p>
        <!-- OPTOUT-Code bei Bedarf einfügen -->
    </div>
    <?
        }
    ?>
</div>