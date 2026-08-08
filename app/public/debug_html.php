<?php
$html = file_get_contents('http://cora.local/test-2/?cv_preview_theme=44');
echo htmlspecialchars($html);
