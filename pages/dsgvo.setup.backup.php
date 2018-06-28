<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin() && rex_addon::get('backup')->isAvailable()) {
    $content = rex_i18n::rawMsg('dsgvo_backup_description', false);
    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_backup_title'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

    // Cronjob
    
    $sql = rex_sql::factory();
    $query = 'SELECT `id`, `name`, parameters, nexttime, environment, `status` FROM `rex_cronjob` WHERE `type` = "rex_cronjob_export"';
    $list = rex_list::factory($query, 10, "dsgvo_backup", 0);
  
    $list->setNoRowsMessage($this->i18n("check_dsgvo_backupcronjob_none"));

    $list->setColumnLabel('id', $this->i18n('check_dsgvo_backupcronjob_id'));
    $list->setColumnLabel('name', $this->i18n('check_dsgvo_backupcronjob_name'));
    $list->setColumnLabel('nexttime', $this->i18n('check_dsgvo_backupcronjob_nexttime'));
    $list->setColumnLabel('status', $this->i18n('check_dsgvo_backupcronjob_status'));
    
    $list->removeColumn('parameters');
    $list->removeColumn('id');
    $list->removeColumn('environment');
    $list->addColumn('external_module_edit', '');
    $list->setColumnLabel('external_module_edit', $this->i18n('check_dsgvo_backupcronjob_edit'));
    $list->setColumnFormat('external_module_edit', 'custom', function ($params) {
        return '<a href="http://dsgvo.pixelfirma.de/redaxo/index.php?page=cronjob/cronjobs&func=edit&oid=###id###">'.$this->i18n('check_dsgvo_backupcronjob_edit').'</a>';
    });


    $fragment = new rex_fragment();

    $fragment->setVar('title', $this->i18n('check_dsgvo_backupcronjob_title'), false);
    $fragment->setVar('body', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
    // Aus dem Backup-Addon kopiert und angepasst:
    // import.server.php ab Zeile 128

    $dir = rex_backup::getDir();
    $folder = rex_backup::getBackupFiles('.sql');
    
    $content = '<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th class="rex-table-icon"></th>
            <th>' . rex_i18n::msg('backup_filename') . '</th>
            <th class="rex-table-width-5">' . rex_i18n::msg('backup_filesize') . '</th>
            <th class="rex-table-width-5">' . rex_i18n::msg('backup_createdate') . '</th>
        </tr>
    </thead>
    <tbody>';

    foreach ($folder as $file) {
        $filepath = $dir . '/' . $file;
        $filec = date('d.m.Y H:i', filemtime($filepath));
        $filesize = rex_file::formattedSize($filepath);


        $content .= '<tr>
                        <td class="rex-table-icon"><i class="rex-icon rex-icon-database"></i></td>
                        <td data-title="' . rex_i18n::msg('backup_filename') . '">' . $file . '</td>
                        <td data-title="' . rex_i18n::msg('backup_filesize') . '">' . $filesize . '</td>
                        <td data-title="' . rex_i18n::msg('backup_createdate') . '">' . $filec . '</td>
                    </tr>
        ';
    }
    
    $content .= '
                        </tbody>
                    </table>';
    
    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('backup_export_db_caption'), false);
    $fragment->setVar('content', $content, false);
    $content = $fragment->parse('core/page/section.php');
    
    echo $content;
    


}