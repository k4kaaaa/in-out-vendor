<?php
$base = __DIR__ . '/resources/views/';

// Fix app.blade.php sidebar logo
$file = $base . 'layouts/app.blade.php';
$c = file_get_contents($file);
$old1 = "            <div class=\"w-9 h-9 rounded-xl bg-white shadow-md shrink-0 flex items-center justify-center\">\n                <svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\">\n                    <img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-5 h-5\" alt=\"Logo\">\n                </svg>\n            </div>";
$new1 = "            <div class=\"w-10 h-10 rounded-xl bg-white shadow-md shrink-0 flex items-center justify-center p-1.5\">\n                <img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-full h-full object-contain\" alt=\"Logo\">\n            </div>";
$c = str_replace($old1, $new1, $c);

// Fix menu item logo in app
$old2 = "                <svg class=\"w-5 h-5 shrink-0\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\">\n                    <img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-5 h-5\" alt=\"Logo\">\n                </svg>";
$new2 = "                <img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-5 h-5 object-contain\" alt=\"Logo\">";
$c = str_replace($old2, $new2, $c);
file_put_contents($file, $c);
echo "app.blade.php\n";

// Fix staff.blade.php sidebar logo
$file = $base . 'layouts/staff.blade.php';
$c = file_get_contents($file);
$old1 = "            <div class=\"w-9 h-9 rounded-xl bg-white shadow-md shrink-0 flex items-center justify-center\">\n                <svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\">\n                    <img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-5 h-5\" alt=\"Logo\">\n                </svg>\n            </div>";
$new1 = "            <div class=\"w-10 h-10 rounded-xl bg-white shadow-md shrink-0 flex items-center justify-center p-1.5\">\n                <img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-full h-full object-contain\" alt=\"Logo\">\n            </div>";
$c = str_replace($old1, $new1, $c);

$old2 = "                <svg class=\"w-5 h-5 shrink-0\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\">\n                    <img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-5 h-5\" alt=\"Logo\">\n                </svg>";
$new2 = "                <img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-5 h-5 object-contain\" alt=\"Logo\">";
$c = str_replace($old2, $new2, $c);
file_put_contents($file, $c);
echo "staff.blade.php\n";

// Fix dashboard icons - use just the img replacement
foreach (['dashboard.blade.php', 'dashboard_staff.blade.php'] as $f) {
    $file = $base . $f;
    $c = file_get_contents($file);
    // Replace any remaining svg wrapped logo-cube instances
    $c = preg_replace('/<svg[^>]*>[\s\n]*<img src=".*?logo-cube[^"]*"[^>]*>[\s\n]*<\/svg>/i', function($m) {
        return '<img' . preg_match('/class="[^"]*"/', $m[0], $mc) ? $mc[0] : 'class="w-5 h-5 object-contain"' . '">';
    }, $c);
    // Simpler: find logo-cube inside svg and extract the img tag
    if (strpos($c, "<img src=\"{{ asset('images/logo-cube.png') }}\"") !== false && strpos($c, "</svg>") !== false) {
        $c = preg_replace('#(<svg[^>]*>)(.*?<img src=".*?logo-cube".*?</svg>)#s', '<$2>', $c);
    }
    file_put_contents($file, $c);
    echo "$f\n";
}

// Fix products/index fallback icon
$file = $base . 'products/index.blade.php';
$c = file_get_contents($file);
$old = "                            <svg class=\"w-6 h-6 text-accent/40\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.5\" viewBox=\"0 0 24 24\">\n                                <path d=\"M12 3L21 8V16L12 21L3 16V8L12 3Z\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linejoin=\"round\"/>\n                                <path d=\"M12 3L3 8L12 13L21 8\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n                                <path d=\"M12 13V21\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linecap=\"round\"/>\n                                <path d=\"M7 10L4 12L7 14\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" fill=\"none\"/>\n                                <path d=\"M4 12H10\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\"/>\n                                <path d=\"M17 10L20 12L17 14\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" fill=\"none\"/>\n                                <path d=\"M14 12H20\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\"/>\n                            </svg>";
$new = "<img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-6 h-6 object-contain\" alt=\"Logo\">";
$c = str_replace($old, $new, $c);

$old2 = "                                <svg class=\"w-12 h-12 mx-auto mb-3 text-gray-300\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.5\" viewBox=\"0 0 24 24\">\n                                    <path d=\"M12 3L21 8V16L12 21L3 16V8L12 3Z\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linejoin=\"round\"/>\n                                    <path d=\"M12 3L3 8L12 13L21 8\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n                                    <path d=\"M12 13V21\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linecap=\"round\"/>\n                                    <path d=\"M7 10L4 12L7 14\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" fill=\"none\"/>\n                                    <path d=\"M4 12H10\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\"/>\n                                    <path d=\"M17 10L20 12L17 14\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" fill=\"none\"/>\n                                    <path d=\"M14 12H20\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\"/>\n                                </svg>";
$new2 = "<img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-12 h-12 mx-auto mb-3 object-contain opacity-40\" alt=\"Logo\">";
$c = str_replace($old2, $new2, $c);
file_put_contents($file, $c);
echo "products/index.blade.php\n";

// Fix products/show
$file = $base . 'products/show.blade.php';
$c = file_get_contents($file);
$old = "                            <svg class=\"w-20 h-20 text-accent/30\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.5\" viewBox=\"0 0 24 24\">\n                                <path d=\"M12 3L21 8V16L12 21L3 16V8L12 3Z\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linejoin=\"round\"/>\n                                <path d=\"M12 3L3 8L12 13L21 8\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n                                <path d=\"M12 13V21\" stroke=\"currentColor\" stroke-width=\"1.4\" stroke-linecap=\"round\"/>\n                                <path d=\"M7 10L4 12L7 14\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" fill=\"none\"/>\n                                <path d=\"M4 12H10\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\"/>\n                                <path d=\"M17 10L20 12L17 14\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\" fill=\"none\"/>\n                                <path d=\"M14 12H20\" stroke=\"currentColor\" stroke-width=\"1.5\" stroke-linecap=\"round\"/>\n                            </svg>";
$new = "<img src=\"{{ asset('images/logo-cube.png') }}\" class=\"w-20 h-20 object-contain opacity-30\" alt=\"Logo\">";
$c = str_replace($old, $new, $c);
file_put_contents($file, $c);
echo "products/show.blade.php\n";

echo "Done.\n";
