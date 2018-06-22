<?php
/**
 * N-Queens problem: put N Queens on a chess board NxN
 * sized such that they aren't at risk of capture.
 */
class Queens
{
    /**
     * @var int[]
     */
    private $result = [];

    /**
     * @var int
     */
    private $size;

    /**
     * Each column and diagonal is currently in use or not is in here.
     * These prevents the need to manually check is row, column or diagonal.
     * Much quicker!
     *
     * @var bool[][]
     */
    private $used = [
        'row' => [],
        'column' => [],
        'down' => [],
        'up' => [],
        'cells' => [],
        'score' => 0,
        'total' => 0,
    ];

    /**
     * @var int[]
     */
    private $stats = [
        'assignments' => 0,
        'backtracks' => 0,
        'queens-placed' => 0,
    ];

    public function __construct(int $size)
    {
        $this->size = $size;
        $this->result = array_fill(0, $size, null);
        $this->used['cells'] = array_fill(0, $size, array_fill(0, $size, 0));
    }

    public function optimize()
    {
        if ($this->size < 19) {
            return;
        }

        $this->assign($this->size - 1, 0, true);
        $this->assign($this->size - 2, 2, true);
        $this->assign($this->size - 3, 4, true);
        $this->assign($this->size - 4, 1, true);
        $this->assign($this->size - 5, 3, true);
    }

    public function solve()
    {
        return $this->doSolve(count($this->used['row']), 0);
    }

    private function doSolve(int $queenNum, int $row): bool
    {
        if (($this->used['row'][$row] ?? false) === true) {
            if(($queenNum === $this->size - 1)
                || $this->doSolve($queenNum + 1, $row + 1) === true
            ) {
                return true;
            }
            return false;
        }
        $options = [];
        for($col = 0; $col < $this->size; $col++) {
            $down = $this->size - 1 - $row + $col;
            $up = $row + $col;

            // This cell is in use by another Queen.
            if(($this->used['column'][$col] ?? false) === true
                || ($this->used['down'][$down] ?? false) === true
                || ($this->used['up'][$up] ?? false) === true
            ) {
                continue;
            }

            // if this cell is allowed, set the queen here
            $this->assign($row, $col, true);
            if ($this->used['score'] !== false) {
                $options[$col] = $this->used['score'];
            }
            $this->assign($row, $col, false);
        }

        asort($options);

        foreach ($options as $col => $score) {
            $this->assign($row, $col, true);
            // If last queen or subsequent queens have been placed, return
            if(($queenNum === $this->size - 1)
                || $this->doSolve($queenNum + 1, $row + 1) === true
            ) {
                return true;
            }

            // otherwise, if we get here we've backtracked and have to try replacing this queen
            $this->stats['backtracks']++;

            $this->assign($row, $col, false);
        }

        return false;
    }

    public function assign(int $row, int $col, bool $value): void
    {
        $newValue = $value ? $col : null;
        if ($this->result[$row] === $newValue) {
            return;
        }
        $difference = $value ? 1 : -1;
        $this->stats['assignments']++;
        $this->result[$row] = $newValue;
        $this->used['total']+= $difference;
        $this->used['row'][$row] = $value;
        $this->used['column'][$col] = $value;
        $this->used['down'][$this->size - 1 - $row + $col] = $value;
        $this->used['up'][$row + $col] = $value;

        $cells = [];
        for ($i = 0; $i < $this->size; $i++) {
            $cells[$col][$i] += $difference;
            $cells[$i][$row] += $difference;

            // Down
            $x = $col - $i;
            $y = $row - $i;
            if (isset($this->used['cells'][$x][$y])) {
                $cells[$x][$y] += $difference;
            }

            $x = $col + $i;
            $y = $row + $i;
            if (isset($this->used['cells'][$x][$y])) {
                $cells[$x][$y] += $difference;
            }

            // Up
            $x = $col - $i;
            $y = $row + $i;
            if (isset($this->used['cells'][$x][$y])) {
                $cells[$x][$y] += $difference;
            }

            $x = $col + $i;
            $y = $row - $i;
            if (isset($this->used['cells'][$x][$y])) {
                $cells[$x][$y] += $difference;
            }
        }

        // Work out which squares are free and if it's possible for the rest of queens to be put in place
        $score = 0;
        foreach ($cells as $x => $ys) {
            foreach ($ys as $y => $difference) {
                if (!isset($this->used['cells'][$x][$y])) {
                    continue;
                }
                $this->used['cells'][$x][$y]+= $difference;
                //$this->used['icells'][$y][$x] = $this->used['cells'][$x][$y];
                $score+= $this->used['cells'][$x][$y] * $this->used['cells'][$x][$y];
            }
        }

        for ($i = 0; $i < $this->size; $i++) {
            if (!in_array($i, $this->result, true) === null) {
                $usedXs = count(array_filter($this->used['cells'][$i]));
                if ($usedXs === $this->size) {
                    $score = false;
                    break;
                }
            }

            if ($this->result[$i] === null) {
                $usedYs = [];
                for ($ii = 0; $ii < $this->size; $ii++) {
                    $usedYs[$ii] = $this->used['cells'][$ii][$i];
                }
                $usedYs = count(array_filter($usedYs));

                if ($usedYs === $this->size) {
                    $score = false;
                    break;
                }
            }
        }

        $this->used['score'] = $score;
    }

    // Rudimentary printing method
    public function display()
    {
        $this->stats['queens-placed'] = 0;
        $sep = str_repeat('-', $this->size * 4 + 1);
        for($row = 0; $row < $this->size; $row++)
        {
            echo $sep, PHP_EOL;
            for($col = 0; $col < $this->size; $col++)
            {
                echo '| ';
                $this->stats['queens-placed'] += (int)($this->result[$row] === $col);
                //echo $this->result[$row] === $col ? 'Q ' : ((int)$this->used['cells'][$col][$row]).' '; // Dev
                echo $this->result[$row] === $col ? 'Q ' : '  ';
            }

            echo '|', PHP_EOL;
        }
        echo $sep, PHP_EOL;
    }

    public function displayStats()
    {
        echo '== Stats ==', PHP_EOL;
        foreach ($this->stats as $key => $value) {
            echo $key, ': ', number_format($value), PHP_EOL;
        }
    }
}

// Run main ...
$queens = new Queens($_SERVER['argv'][1]);
$before = microtime(true);
$queens->optimize();
$queens->solve();
$queens->display();
$queens->displayStats();
$after = microtime(true);
echo 'Total: '.number_format($after - $before, 2), "seconds\n";
