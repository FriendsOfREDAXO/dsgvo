<?php

class rex_api_dsgvo extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        ob_end_clean();
        $version = rex_request('version','string', 0); // Version der API
        $rex_version = rex_request('rex_version','string', 0); // Version der REDAXO-Installation
        $domains = rex_request('domains','string', ""); // Domain(s), kommasepariert
        $langs = rex_request('langs','string', ""); // Sprache(n) als ISO-Code, kommasepariert, optional 
        $keywords = rex_request('keywords','string', ""); // Schlüssel der einzelnen Dienste, kommasepariert, optional
        $api_key = rex_request('api_key','string', ""); // API-Key zu EINER Domain, optional
        $html = rex_request('html','int', 0); // Weitergabe der Datenschutz-Erklärung als Textile oder als HTML-Code

        // Initial 1.0 Version der API - Sollten weitere Features hinzukommen, muss eine weitere Version angefragt werden.
        if($version <= 1) {

            $params = [];
            if($domains) {
                $where_query .= " AND FIND_IN_SET(s.domain, :domains)";
                $params[":domains"] = $domains;
            } else {
                $where_query .= ' AND s.domain = "default"';
            }

            if($langs) {
                $where_query .= " AND FIND_IN_SET(lang, :langs)";
                $params[":langs"] = $langs;
            }

            if($keywords) {
                $where_query .= " AND FIND_IN_SET(keyword, :keywords)";
                $params[":keywords"] = $keywords;
            } 

            if($api_key) {
                $where_query .= " AND `api_key` = :api_key";
                $params[":api_key"] = $api_key;
            } else {
                $where_query .= ' AND (`api_key` = "" OR `api_key` IS NULL)';
            }

            $dsgvo_items = array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_server s LEFT JOIN rex_dsgvo_server_project p on s.domain = p.domain WHERE status = 1 '.$where_query.' ORDER BY prio', $params));
        
            if($html) {
                $dsgvo_items = $this->Textile2HTML($dsgvo_items);
            }
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($dsgvo_items);

        // LOG

        $raw = ["version" => $version, "domains" => $domains, "langs" => $langs, "keywords" => $keywords, "rex_version" => $rex_version];
        rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO rex_dsgvo_server_log (`domain`, `status`, `createdate`, `rex_version`, `raw`) VALUES(?,?,?,?,?)', [$domains, 1, date('Y-m-d G:i:s'), $rex_version, json_encode($raw)] );

        exit();

    }

    public function Textile2HTML($dsgvo_items) {

        foreach($dsgvo_items AS &$dsgvo_item) {
            $dsgvo_item['text'] = markitup::parseOutput ('textile', $dsgvo_item['text']);
            unset($dsgvo_item);
        }
        return $dsgvo_items;
    }
}
?>