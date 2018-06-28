<?php

echo rex_view::title($this->i18n('dsgvo'));

if (rex::getUser()->isAdmin()) {

    $content1 = '';
    $content1 .= '<p>'.$this->i18n('dsgvo_setup_contactform_description').'</p>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_setup_contactform_title'), false);
    $fragment->setVar('body', $content1, false);
    echo $fragment->parse('core/page/section.php');
   

    $checkbox_pipe_title = $this->i18n('dsgvo_setup_contactform_pipe_title');

$checkbox_pipe = '<p>'.$this->i18n('dsgvo_setup_contactform_pipe_content').'</p>';
$checkbox_pipe .=
'<pre class="pre-scrollable">'.
htmlentities('checkbox|dsgvo_checkbox|Ich habe die <a href="">Datenschutzerklärung</a> zur Kenntnis genommen. Ich stimme zu, dass meine Angaben und Daten zur Beantwortung meiner Anfrage elektronisch erhoben und gespeichert werden.|0,1|0|no_db|
validate|empty|dsgvo_checkbox|Bitte bestätigen Sie, dass Sie die Datenschutzerklärung zur Kenntnis genommen haben und stimmen Sie der elektronischen Verwendung Ihrer Daten zur Beantwortung Ihrer Anfrage zu.|
html|dsgvo_notice||<p>Hinweis: Sie können Ihre Einwilligung jederzeit für die Zukunft per E-Mail an E-Mail-Adresse@xyz.de widerrufen.</p>|').
'</pre>';
    
    $fragment = new rex_fragment();
    $fragment->setVar('title', $checkbox_pipe_title, false);
    $fragment->setVar('body', $checkbox_pipe, false);
    echo $fragment->parse('core/page/section.php');
    
    $checkbox_php_title = $this->i18n('dsgvo_setup_contactform_php_title');
    
$checkbox_php = '<p>'.$this->i18n('dsgvo_setup_contactform_php_content').'</p>';
$checkbox_php .=
'<pre class="pre-scrollable">'.
htmlentities('$yform->setValueField(\'checkbox\', array(\'dsgvo_checkbox\',\'Ich habe die <a href="">Datenschutzerklärung</a> zur Kenntnis genommen. Ich stimme zu, dass meine Angaben und Daten zur Beantwortung meiner Anfrage elektronisch erhoben und gespeichert werden.\',\'0,1\',\'0\',\'no_db\'));
$yform->setValidateField(\'empty\', array(\'dsgvo_checkbox\',\'Bitte bestätigen Sie, dass Sie die Datenschutzerklärung zur Kenntnis genommen haben und stimmen Sie der elektronischen Verwendung Ihrer Daten zur Beantwortung Ihrer Anfrage zu.\'));
$yform->setValueField(\'html\', array(\'dsgvo_notice\',\'\',\'<p>Hinweis: Sie können Ihre Einwilligung jederzeit für die Zukunft per E-Mail an E-Mail-Adresse@xyz.de widerrufen.</p>\'));').
'</pre>';
    
    $fragment = new rex_fragment();
    $fragment->setVar('title', $checkbox_php_title, false);
    $fragment->setVar('body', $checkbox_php, false);
    echo $fragment->parse('core/page/section.php');


    $content .= '<p>'.$this->i18n('dsgvo_setup_contactform_general_content').'</p>';
    $content .= 
'<pre class="pre-scrollable">'.
htmlentities('<p><input type="checkbox" value="1" required="required" name="dsgvo_contactform" /> Ich habe die <a href="">Datenschutzerklärung</a> zur Kenntnis genommen. Ich stimme zu, dass meine Angaben und Daten zur Beantwortung meiner Anfrage elektronisch erhoben und gespeichert werden.</p>
<p>Hinweis: Sie können Ihre Einwilligung jederzeit für die Zukunft per E-Mail an E-Mail-Adresse@xyz.de widerrufen.</p>').
'</pre>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('dsgvo_setup_contactform_general_title'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

}
