<div class="dsgvo-items">
    <?php foreach ($dsgvo_pool as $dsgvo_item) { ?>
    <div class="dsgvo-item">
        <h1 class="dsgvo-item-title"><?= $dsgvo_item->name ?></h1>
        <p class="dsgvo-item-description"><?= $dsgvo_item->text ?></p>
        <!-- OPTOUT-Code bei Bedarf einfügen -->
    </div>
    <?
        }
    ?>
</div>