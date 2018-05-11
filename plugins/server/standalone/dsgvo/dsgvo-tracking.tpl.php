<script src="/dsgvo/cookie.js"></script>
<?php foreach ($dsgvo_pool as $dsgvo_item) { 

    if($dsgvo_item->code && $_COOKIE["dsgvo_".$dsgvo_item->keyword] != -1) {
        echo $dsgvo_item->code;
    }

} ?>
