<?php

declare(strict_types=1);

/**
 * PROBE
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

it('probes', function (): void {
    $log = 'C:/Users/kings/AppData/Local/Temp/claude/bd530b16d6127274/3a11cf57-0587-464b-85c2-f9bf9dfd6335/scratchpad/probe.txt';
    $admin = $this->person('administrator');
    for ($i = 1; $i <= 3; $i++) {
        $this->publish('Story number '.$i.' about wolves');
    }
    $this->theme(__DIR__.'/..')->activate();
    $out = [];
    $this->withoutExceptionHandling();
    foreach (['/2026/page/2/', '/2025/', '/author/person-2/', '/author/person-1/', '/author/person-2/page/2/', '/?s=wolves&x=1', '/?s=%3Cb%3Ex'] as $p) {
        try {
            $r = $this->fetch($p);
            $c = $r->getContent();
            $out[] = $p.' => '.$r->getStatusCode().' tpl='.(preg_match('/data-template="([^"]*)"/', $c, $m) ? $m[1] : '-').' h1='.substr_count($c, '<h1').' cards='.substr_count($c, 'story card');
        } catch (Throwable $e) { $out[] = $p.' EXC '.substr($e->getMessage(), 0, 300); }
    }
    foreach (['/author/person-2/', '/2026/', '/author/person-1/', '/2025/', '/?s=%3Cb%3Ex'] as $p) {
        $s = $this->fetch($p)->getContent();
        $a = (int) strpos($s, '<main');
        $out[] = $p."\n".substr($s, $a, strpos($s, '</main>') - $a);
    }
    foreach (['error', 'index'] as $v) {
        $php = app('blade.compiler')->compileString(file_get_contents(__DIR__.'/../views/skins/fable/'.$v.'.blade.php'));
        try { token_get_all($php, TOKEN_PARSE); $out[] = $v.' parses'; } catch (Throwable $e) { $out[] = $v.' PARSE FAIL '.$e->getMessage(); }
    }
    file_put_contents($log, implode("\n=====\n", $out));
    expect(true)->toBeTrue();
});
