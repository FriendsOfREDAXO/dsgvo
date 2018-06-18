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

    foreach($keywords as $keyword) {
        // Templates
        $sql = rex_sql::factory();
        $query = 'SELECT `id`, `name` FROM `rex_template` WHERE (`content` REGEXP \'([\\S]*)('.implode('|', [$keyword]).')([\\S]*)\')';
        $templates = array_filter($sql->setDebug(0)->getArray($query));
        $list = rex_list::factory($query, 10, $listName, $debug);
        if(count($templates)) {
            $class = " panel-danger";
        } else {
            $class = " panel-success";
        }
        $list->setNoRowsMessage($this->i18n("check_dsgvo_tracking_templates_success", implode(",",[$keyword])));

        $list->setColumnLabel('id', $this->i18n('check_dsgvo_external_template_id'));
        $list->setColumnLabel('name', $this->i18n('check_dsgvo_external_template_name'));
        
        $list->addColumn('external_template_edit', '');
        $list->setColumnLabel('external_template_edit', $this->i18n('check_dsgvo_external_template_column'));
        $list->setColumnFormat('external_template_edit', 'custom', function ($params) {
                return '<a href="index.php?page=templates&start=0&function=edit&template_id=###id###&list=feb98055ee8721432e8326ab2dc0d609">'.$this->i18n('check_dsgvo_external_template_edit').'</a>';
        });

        $fragment = new rex_fragment();

        $fragment->setVar('title', $this->i18n('check_dsgvo_tracking_templates_danger', implode(",",$keywords)), false);
        $fragment->setVar('body', $list->get(), false);
        $fragment->setVar('class', $class, false);
        echo $fragment->parse('core/page/section.php');
    }

    foreach($keywords as $keyword) {
        // Module
        $sql = rex_sql::factory();
        $query = 'SELECT `id`, `name` FROM `rex_module` WHERE (`output` REGEXP \'([\\S]*)('.implode('|', [$keyword]).')([\\S]*)\')';

        $modules = array_filter($sql->setDebug(0)->getArray($query));
        $list = rex_list::factory($query, 10, $listName, $debug);
        if(count($modules)) {
            $class = " panel-danger";
        } else {
            $class = " panel-success";
        }
        $list->setNoRowsMessage($this->i18n("check_dsgvo_tracking_modules_success", implode(",",[$keyword])));

        $list->setColumnLabel('id', $this->i18n('check_dsgvo_external_module_id'));
        $list->setColumnLabel('name', $this->i18n('check_dsgvo_external_module_name'));
        
        $list->addColumn('external_module_edit', '');
        $list->setColumnLabel('external_module_edit', $this->i18n('check_dsgvo_external_module_column'));
        $list->setColumnFormat('external_module_edit', 'custom', function ($params) {
            return '<a href="http://dsgvo.pixelfirma.de/redaxo/index.php?page=modules/modules&start=0&function=edit&module_id=###id###&list=fcbe7c7a02e42a4eaf0874a32644e84e">'.$this->i18n('check_dsgvo_external_module_edit').'</a>';
        });


        $fragment = new rex_fragment();

        $fragment->setVar('title', $this->i18n('check_dsgvo_tracking_modules_danger', implode(",",$keywords)), false);
        $fragment->setVar('body', $list->get(), false);
        $fragment->setVar('class', $class, false);
        echo $fragment->parse('core/page/section.php');
    }

}