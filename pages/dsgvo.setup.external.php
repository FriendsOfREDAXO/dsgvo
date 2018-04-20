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


    $sql = rex_sql::factory();
    $templates = array_filter($sql->setDebug(0)->getArray('SELECT `id`, `name` FROM `rex_template` WHERE `content` LIKE "%'.implode('%" OR `content` LIKE "%', $keywords).'%"'));



    $list = rex_list::factory('SELECT `id`, `name` FROM `rex_template` WHERE `content` LIKE "%'.implode('%" OR `content` LIKE "%', $keywords).'%"', 10, $listName, $debug);
    
    if(count($templates)) {
        $class = " panel-danger";
    } else {
        $class = " panel-success";
    }
    $list->setNoRowsMessage($this->i18n("check_dsgvo_tracking_success"));

    #                $content .= '<p><a class="btn btn-danger" href="index.php?page=templates&amp;function=edit&amp;template_id='.$template['id'].'" class="rex-button">' . $this->i18n('dsgvo_dementiasql_add_cronjob') . '</a></p></div>';
    $fragment = new rex_fragment();

    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking_danger'), false);
    $fragment->setVar('body', $list->get(), false);
    $fragment->setVar('class', $class, false);
    echo $fragment->parse('core/page/section.php');

    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_tracking_description').'</p><code>'.implode(",", $keywords).'</code>';


    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_tracking'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');


}