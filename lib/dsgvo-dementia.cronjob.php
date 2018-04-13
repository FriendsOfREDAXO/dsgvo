<?php

/**
 * @package redaxo\dsgvo
 */
class rex_cronjob_dsgvo_dementia extends rex_cronjob
{

    
    public function execute()
    {
        
        rex_sql::factory()->query('DELETE FROM '.$this->getParam('rex_table').' WHERE '.(string)$this->getParam('field').' < MONTH(NOW() - INTERVAL '.(int)$this->getParam('interval').' MONTH)');

        $this->setMessage('Datensätze in der Tabelle '.$this->getParam('rex_table').' gelöscht, die älter als '.$this->getParam('interval').' Monate waren.');
        return true;

    }

    public function getTypeName()
    {
        return rex_i18n::msg('dsgvo_dementia_cronjob_name');
    }

    public function getParamFields()
    {

        // Eingabefelder des Cronjobs definieren
        $fields = [
            [
                 'label' => rex_i18n::msg('dsgvo_dementia_cronjob_rex_table_label'),
                 'name' => 'rex_table',
                 'type' => 'select',
                 'options' => array_column(rex_sql::factory()->getArray('SELECT TABLE_NAME as id, TABLE_NAME as name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE "rex_%"'), 'id', 'name'),
                 'notice' => rex_i18n::msg('dsgvo_dementia_cronjob_rex_table_notice'),
             ],
             [
                  'label' => rex_i18n::msg('dsgvo_dementia_cronjob_field_label'),
                  'name' => 'field',
                  'type' => 'text',
                  'default' => 'createdate',
                  'notice' => rex_i18n::msg('dsgvo_dementia_cronjob_field_notice'),
            ],
             [
                  'label' => rex_i18n::msg('dsgvo_dementia_cronjob_interval_label'),
                  'name' => 'interval',
                  'default' => '6',
                  'type' => 'text',
                  'notice' => rex_i18n::msg('dsgvo_dementia_cronjob_interval_notice'),
            ]
        ];

        return $fields;
    }
}