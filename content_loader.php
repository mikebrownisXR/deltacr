<?php
// Simple README.md section extractor
function parse_markdown_sections($path)
{
    if (!is_readable($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    $sections = [];
    $current = null;
    foreach ($lines as $line) {
        if (preg_match('/^#{1,3}\s*(.+)$/', $line, $m)) {
            $current = trim($m[1]);
            $sections[$current] = '';
            continue;
        }
        if ($current !== null) {
            $sections[$current] .= $line . "\n";
        }
    }
    return $sections;
}

function get_readme_section_by_keywords($keywords)
{
    $path = __DIR__ . '/README.md';
    $sections = parse_markdown_sections($path);
    $results = [];
    foreach ($sections as $title => $content) {
        foreach ((array)$keywords as $kw) {
            if (stripos($title, $kw) !== false || stripos($content, $kw) !== false) {
                $results[$title] = $content;
                break;
            }
        }
    }
    return $results;
}

function render_markdown_as_html($md)
{
    // Very small markdown -> html conversion for basic headings & paragraphs
    $html = htmlspecialchars($md);
    // headings
    $html = preg_replace('/^###\s*(.+)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^##\s*(.+)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^#\s*(.+)$/m', '<h1>$1</h1>', $html);
    // links [text](url)
    $html = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $html);
    // paragraphs
    $html = preg_replace('/\n{2,}/', "</p><p>", $html);
    $html = '<p>' . trim($html) . '</p>';
    return $html;
}
