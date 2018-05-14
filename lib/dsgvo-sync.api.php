<?php

// Aufruf: 
// /?rex-api-call=store_status&param=close

class rex_api_dsgvo_sync extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        ob_end_clean();
        $api_key = rex_request('api_key','string');
        

        if($api_key == ""); 
    }
}

?>