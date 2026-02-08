<?php
require_once __DIR__ . '/../content_loader.php';
require_once __DIR__ . '/../config.php';
$sections = get_readme_section_by_keywords(['admin','Admin','administrator']);
?><!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/css/styles.css"><title>Admin Guide</title></head>
<body><header class="topbar"><div class="brand">DeltaCR</div><nav class="nav"><a href="/index.php">Home</a><a href="/dashboard.php">Dashboard</a></nav></header>
<main class="form-wrap"><h1>Admin Guide</h1>
<?php if($sections): foreach($sections as $t=>$c): echo render_markdown_as_html("# $t\n\n".$c); endforeach; else: ?>
<p>No dedicated admin section found in README. See the <a href="/README.md">project README</a>.</p>
<?php endif; ?></main></body></html>
