<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {

    $content = rex_i18n::rawMsg('dsgvo_description_all', false);
    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_description_all_title'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

    $content = rex_i18n::rawMsg('dsgvo_description_ad', false);
    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_description_ad_title'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
    
}
