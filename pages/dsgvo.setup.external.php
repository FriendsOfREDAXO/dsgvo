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
    $fragment = new rex_fragment();

    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking_templates_danger', implode(",",$keywords)), false);
    $fragment->setVar('body', $list->get(), false);
    $fragment->setVar('class', $class, false);
    echo $fragment->parse('core/page/section.php');

    // Module
    $sql = rex_sql::factory();
    $modules = array_filter($sql->setDebug(0)->getArray('SELECT `id`, `name` FROM `rex_module` WHERE `output` LIKE "%'.implode('%" OR `output` LIKE "%', $keywords).'%"'));
    $list = rex_list::factory('SELECT `id`, `name` FROM `rex_module` WHERE `output` LIKE "%'.implode('%" OR `output` LIKE "%', $keywords).'%"', 10, $listName, $debug);
    if(count($modules)) {
        $class = " panel-danger";
    } else {
        $class = " panel-success";
    }
    $list->setNoRowsMessage($this->i18n("check_dsgvo_tracking_modules_success", implode(",",$keywords)));
    $fragment = new rex_fragment();

    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking_modules_danger', implode(",",$keywords)), false);
    $fragment->setVar('body', $list->get(), false);
    $fragment->setVar('class', $class, false);
    echo $fragment->parse('core/page/section.php');


}