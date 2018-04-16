<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {


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
    
}