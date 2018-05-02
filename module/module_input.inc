<?php // dsgvo_module_input - Diese Zeile nicht löschen 

if(rex_addon::get('mform')->isAvailable()) {
    $mform = new MForm();

    if(rex_addon::get('yrewrite')->isAvailable()) {
        $domains = [];
        foreach(rex_yrewrite::getDomains(true) as $domain => $object) {
            $domains[$domain] = $domain;
        } 

        $mform->addSelectField("1", $domains, array('label'=>'Domain')); // use string for x.0 json values
    } else {
        $mform->addSelectField("1", [rex::getServer()], array('label'=>'Domain')); // use string for x.0 json values
    }

    $langs = [];

    foreach(rex_clang::getAll(true) as $lang) {
        $langs[$lang->getCode()] = $lang->getCode();
    }
    $mform->addSelectField("2", $langs, array('label'=>'Sprachen')); // use string for x.0 json values
    echo $mform->show();
} else {
    // Input-Felder ohne MForm. TODO   
}
?>

