<?php
/**
 */
class rex_cronjob_dsgvo_sync extends rex_cronjob
{

    
    public function execute()
    {


        $url = $this->getParam('url') . 
        "?api_key=" . $this->getParam('api_key') . 
        "&domains=" . $this->getParam('domains') .
        "&version=" . rex_addon::get('dsgvo')->getProperty('version')) .
        "&rex_version=" . rex::getVersion();

        dump($url);
        $curl = curl_init();
        curl_setopt_array($curl,array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true));
        $resp = curl_exec($curl);

        if (!curl_errno($curl)) { 
            $keys = json_decode($resp, true);

            foreach($keys as $key) {
                dump($key);
                $insert_query = '
                INSERT INTO rex_dsgvo_client
                    (`category`, `keyword`, name, text, source, source_url, status, lang)
                VALUES
                    (:category, :keyword, :name, :text, :source, :source_url, :status, :lang)';

                $values = [];
                $values[':category']    = $key['category'];
                $values[':keyword']     = $key['keyword'];
                $values[':lang']        = $key['lang'];
                $values[':name']        = $key['name'];
                $values[':text']        = $key['text'];
                $values[':source']      = $key['source'];
                $values[':source_url']  = $key['source_url'];
                $values[':status']      = $key['status'];
    
                rex_sql::factory()->setDebug(0)->setQuery($insert_query, $values);
            }
            $this->setMessage("DSGVO: Datenschutz-Texte abgeglichen.");

        } else {
            $this->setMessage("Fehler beim Aufruf des DSGVO: Texte-Servers");
        }

        return true;

    }

    public function getTypeName()
    {
        return rex_i18n::msg('dsgvo_privacy_cronjob_name');
    }

    public function getParamFields()
    {
        $default_url = 'http://dsgvo.pixelfirma.de/?rex-api-call=dsgvo';
        

        $domains = [];
        $domains[0] => "Bitte wählen";
        if(rex_addon::get('yrewrite')->isAvailable()) {
            foreach(rex_yrewrite::getDomains(true) as $domain => $object) {
                $domains[$domain] = $domain;
            } 
        } else {
                $domains[rex::getServer()] = rex::getServer();
        }

        $fields = [
            [
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_url_label'),
                'name' => 'url',
                'type' => 'text',
                'default' => $default_url,
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_url_notice'),
            ],
            [
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_domains_label'),
                'name' => 'domains',
                'type' => 'select',
                'options' => $domains,
                'default' => "0",
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_domains_notice'),
            ],
            [
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_api_key_label'),
                'name' => 'api_key',
                'type' => 'text',
                'default' => "",
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_api_key_notice'),
            ]
        ];

        return $fields;
    }
}