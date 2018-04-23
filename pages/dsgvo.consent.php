<?php 

echo rex_view::title($this->i18n('dsgvo'));

$message = '';

if (rex_post('btn_save', 'string') != '') {
    $this->setConfig(rex_post('settings', [
        ['dsgvo_consent_css', 'string']
    ]));

    $message = $this->i18n('dsgvo_consent_css_saved_successful');
}

$content = '';


$formElements = [];
$n = [];
$n['label'] = '<label for="phpmailer-dsgvo_consent_css">' . $this->i18n('dsgvo_consent_css_label') . '</label>';
$n['field'] = '<textarea class="form-control codemirror" id="phpmailer-dsgvo_consent_css" name="settings[dsgvo_consent_css]">'.$this->getConfig('dsgvo_consent_css') . '</textarea>';
$n['note'] = 'Test';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$fragment->setVar('class', "panel panel-warning", false);
$content .= $fragment->parse('core/form/form.php');

$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="' . $this->i18n('save') . '">' . $this->i18n('save') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('flush', true);
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('dsgvo_consent_css_title'), false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');
echo '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        ' . $content . '
    </form>';
