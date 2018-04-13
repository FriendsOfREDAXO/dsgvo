<script>
<?php foreach ($this->dsgvo_pool as $dsgvo_item) { ?>
        console.log("<?= $dsgvo_item['name'] ?>");
<? } ?>

var google_code = "<?= $dsgvo["code"]; ?>"
if(Cookies.get('dsgvo-test') == 1) {
    console.log(0+google_code);
    Cookies.set('dsgvo-test', 0);
} else {
    console.log(1+google_code);
    Cookies.set('dsgvo-test', 1);
}

</script>