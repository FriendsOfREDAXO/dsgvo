<?php

class rex_api_dsgvo extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        ob_end_clean();
        $keys = rex_request('keywords','string', "");
        $api_key = rex_request('api_key','string', "");
        $domains = rex_request('domains','string', "default");
        header('Content-Type: application/json; charset=UTF-8');
        if($keys) {
            $dsgvo_items = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE FIND_IN_SET(`keyword`, :keywords)', [":keywords" => $keys]);
        } else if($domains) {
            $dsgvo_items = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE FIND_IN_SET(`domain`, :domains)', [":domains" => $domains]);
        }
/*        foreach($dsgvo_items AS &$dsgvo_item) {
            $dsgvo_item['text'] = markitup::parseOutput ('textile', $dsgvo_item['text']);
            unset($dsgvo_item);
        } */
        echo json_encode($dsgvo_items);
        exit();

    }

}
?>