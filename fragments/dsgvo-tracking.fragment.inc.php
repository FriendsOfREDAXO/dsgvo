<script language="javascript" type="text/javascript" src="/assets/addons/dsgvo/js/cookie.js"></script>
<!-- dsgvo-tracking-fragment -->
    <?php foreach ($this->dsgvo_pool as $dsgvo_item) { 
    
    if($dsgvo_item["code"] && rex_request::cookie("dsgvo_".$dsgvo_item['keyword']) != -1) {

        echo $dsgvo_item["code"];

    }

} ?>
<!-- / dsgvo-tracking-fragment -->