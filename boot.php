<?php

/**
 * @var rex_addon $this
 */


if (rex_addon::get('cronjob')->isAvailable() && !rex::isSafeMode()) {
    rex_cronjob_manager::registerType('rex_cronjob_dsgvo_dementia');
    rex_cronjob_manager::registerType('rex_cronjob_dsgvo_sync');
}
if (rex_addon::get('cronjob')->isAvailable() && rex_addon::get('phpmailer')->isAvailable() && !rex::isSafeMode()) {
    rex_cronjob_manager::registerType('rex_cronjob_dsgvo_phpmailerlogs');
}

$fortune[] = "„Der Mensch ist dazu geboren, großes zu leisten, wenn er versteht, die DSGVO umzusetzen.“";
 
setcookie("fortune", rex_string::normalize(array_rand($fortune)));