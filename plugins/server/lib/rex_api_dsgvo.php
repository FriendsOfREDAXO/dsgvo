<?php

class rex_api_dsgvo extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        ob_end_clean();
        $keys = rex_request('keywords','string');
        $api_key = rex_request('api_key','string');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_yf_dsgvo WHERE FIND_IN_SET(`keyword`, :keywords)', [":keywords" => $keys]));
            exit();

    }

}
?>