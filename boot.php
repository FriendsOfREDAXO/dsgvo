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

if (rex::getUser() && rex::getUser()->isAdmin()) {
    $fortune[] = "„Der Mensch ist dazu geboren, großes zu leisten, wenn er versteht, die DSGVO umzusetzen.“";
    $fortune[] = "„Die beste Möglichkeit, Träume zu verwirklichen, ist datenschutzkonformes Handeln.“";
    $fortune[] = "„Hilfe!!! Ich werde in einer deutschen Abmahn-Kanzlei gefangen gehalten.“";
    $fortune[] = "„404 – fortune not found.“";
    $fortune[] = "„Cookie's Fortune Cookie Fortunes with Cookie \"Fortune Cookie\" Masterson.“";
    $fortune[] = "„Für den Optimisten ist das Leben kein Problem. Für den Datenschutzbeauftragten schon.“";
    $fortune[] = "„Man verliert die meiste Zeit damit, dass man eine rechtssichere Website umsetzen will.“";
    setcookie("dsgvo_fortune", rex_string::normalize($fortune[array_rand($fortune)]));
}