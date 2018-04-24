<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {

      
    $keywords[] = "facebook";
    $keywords[] = "google";
    $keywords[] = "analytics";
    $keywords[] = "fonts";
    $keywords[] = "cdn";
    $keywords[] = "matomo";
    $keywords[] = "piwik";
    $keywords[] = "adobe";
    $keywords[] = "instagram";
    $keywords[] = "youtube";
    $keywords[] = "twitter";
    $keywords[] = "pinterest";
    $keywords[] = "fontfont";
    $keywords[] = "typekit";


    $content = '';
    $content .= $this->i18n('check_dsgvo_tracking_description', implode(",",$keywords));


    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');


    // Templates
    $sql = rex_sql::factory();
    $templates = array_filter($sql->setDebug(0)->getArray('SELECT `id`, `name` FROM `rex_template` WHERE `content` LIKE "%'.implode('%" OR `content` LIKE "%', $keywords).'%"'));
    $list = rex_list::factory('SELECT `id`, `name` FROM `rex_template` WHERE `content` LIKE "%'.implode('%" OR `content` LIKE "%', $keywords).'%"', 10, $listName, $debug);
    if(count($templates)) {
        $class = " panel-danger";
    } else {
        $class = " panel-success";
    }
    $list->setNoRowsMessage($this->i18n("check_dsgvo_tracking_templates_success", implode(",",$keywords)));
    
    $list->addColumn('external_template_edit', '');
    $list->setColumnLabel('external_template_edit', $this->i18n('check_dsgvo_external_template_column'));
    $list->setColumnFormat('external_template_edit', 'custom', function ($params) {
 //       $has_template = array_shift(array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_server_log WHERE domain = "'.$params['list']->getValue('domain').'" ORDER BY createdate DESC')));
        if ($has_template) {
            return '<a href="">'.$this->i18n('check_dsgvo_external_template_edit').'</a>';
        } else {
            return '<a href="">'.$this->i18n('check_dsgvo_external_template_add').'</a>';
        }
    });


    $fragment = new rex_fragment();

    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking_templates_danger', implode(",",$keywords)), false);
    $fragment->setVar('body', $list->get(), false);
    $fragment->setVar('class', $class, false);
    echo $fragment->parse('core/page/section.php');

    // Module
    $sql = rex_sql::factory();
    $query = 'SELECT `id`, `name` FROM `rex_module` WHERE `output` LIKE "%'.implode('%" OR `output` LIKE "%', $keywords).'%"';
    $modules = array_filter($sql->setDebug(0)->getArray($query));
    $list = rex_list::factory($query, 10, $listName, $debug);
    if(count($modules)) {
        $class = " panel-danger";
    } else {
        $class = " panel-success";
    }
    $list->setNoRowsMessage($this->i18n("check_dsgvo_tracking_modules_success", implode(",",$keywords)));


    
    $list->addColumn('external_module_edit', '');
    $list->setColumnLabel('external_module_edit', $this->i18n('check_dsgvo_external_module_column'));
    $list->setColumnFormat('external_module_edit', 'custom', function ($params) {
 //       $has_template = array_shift(array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_server_log WHERE domain = "'.$params['list']->getValue('domain').'" ORDER BY createdate DESC')));
        if ($has_template) {
            return '<a href="">'.$this->i18n('check_dsgvo_external_module_edit').'</a>';
        } else {
            return '<a href="">'.$this->i18n('check_dsgvo_external_module_add').'</a>';
        }
    });


    $fragment = new rex_fragment();

    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking_modules_danger', implode(",",$keywords)), false);
    $fragment->setVar('body', $list->get(), false);
    $fragment->setVar('class', $class, false);
    echo $fragment->parse('core/page/section.php');


}