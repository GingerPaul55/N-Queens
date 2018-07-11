<?php declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

error_reporting(E_ALL & ~E_NOTICE);

$ns = [
    1, 2, 3, 4, 5, 6, 7, 8,   // Easy
    12, 14, 16, 18, 19, 21,   // Medium
    37, 42, 51, 63, 100//, 112, // Hard
    // Impossible
];

$solutions = 0;
foreach ($ns as $n) {
    echo 'n='.$n.';';
    $queens = new NQueens\NQueens($n);
    $before = microtime(true);
    $queens->optimize();
    $queens->solve();
    $after = microtime(true);
    $time = number_format($after - $before, 2);
    $stats = $queens->getStats();
    $stats['time'] = $time;
    $solved = $stats['queens-placed'] === $n;
    $stats['solved'] = $solved ? 'SOLVED' : 'UNSOLVED';
    foreach ($stats as $key => $value) {
        echo ' '.$key.': '.$value.';';
    }
    $solutions+= (int)$solved;
    echo PHP_EOL;

    unset($queens);
}

echo 'Solved '.$solutions.'/'.count($ns),PHP_EOL;
die(count($ns) - $solutions - 2);
