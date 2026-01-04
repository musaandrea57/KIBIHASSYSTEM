<?php

$file = 'c:/xampp/htdocs/KIBIHASSYSTEM/resources/views/dashboard.blade.php';
$content = file_get_contents($file);

// Fix the missing closing divs in the middle
$badBlock = '                                <span class="inline-block px-4 py-2 rounded-full font-bold text-sm uppercase {{ $statusBadgeClass }}">
                                    {{ str_replace(\'_\', \' \', $application->status) }}
                                </span>
                    </div>
                    <div class="p-6 border-b border-gray-200">';

$goodBlock = '                                <span class="inline-block px-4 py-2 rounded-full font-bold text-sm uppercase {{ $statusBadgeClass }}">
                                    {{ str_replace(\'_\', \' \', $application->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 border-b border-gray-200">';

if (strpos($content, $badBlock) !== false) {
    $content = str_replace($badBlock, $goodBlock, $content);
    echo "Fixed middle of dashboard.blade.php\n";
}

// Fix the extra closing divs at the end
// Find the last </div></div> and remove them if they are redundant
// We know there are 2 extra divs at the end based on previous analysis
$endBlock = '            @endif
        </div>
    </div>

@endsection';

$fixedEndBlock = '            @endif
        </div>
    </div>
@endsection';
// Wait, the end block in my read was:
// 114->            @endif
// 115->        </div>
// 116->    </div>
// 117->
// 118->@endsection
// If I restored the 2 missing divs in the middle, then the 2 divs at the end are actually NEEDED?
// No, the regex consumed the middle divs (so they are gone).
// And replaced the wrapper start/end with... nothing (for dashboard).
// So the wrapper start is gone.
// So the wrapper end (Line 115, 116) IS redundant.
// The middle divs I just restored are for the Inner Content structure (Header closure).
// So yes, I need to remove the end divs.

$content = preg_replace('/<\/div>\s*<\/div>\s*@endsection/', "@endsection", $content);
echo "Fixed end of dashboard.blade.php\n";

file_put_contents($file, $content);
