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


}