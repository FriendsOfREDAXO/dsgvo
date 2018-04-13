<?php
/**
 */
class rex_cronjob_dsgvo_phpmailerlogs extends rex_cronjob
{

    
    public function execute()
    {

        $ext = '.html';

        if ($this->delete_interval) {
            $files = glob(rex_path::addonData('phpmailer', '*'.$ext));
            $backups = [];
            $limit = strtotime('-1 month'); // Generelle Vorhaltezeit: 1 Monat

            foreach ($files as $file) {
                $timestamp = filectime($file);

                if ($timestamp > $limit) {
                    // wenn es die generelle Vorhaltezeit unterschreitet
                    continue;
                }

                $backups[$file] = $timestamp;
            }

            asort($backups, SORT_NUMERIC);

            $step = '';
            $countDeleted = 0;

            foreach ($backups as $backup => $timestamp) {
                $stepLast = $step;
                $step = date($this->delete_interval, (int) $timestamp);

                if ($stepLast !== $step) {
                    // wenn es zu diesem Interval schon ein Backup gibt
                    continue;
                }

                // dann löschen
                rex_file::delete($backup);
                ++$countDeleted;
            }

            if ($countDeleted) {
                $message .= ', '.$countDeleted.' old backups deleted';
            }
        }

    }

    public function getTypeName()
    {
        return rex_i18n::msg('dsgvo_phpmailerlogs_cronjob_name');
    }

    public function getParamFields()
    {

        $fields[] = [
            'label' => rex_i18n::msg('dsgvo_phpmailerlogs_delete_interval'),
            'name' => 'delete_interval',
            'type' => 'select',
            'options' => [
                'YW' => rex_i18n::msg('dsgvo_phpmailerlogs_delete_interval_weekly'),
                'YM' => rex_i18n::msg('dsgvo_phpmailerlogs_delete_interval_monthly'), ],
            'default' => 'YW',
            'notice' => rex_i18n::msg('dsgvo_phpmailerlogs_delete_interval_notice'),
        ];

        return $fields;
    }
}