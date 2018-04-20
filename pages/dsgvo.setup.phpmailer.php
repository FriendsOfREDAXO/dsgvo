<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {
   
    // Schritt 6
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_phpmailer-logs_cronjob_description').'</p>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_phpmailer-logs_cronjob'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

}