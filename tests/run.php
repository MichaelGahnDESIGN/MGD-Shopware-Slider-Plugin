<?php

declare(strict_types=1);

// Mini Test ohne Abhängigkeiten: php tests/run.php
require __DIR__ . '/../src/Core/Config.php';
require __DIR__ . '/../src/Core/Limits.php';
require __DIR__ . '/../src/Core/Prime.php';
require __DIR__ . '/../src/Core/Renderer.php';

use MGD\Slider\Config;
use MGD\Slider\Limits;
use MGD\Slider\Prime;
use MGD\Slider\Renderer;

$fails = 0;
function check(string $name, bool $ok): void
{
    global $fails;
    echo ($ok ? 'OK    ' : 'FEHLER ') . $name . "\n";
    $fails += $ok ? 0 : 1;
}

// Limit Free
check('Limit 5: 4 vorhanden > anlegen ok', Limits::canCreate(4));
check('Limit 5: 5 vorhanden > abgelehnt', !Limits::canCreate(5));
check('Limit Meldung deutsch', str_contains(Limits::reachedMessage(), 'Limit von 5 Slidern'));

// Config bereinigen
$c = Config::normalize(['mode' => 'evil', 'interval' => 1, 'accent' => 'red', 'prime' => ['count' => 99]]);
check('mode Fallback classic', $c['mode'] === 'classic');
check('interval min 2000', $c['interval'] === 2000);
check('accent ungueltig leer', $c['accent'] === '');
check('prime count max 12', $c['prime']['count'] === 12);
check('javascript: URL raus', Config::url('javascript:alert(1)') === '');
check('relative URL bleibt', Config::url('/media/a.jpg') === '/media/a.jpg');

// Prime
$cands = [];
for ($i = 1; $i <= 8; $i++) {
    $cands[] = ['id' => (string) $i, 'name' => "A$i", 'url' => "/a$i", 'image' => "/i$i.jpg", 'sales' => $i, 'inStock' => $i !== 3];
}
$cfg = Config::normalize(['mode' => 'prime', 'prime' => ['source' => 'bestseller', 'count' => 3]]);
$s = Prime::select($cands, $cfg);
check('Bestseller: 3 Slides', count($s) === 3);
check('Bestseller: staerkster zuerst (A8)', $s[0]['title'] === 'A8');
check('Ausverkauft (A3) nie dabei', !in_array('A3', array_column(Prime::select($cands, Config::normalize(['prime' => ['count' => 12]])), 'title'), true));

// Renderer + Vertrag
$html = Renderer::render(Config::normalize(['autoplay' => true, 'label' => 'Test']), $s, '1');
check('section.mgd-slider', str_contains($html, '<section id="mgd-slider-1" class="mgd-slider'));
check('aria carousel', str_contains($html, 'aria-roledescription="carousel"'));
check('erstes Bild fetchpriority', str_contains($html, 'fetchpriority="high"'));
check('leer > kein HTML', Renderer::render($cfg, [], '1') === '');
$xss = Renderer::render($cfg, [['image' => '/x.jpg', 'imageSet' => '', 'alt' => '"><script>', 'title' => '<b>x</b>', 'text' => '', 'buttonLabel' => '', 'buttonUrl' => '', 'overlayColor' => '', 'textColor' => '']], '1');
check('Alt wird escaped', !str_contains($xss, '"><script>'));

exit($fails === 0 ? 0 : 1);
