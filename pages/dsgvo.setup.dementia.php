<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {


    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_dementiasql_cronjob_description').'</p>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_dementiasql_cronjob'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
    
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_dementia_cronjob_description').'</p>';
/*
            $content .= '<div class="alert alert-danger"><p>In der Tabelle <code>'.$table['TABLE_NAME']."</code> wurde ein Feld namens <code>" . $table['COLUMN_NAME'].'</code> gefunden. Für diese Tabelle gibt es keinen DSGVO-Cronjob.</p>';
            $content .= '<p><a class="btn btn-danger" href="index.php?page=yform/manager/data_edit&amp;table_name=rex_dsgvo_client" class="rex-button">' . $this->i18n('dsgvo_dementiasql_add_cronjob') . '</a></p></div>';
*/

    $sql = rex_sql::factory();
    $query = 'SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    DATA_TYPE AS "Datentyp"
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE 
         TABLE_NAME LIKE "rex_%" AND 
         ((COLUMN_NAME = "createdate" OR 
         COLUMN_NAME = "updatedate" OR 
         COLUMN_NAME = "datestamp" OR 
         COLUMN_NAME = "datetime" OR 
         COLUMN_NAME = "timestamp" OR 
         DATA_TYPE = "datetime") OR
         (COLUMN_NAME = "email" OR 
         COLUMN_NAME = "e-mail" OR 
         COLUMN_NAME = "Email" OR 
         COLUMN_NAME LIKE "%mail%" OR 
         COLUMN_NAME = "lastname" OR 
         COLUMN_NAME = "prename" OR 
         COLUMN_NAME = "vorname" OR 
         COLUMN_NAME = "street" OR 
         COLUMN_NAME = "birthday" OR 
         COLUMN_NAME = "geburtstag")) AND
         TABLE_NAME != "rex_action" AND
         TABLE_NAME != "rex_article" AND
         TABLE_NAME != "rex_article_slice" AND
         TABLE_NAME != "rex_cronjob" AND
         TABLE_NAME != "rex_dsgvo_client" AND
         TABLE_NAME != "rex_dsgvo_server" AND
         TABLE_NAME != "rex_dsgvo_server_log" AND
         TABLE_NAME != "rex_dsgvo_server_project" AND
         TABLE_NAME != "rex_media" AND
         TABLE_NAME != "rex_media_category" AND
         TABLE_NAME != "rex_media_manager_type_effect" AND
         TABLE_NAME != "rex_module" AND
         TABLE_NAME != "rex_template" AND
         TABLE_NAME != "rex_user" AND
         TABLE_NAME != "rex_url_generate" AND
         TABLE_NAME != "rex_user" AND
         TABLE_NAME != "rex_user_role" 
         ORDER BY table_name';

    $templates = array_filter($sql->setDebug(0)->getArray($query));

    $list = rex_list::factory($query, 50, $listName, $debug);
    if(count($templates)) {
        $class = " panel-danger";
    } else {
        $class = " panel-success";
    }
    $list->setNoRowsMessage($this->i18n("check_dsgvo_dementia_cronjob_success"));

    $list->setColumnLabel('TABLE_NAME', $this->i18n('check_dsgvo_dementia_cronjob_table_name'));
    $list->setColumnLabel('COLUMN_NAME', $this->i18n('check_dsgvo_dementia_cronjob_column_name'));
    $list->setColumnLabel('DATA_TYPE', $this->i18n('check_dsgvo_dementia_cronjob_data_type'));

    $list->addColumn('dementia_cronjob', '');
    $list->setColumnLabel('dementia_cronjob', $this->i18n('check_dsgvo_dementia_cronjob_column'));
    $list->setColumnFormat('dementia_cronjob', 'custom', function ($params) {
 //       $has_cronjob = array_shift(array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_server_log WHERE domain = "'.$params['list']->getValue('domain').'" ORDER BY createdate DESC')));
        if ($has_cronjob) {
            $str = '<a href="index.php?page=cronjob/cronjobs&func=add&oid=2&list=cronjobs">'.$this->i18n('check_dsgvo_dementia_cronjob_edit').'</a>';
        } else {
            $str = '<a href="index.php?page=cronjob/cronjobs&func=add&list=cronjobs">'.$this->i18n('check_dsgvo_dementia_cronjob_add').'</a>';
            //$list->addLinkAttribute('dementia_cronjob','test','test');
        }
        return $str;
    });


    $fragment = new rex_fragment();

    $fragment->setVar('title', $this->i18n('check_dsgvo_dementia_cronjob'), false);
    $fragment->setVar('body', $list->get(), false);
    $fragment->setVar('class', $class, false);
    echo $fragment->parse('core/page/section.php');
}