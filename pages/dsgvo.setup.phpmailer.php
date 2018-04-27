<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {
   
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_phpmailer-logs_cronjob_description').'</p>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_phpmailer-logs_cronjob'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');


    $dir = rex_path::addonData('phpmailer').'mail_log';

    $files = [];

    function getDir($dir,$pre) {
        global $files;
        $pre.= "-";
        $dh = opendir($dir);
        while($file = readdir($dh)) {
          if($file != "." && $file != "..") {
            if(is_dir("$dir/$file")) {
              dump("$pre $file [DIR]");
              getDir("$dir/$file",$pre);
            } else {
              $files[] = $file;
            }
          }
        }
        closedir($dh);
      }
    
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

    foreach ($files as $file) {
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