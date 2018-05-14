<?php

/**
 * @var rex_addon $this
 */


if (rex_addon::get('cronjob')->isAvailable() && !rex::isSafeMode()) {
    rex_cronjob_manager::registerType('rex_cronjob_dsgvo_dementia');
    rex_cronjob_manager::registerType('rex_cronjob_dsgvo_privacy');
    rex_cronjob_manager::registerType('rex_cronjob_dsgvo_sync2');
}