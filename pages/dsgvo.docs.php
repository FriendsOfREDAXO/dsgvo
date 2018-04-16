<?php
/**
 * DSGVO Addon.
 *
 * @author alexplusde,
 *
 * @package redaxo\dsgvo
 *
 * @var rex_addon $this
 */
echo rex_view::title($this->i18n('dsgvo'));

$content = file_get_contents(rex_path::addon('dsgvo')."README.md");
$content_blocks = [];
$h2_blocks = explode("\n## ", "\n".$content);
foreach ($h2_blocks as $h2_i => $h2_block) {
    preg_match('/(.*)\n^(?:.|\n(?!#))*/m', $h2_block, $headline);
    if (isset($headline[1])) {
        $navi_list[] = '* '.$headline[1];
        $content_h2_block = '# '.$headline[0];
        preg_match_all('/(?!### )*^### (.*)\n((?:.|\n(?!### ))*)/m', $h2_block, $matches);
        if (count($matches[0]) > 0) {
            $navi_elements = $matches[1];
            $blocks = $matches[2];
            $content_blocks['chapter'.$h2_i] = $content_h2_block;
            foreach ($navi_elements as $h3_i => $navi_element) {
                $navi_list[] = '	* <a href="index.php?page=dsgvo/docs&amp;n=chapter'.$h2_i.'#section'.$h3_i.'">'.$navi_element.'</a>';
                $content_blocks["chapter".$h2_i] .= "\n## ".$navi_element.$blocks[$h3_i];
            }
        }
    }
}
reset($content_blocks);
$n = rex_request('n', 'string', key($content_blocks));

if (!isset($content_blocks[$n])) {
    $n = key($content_blocks);
}
$navi_view = implode("\n", $navi_list);
    $blocks_view = $content_blocks[$n];

    $miu = rex_markdown::factory();

// Navigation 
$navi_view = $miu->parse($navi_view);
$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('dsgvo_docs_navigation').'', false);
$fragment->setVar('body', $navi_view, false);
$navi = $fragment->parse('core/page/section.php');


$blocks_view = $miu->parse($blocks_view);
$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('docs').' [ <a href="https://github.com/alexplusde/dsgvo/blob/master/README.md">README.md</a> ]', false);
$fragment->setVar('body', $blocks_view, false);
$content = $fragment->parse('core/page/section.php');

echo '<section class="rex-yform-docs">
    <div class="row">
    <div class="col-md-4 yform-docs-navi">'.$navi.'</div>
    <div class="col-md-8 yform-docs-content">'.$content.'</div>
    </div>
</section>';