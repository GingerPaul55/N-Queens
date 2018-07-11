<?php

require __DIR__.'/../vendor/autoload.php';

// Run main ...
$queens = new NQueens\NQueens((int)$_SERVER['argv'][1]);
$before = microtime(true);
$queens->optimize();
$queens->solve();
$queens->display();
$queens->displayStats();
$after = microtime(true);
echo 'Total: '.number_format($after - $before, 2), "seconds\n";
