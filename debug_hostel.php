<?php

$filePath = 'c:/xampp/htdocs/KIBIHASSYSTEM/resources/views/admin/welfare/reports/hostel.blade.php';
$content = file_get_contents($filePath);

echo "Checking $filePath\n";
if (preg_match('/@section\(\'content\'\)(.*?)@endsection/s', $content, $matches)) {
    $sectionContent = $matches[1];
    $openDivs = substr_count(strtolower($sectionContent), '<div');
    $closeDivs = substr_count(strtolower($sectionContent), '</div>');
    echo "Open: $openDivs, Close: $closeDivs\n";
    
    // Dump the end of the section content to see if it has divs
    echo "End of content:\n" . substr($sectionContent, -50) . "\n";
} else {
    echo "Regex failed to match content section.\n";
}
