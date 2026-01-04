<?php

$filePath = 'c:/xampp/htdocs/KIBIHASSYSTEM/resources/views/dashboard.blade.php';
$content = file_get_contents($filePath);

echo "Checking $filePath\n";
if (preg_match('/@section\(\'content\'\)(.*?)@endsection/s', $content, $matches)) {
    $sectionContent = $matches[1];
    $openDivs = substr_count(strtolower($sectionContent), '<div');
    $closeDivs = substr_count(strtolower($sectionContent), '</div>');
    echo "Open: $openDivs, Close: $closeDivs\n";
} else {
    echo "Regex failed to match content section.\n";
}
