<?php
/**
 */
class rex_cronjob_dsgvo_sync extends rex_cronjob
{

    
    public function execute()
    {


        $url = $this->getParam('url') . "&version=" . $this->getParam('version') . "&keywords=" . $this->getParam('fields') . "&lang_codes=" . $this->getParam('lang_codes') . "&domains=" . $this->getParam('domains');
        $curl = curl_init();
        curl_setopt_array($curl,array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true));
        $resp = curl_exec($curl);
        dump($url);

        if (!curl_errno($curl)) { 
            $keys = json_decode($resp, true);

            foreach($keys as $key) {
                dump($key);
                $insert_query = '
                INSERT INTO rex_dsgvo_client
                    (`keyword`, name, text, source, source_url, status, lang)
                VALUES
                    (:keyword, :name, :text, :source, :source_url, :status, :lang)';

                $values = [];
                $values[':keyword']     = $key['keyword'];
                $values[':name']        = $key['name'];
                $values[':text']        = $key['text'];
                $values[':source']      = $key['source'];
                $values[':source_url']  = $key['source_url'];
                $values[':status']      = $key['status'];
                $values[':lang']        = $key['lang'];
    
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
        
        $fields = [
            [
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_url_label'),
                'name' => 'url',
                'type' => 'text',
                'default' => $default_url,
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_url_notice'),
            ],
            [
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_version_label'),
                'name' => 'version',
                'type' => 'select',
                'options' => ["beta" => "beta"],
                'default' => "beta",
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_version_notice'),
            ],
            [
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_lang_codes_label'),
                'name' => 'lang_codes',
                'type' => 'text',
                'default' => "de",
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_lang_codes_notice'),
            ],
            [
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_domains_label'),
                'name' => 'domains',
                'type' => 'select',
                'options' => [0 => "Bitte wählen", "dsgvo.pixelfirma.de" => "dsgvo.pixelfirma.de"],
                'default' => "0",
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_domains_notice'),
            ],
            [
                'label' => rex_i18n::msg('dsgvo_privacy_cronjob_fields_label'),
                'name' => 'fields',
                'type' => 'text',
                'default' => "facebook,cookies,kontakt",
                'notice' => rex_i18n::msg('dsgvo_privacy_cronjob_fields_notice'),
            ],
        ];

        return $fields;
    }
}