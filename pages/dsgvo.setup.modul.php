<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {
    
    // Schritt 2



    $content = '';
    $searchtext = 'dsgvo_module_output';

    $gm = rex_sql::factory();
    $gm->setQuery('select * from '.rex::getTable('module').' where output LIKE "%' . $searchtext . '%"');

    $module_id = 0;
    $module_name = '';
    foreach ($gm->getArray() as $module) {
        $module_id = $module['id'];
        $module_name = $module['name'];
    }

    $dsgvo_module_name = 'Datenschutz-Erklärung';

    if (rex_request('install', 'integer') == 1) {
        $input = rex_file::get(rex_path::addon('dsgvo', 'module/module_input.inc'));
        $output = rex_file::get(rex_path::addon('dsgvo', 'module/module_output.inc'));

        $mi = rex_sql::factory();
        // $mi->debugsql = 1;
        $mi->setTable(rex::getTable("module"));
        $mi->setValue('input', $input);
        $mi->setValue('output', $output);

        if ($module_id == rex_request('module_id', 'integer', -1)) {
            $mi->setWhere('id="' . $module_id . '"');
            $mi->update();
            echo rex_view::success('Modul "' . $module_name . '" wurde aktualisiert');
        } else {
            $mi->setValue('name', $dsgvo_module_name);
            $mi->insert();
            $module_id = (int) $mi->getLastId();
            $module_name = $dsgvo_module_name;
            echo rex_view::success('DSGVO-Modul wurde angelegt unter "' . $dsgvo_module_name . '"');
        }
    }

    $content .= '<p>'.$this->i18n('install_dsgvo_modul_description').'</p>';

    if ($module_id > 0) {
        $class = " panel-success";
        $content .= '<p><a class="btn btn-primary" href="index.php?page=dsgvo/setup/modul&amp;install=1&amp;module_id=' . $module_id . '" class="rex-button">' . $this->i18n('install_update_dsgvo_module', htmlspecialchars($module_name)) . '</a></p>';
    } else {
        $class = " panel-danger";
        $content .= '<p><a class="btn btn-primary" href="index.php?page=dsgvo/setup/modul&amp;install=1" class="rex-button">' . $this->i18n('install_dsgvo_modul', $dsgvo_module_name) . '</a></p>';
    }


    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('install_dsgvo_modul'), false);
    $fragment->setVar('body', $content, false);
    $fragment->setVar('class', $class, false);
    echo $fragment->parse('core/page/section.php');
  
}