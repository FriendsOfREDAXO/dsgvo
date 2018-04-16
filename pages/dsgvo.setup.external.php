<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {

      
    // Schritt 4
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_tracking_description').'</p>';
    $keywords[] = "facebook";
    $keywords[] = "google";
    $keywords[] = "analytics";
    $keywords[] = "tracking";
    $keywords[] = "fonts";
    $keywords[] = "cdn";
    $keywords[] = "matomo";
    $keywords[] = "piwik";
    $keywords[] = "adobe";

    $sql = rex_sql::factory();
    $templates = array_filter($sql->setDebug(0)->getArray('SELECT `id`, `name` FROM `rex_template` WHERE `content` LIKE "%'.implode('%" OR `content` LIKE "%', $keywords).'%"'));


    if(!count($templates)) {
            $content .= '<div class="alert alert-success"><p>'.$this->i18n("check_dsgvo_tracking_success", implode(",", $keywords)).'</p></div>';
    } else {
        foreach ($templates as $template) {
                $content .= '<div class="alert alert-danger"><p>'.$this->i18n('check_dsgvo_tracking_danger', $template['name'], implode(",", $keywords)).'</p>';
                $content .= '<p><a class="btn btn-danger" href="index.php?page=templates&amp;function=edit&amp;template_id='.$template['id'].'" class="rex-button">' . $this->i18n('dsgvo_dementiasql_add_cronjob') . '</a></p></div>';
        }
    }

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

}