<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin() && rex_addon::get('phpmailer')->isAvailable()) {
   
    $content = '';
    $content .= '<p>'.$this->i18n('check_dsgvo_phpmailer-logs_cronjob_description').'</p>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('check_dsgvo_phpmailer-logs_cronjob'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');


    $dir = rex_path::addonData('phpmailer').'mail_log';

    if(@opendir($dir)) {
    $files = [];

    function getDir($dir,$pre) {
        global $files;
        $pre.= "-";
        $dh = opendir($dir);
        while($file = readdir($dh)) {
            if($file != "." && $file != "..") {
                if(is_dir("$dir/$file")) {
                    // dump("$pre $file [DIR]");
                    getDir("$dir/$file",$pre);
                } else {
                    //dump("$pre $file [DIR]");
                    $files[$dir] = $file;
                }
            }
        }
        closedir($dh);
        return $files;
      }
    
    $files = getDir($dir, "");

    }

    $content = '<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th class="rex-table-icon"></th>
            <th>' . rex_i18n::msg('check_dsgvo_phpmailer-logs_filename') . '</th>
            <th class="rex-table-width-5">' . rex_i18n::msg('check_dsgvo_phpmailer-logs_folder') . '</th>
            <th class="rex-table-width-5">' . rex_i18n::msg('check_dsgvo_phpmailer-logs_filesize') . '</th>
            <th class="rex-table-width-5">' . rex_i18n::msg('check_dsgvo_phpmailer-logs_createdate') . '</th>
        </tr>
    </thead>
    <tbody>';

    if(count($files)) {
        foreach ($files as $folder => $file) {
            $filec = date('d.m.Y H:i', filemtime($folder."/".$file));
            $filesize = rex_file::formattedSize($folder."/".$file);


            $content .= '<tr>
                            <td class="rex-table-icon"><i class="rex-icon rex-icon-article"></i></td>
                            <td data-title="' . rex_i18n::msg('check_dsgvo_phpmailer-logs_filename') . '">' . $file . '</td>
                            <td data-title="' . rex_i18n::msg('check_dsgvo_phpmailer-logs_folder') . '">' . $folder . '</td>
                            <td data-title="' . rex_i18n::msg('check_dsgvo_phpmailer-logs_filesize') . '">' . $filesize . '</td>
                            <td data-title="' . rex_i18n::msg('check_dsgvo_phpmailer-logs_createdate') . '">' . $filec . '</td>
                        </tr>
            ';
        }
    }
    $content .= '
                        </tbody>
                    </table>';
    
    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('check_dsgvo_phpmailer-logs_caption'), false);
    $fragment->setVar('content', $content, false);
    $content = $fragment->parse('core/page/section.php');
    
    echo $content;
    
}