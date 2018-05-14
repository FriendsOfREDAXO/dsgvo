<?php

class rex_cronjob_dsgvo_sync2 extends rex_cronjob
{

    public function execute()
    {

        $domains = rex_sql::factory()->setDebug(0)->getArray("SELECT * FROM rex_dsgvo_project"); 

        foreach($domains as $domain) {
        $curl = curl_init();
        $url = $domain['domain']."/?rex-api-call=dsgvo_sync&api_key=".$domain['api_key'];
        curl_setopt_array($curl,array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true));
        $resp = curl_exec($curl);

            if (!curl_errno($curl)) { 

                if(($resp[0] == "{") && (json_last_error() === JSON_ERROR_NONE)) {
                    rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO rex_dsgvo_server_log (`domain`, `status`, `createdate`, `rex_version`, `raw`) VALUES(?,?,NOW(),?,?)', [$domain['domain'], 1, "x", $resp] );
                } else {
                    rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO rex_dsgvo_server_log (`domain`, `status`, `createdate`, `rex_version`, `raw`) VALUES(?,?,NOW(),?,?)', [$domain['domain'], 0, "", $resp] );
                }
            }

        }

        return true;

    }
    public function getTypeName()
    {
        return rex_i18n::msg('rex_cronjob_dsgvo_sync2_name');
    }

    public function getParamFields()
    {
        return [];
    }
}
?>