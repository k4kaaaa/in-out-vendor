<?php
$src = 'C:\Users\kaka\Downloads\Logo Kubus Hijau IN-OUT VENDOR.png';
$im = imagecreatefrompng($src);
$w = imagesx($im);
$h = imagesy($im);

// find bounding box of non-white pixels
$minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if ($r < 245 || $g < 245 || $b < 245) {
            if ($x < $minX) $minX = $x;
            if ($x > $maxX) $maxX = $x;
            if ($y < $minY) $minY = $y;
            if ($y > $maxY) $maxY = $y;
        }
    }
}

// text is in lower part; cut at 66% of height to keep only cube
$cutY = (int)($h * 0.66);
if ($maxY > $cutY) $maxY = $cutY;

$pad = 20;
$x = max(0, $minX - $pad);
$y = max(0, $minY - $pad);
$cw = min($w - $x, $maxX - $minX + $pad * 2);
$ch = min($h - $y, $maxY - $minY + $pad * 2);

$cube = imagecreatetruecolor($cw, $ch);
imagesavealpha($cube, true);
$trans = imagecolorallocatealpha($cube, 0, 0, 0, 127);
imagefill($cube, 0, 0, $trans);
imagecopy($cube, $im, 0, 0, $x, $y, $cw, $ch);

// make white background transparent
for ($yy = 0; $yy < $ch; $yy++) {
    for ($xx = 0; $xx < $cw; $xx++) {
        $rgba = imagecolorat($cube, $xx, $yy);
        $a = ($rgba >> 24) & 0x7F;
        if ($a === 127) continue;
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        if ($r > 240 && $g > 240 && $b > 240) {
            imagesetpixel($cube, $xx, $yy, imagecolorallocatealpha($cube, 255, 255, 255, 127));
        }
    }
}

// square canvas, cube centered
$size = max($cw, $ch);
$sq = imagecreatetruecolor($size, $size);
imagesavealpha($sq, true);
$trans2 = imagecolorallocatealpha($sq, 0, 0, 0, 127);
imagefill($sq, 0, 0, $trans2);
imagecopy($sq, $cube, (int)(($size - $cw) / 2), (int)(($size - $ch) / 2), 0, 0, $cw, $ch);

// resize to 320x320
$out = imagecreatetruecolor(320, 320);
imagesavealpha($out, true);
$trans3 = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefill($out, 0, 0, $trans3);
imagecopyresampled($out, $sq, 0, 0, 0, 0, 320, 320, $size, $size);

imagepng($out, 'public/images/logo-cube.png');
echo "crop x=$x y=$y w=$cw h=$ch size=$size -> logo-cube.png\n";
