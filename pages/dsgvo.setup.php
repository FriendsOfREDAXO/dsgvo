<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {


    // Schritt 1

    $content = '';
    $searchtext = 'rex_cronjob_dsgvo_privacy';

    $gm = rex_sql::factory();
    $cronjobs = $gm->setQuery('select * from '.rex::getTable('cronjob').' where type = "rex_cronjob_dsgvo_privacy"')->getArray();

    $content .= '<p>'.$this->i18n('check_dsgvo_cronjob_description').'</p>';

    if (!count($cronjobs)) {
        $content .= '<p><a class="btn btn-primary" href="index.php?page=cronjob/cronjobs&amp;func=add&amp;list=cronjobs=1" class="rex-button">' . $this->i18n('dsgvo_add_cronjob', htmlspecialchars($module_name)) . '</a>';
    } else {
        $content .= '<p><a class="btn btn-primary" href="index.php?page=cronjob/cronjobs&amp;func=edit&amp;oid='.$cronjobs[0]['id'].'&amp;list=cronjobs" class="rex-button">' . $this->i18n('dsgvo_edit_cronjob', $dsgvo_module_name) . '</a>';
    }
    $content .= ' oder <a class="btn btn-default" href="index.php?page=yform/manager/data_edit&amp;table_name=rex_dsgvo_client" class="rex-button">' . $this->i18n('dsgvo_edit_text') . '</a></p>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_cronjob'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');


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
        $content .= '<p><a class="btn btn-primary" href="index.php?page=dsgvo/install&amp;install=1&amp;module_id=' . $module_id . '" class="rex-button">' . $this->i18n('install_update_dsgvo_module', htmlspecialchars($module_name)) . '</a></p>';
    } else {
        $content .= '<p><a class="btn btn-primary" href="index.php?page=dsgvo/install&amp;install=1" class="rex-button">' . $this->i18n('install_dsgvo_modul', $dsgvo_module_name) . '</a></p>';
    }


    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('install_dsgvo_modul'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
    
    // Schritt 3
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_dementia_cronjob_description').'</p>';


    $gm = rex_sql::factory();
    $tables = $gm->setQuery('SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE 
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE 
         TABLE_NAME LIKE "rex_%" AND 
         (COLUMN_NAME = "createdate" OR 
         COLUMN_NAME = "updatedate" OR 
         COLUMN_NAME = "datestamp" OR 
         COLUMN_NAME = "datetime" OR 
         COLUMN_NAME = "timestamp" OR 
         DATA_TYPE = "datetime") AND
         TABLE_NAME != "rex_action" AND
         TABLE_NAME != "rex_article" AND
         TABLE_NAME != "rex_article_slice" AND
         TABLE_NAME != "rex_cronjob" AND
         TABLE_NAME != "rex_module" AND
         TABLE_NAME != "rex_template" AND
         TABLE_NAME != "rex_user" AND
         TABLE_NAME != "rex_user_role"');

    foreach ($tables->getArray() as $table) {

        $sql = rex_sql::factory();
        $dementia_cronjob = array_shift($sql->setQuery('SELECT * FROM `rex_cronjob` WHERE `type` = "rex_cronjob_dsgvo_dementia" AND `parameters` LIKE "%'.$table['TABLE_NAME'].'%" LIMIT 1')->getArray());
         
        if($dementia_cronjob['id']) {
            $content .= '<div class="alert alert-success"><p>In der Tabelle <code>'.$table['TABLE_NAME']."</code> wurde ein <code>" . $table['DATA_TYPE']."</code>-Feld namens <code>" . $table['COLUMN_NAME'].'</code> gefunden. Für diese Tabelle gibt es einen DSGVO-Cronjob namens '.$dementia_cronjob['name'].'.</p></div>';

        } else {
            $content .= '<div class="alert alert-danger"><p>In der Tabelle <code>'.$table['TABLE_NAME']."</code> wurde ein <code>" . $table['DATA_TYPE']."</code>-Feld namens <code>" . $table['COLUMN_NAME'].'</code> gefunden. Für diese Tabelle gibt es keienn DSGVO-Cronjob.</p>';
            $content .= '<p><a class="btn btn-danger" href="index.php?page=yform/manager/data_edit&amp;table_name=rex_dsgvo_client" class="rex-button">' . $this->i18n('dsgvo_dementiasql_add_cronjob') . '</a></p></div>';
        }
    }

    $sql = rex_sql::factory();
    $tables = $sql->setQuery('SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE 
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE 
        TABLE_NAME LIKE "rex_%" AND
         (COLUMN_NAME = "email" OR 
         COLUMN_NAME = "e-mail" OR 
         COLUMN_NAME = "Email" OR 
         COLUMN_NAME LIKE "%mail%" OR 
         COLUMN_NAME = "lastname" OR 
         COLUMN_NAME = "prename" OR 
         COLUMN_NAME = "vorname" OR 
         COLUMN_NAME = "street" OR 
         COLUMN_NAME = "birthday" OR 
         COLUMN_NAME = "geburtstag") AND
         TABLE_NAME != "rex_article" AND
         TABLE_NAME != "rex_article_slice" AND
         TABLE_NAME != "rex_user" AND
         TABLE_NAME != "rex_user_role"
         GROUP BY table_name');

    foreach ($tables->getArray() as $table) {

        $sql = rex_sql::factory();
        $dementia_cronjob = array_shift($sql->setQuery('SELECT * FROM `rex_cronjob` WHERE `type` = "rex_cronjob_dsgvo_dementia" AND `parameters` LIKE "%'.$table['TABLE_NAME'].'%" LIMIT 1')->getArray());
         
        if($dementia_cronjob['id']) {
            $content .= '<div class="alert alert-success"><p>In der Tabelle <code>'.$table['TABLE_NAME']."</code> wurde ein Feld namens <code>" . $table['COLUMN_NAME'].'</code> gefunden. Für diese Tabelle gibt es einen DSGVO-Cronjob namens '.$dementia_cronjob['name'].'.</p></div>';

        } else {
            $content .= '<div class="alert alert-danger"><p>In der Tabelle <code>'.$table['TABLE_NAME']."</code> wurde ein Feld namens <code>" . $table['COLUMN_NAME'].'</code> gefunden. Für diese Tabelle gibt es keinen DSGVO-Cronjob.</p>';
            $content .= '<p><a class="btn btn-danger" href="index.php?page=yform/manager/data_edit&amp;table_name=rex_dsgvo_client" class="rex-button">' . $this->i18n('dsgvo_dementiasql_add_cronjob') . '</a></p></div>';
        }
    }


    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_dementia_cronjob'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

    // Schritt 4
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_tracking_description').'</p>';

    $sql = rex_sql::factory();
    $templates = $sql->setQuery('SELECT * FROM `rex_template` WHERE CONTAINS (`content`, "facebook or google or fonts or tracking or analytics or cdn or piwik or matomo")')->getArray();


    if(!$templates['id']) {
            $content .= '<div class="alert alert-success"><p>Es wurden keine Templates gefunden, die folgende Keywords enthalten: <code>facebook or google or fonts or tracking or analytics or cdn</code></p></div>';
    } else {
        foreach ($templates as $template) {
                $content .= '<div class="alert alert-danger"><p>In der Tabelle <code>'.$template['name'].'</code> eins der folgenden Keywords entdeckt: <code>facebook or google or fonts or tracking or analytics or cdn</code>. Für diese Tabelle gibt es keinen DSGVO-Cronjob.</p>';
                $content .= '<p><a class="btn btn-danger" href="index.php?page=templates&amp;function=edit&amp;template_id='.$template['id'].'" class="rex-button">' . $this->i18n('dsgvo_dementiasql_add_cronjob') . '</a></p></div>';
        }
    }

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

    // Schritt 5
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_dementiasql_cronjob_description').'</p>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_dementiasql_cronjob'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

    // Schritt 6
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_phpmailer-logs_cronjob_description').'</p>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_phpmailer-logs_cronjob'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');


}

$content = rex_i18n::rawMsg('dsgvo_description_all', false);
