<?php // dsgvo_module_input - Diese Zeile nicht löschen 

if(rex_addon::get('mform')->isAvailable()) {
    $mform = new MForm();

    if(rex_addon::get('yrewrite')->isAvailable()) {
        $domains = [];
        foreach(rex_yrewrite::getDomains(true) as $domain => $object) {
            preg_match('/([http]*[s]*:\/\/)*(www\.)*([a-zA-Z0-9\-\.]*)[\/]*/', $domain, $matches);
            $domains[$matches[3]] = $matches[3];
        } 

        $mform->addSelectField("1", $domains, array('label'=>'Domain'));
    } else {
        preg_match('/([http]*[s]*:\/\/)*(www\.)*([a-zA-Z0-9\-\.]*)[\/]*/', rex::getServer(), $matches);
        $mform->addSelectField("1", [$matches[3] => $matches[3]], array('label'=>'Domain')); 
    }

    $langs = [];

    foreach(rex_clang::getAll(true) as $lang) {
        $langs[$lang->getCode()] = $lang->getCode();
    }
    $mform->addSelectField("2", $langs, array('label'=>'Sprachen'));
    echo $mform->show();
} else {
    // Input-Felder ohne MForm. TODO   
}
?>

