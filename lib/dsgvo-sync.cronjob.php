<?php
/**
 */
class rex_cronjob_dsgvo_sync extends rex_cronjob
{

    
    public function execute()
    {


        $url = $this->getParam('url') . 
        "&api_key=" . $this->getParam('api_key') . 
        "&domains=" . $this->getParam('domain') .
        "&version=" . rex_addon::get('dsgvo')->getProperty('version') .
        "&rex_version=" . rex::getVersion();

        $curl = curl_init();
        curl_setopt_array($curl,array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_FAILONERROR => true));
        $resp = curl_exec($curl);

        if (!curl_errno($curl)) { 
            rex_sql::factory()->setDebug(0)->setQuery("DELETE FROM rex_dsgvo_client WHERE domain = :domain", [":domain" => $this->getParam('domain')]);
            $keys = json_decode($resp, true);

            foreach($keys as $key) {
                $insert_query = '
                INSERT INTO rex_dsgvo_client
                    (`category`, domain, `keyword`, prio, name, text, code, source, source_url, status, lang, updatedate)
                VALUES
                    (:category, :domain, :keyword, :prio, :name, :text, :code, :source, :source_url, :status, :lang, NOW())';

                $values = [];
                $values[':category']    = $key['category'];
                $values[':domain']      = $key['domain'];
                $values[':keyword']     = $key['keyword'];
                $values[':prio']        = $key['prio'];
                $values[':lang']        = $key['lang'];
                $values[':name']        = $key['name'];
                $values[':text']        = $key['text'];
                $values[':code']        = $key['code'];
                $values[':source']      = $key['source'];
                $values[':source_url']  = $key['source_url'];
                $values[':status']      = $key['status'];
    
                rex_sql::factory()->setDebug(0)->setQuery($insert_query, $values);
            }
            $this->setMessage("DSGVO: Datenschutz-Texte abgeglichen.");
            return true;

        } else {
            $this->setMessage("Fehler beim Aufruf des DSGVO: Texte-Servers");
            return false;
        }

    }

    public function getTypeName()
    {
        return rex_i18n::msg('dsgvo_privacy_cronjob_name');
    }

    public function getParamFields()
    {
        $default_url = 'http://dsgvo.pixelfirma.de/?rex-api-call=dsgvo';
        
        $domains = [];
        if(rex_addon::get('yrewrite')->isAvailable()) {
            foreach(rex_yrewrite::getDomains(true) as $domain => $object) {
                preg_match('/([http]*[s]*:\/\/)*(www\.)*([a-zA-Z0-9\-\.]*)[\/]*/', $domain, $matches);
                $domains[$matches[3]] = $matches[3];
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
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_domain_label'),
                'name' => 'domain',
                'type' => 'select',
                'options' => $domains,
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_domain_notice'),
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