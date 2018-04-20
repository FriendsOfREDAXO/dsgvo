<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {
    $content = rex_i18n::rawMsg('dsgvo_backup_description', false);
    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_backup_title'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

    // Prüfung Backup-Cronjob
    // Prüfung Backup-Verzeichnisinhalt

}