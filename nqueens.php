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
        for($col = 0; $col < $this->size; $col++) {
            $down = $this->size - 1 - $row + $col;
            $up = $row + $col;

            //echo "row: $row; col: $col; down: $down; up: $up",PHP_EOL;
            if(($this->used['column'][$col] ?? false) === true
                || ($this->used['down'][$down] ?? false) === true
                || ($this->used['up'][$up] ?? false) === true
            ) {
                continue;
            }

            // if this cell is allowed, set the queen here
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

    private function assign(int $row, int $col, bool $value): void
    {
        $this->stats['assignments']++;
        $this->result[$row] = $col;
        $this->used['row'][$row] = $value;
        $this->used['column'][$col] = $value;
        $this->used['down'][$this->size - 1 - $row + $col] = $value;
        $this->used['up'][$row + $col] = $value;
    }

    // Rudimentary printing method
    public function display()
    {
        $this->stats['queens-placed'] = 0;
        for($row = 0; $row < $this->size; $row++)
        {
            $sep = '-';
            for($col = 0; $col < $this->size; $col++)
            {
                $sep .= '----';    // for every column add 4 dashes to then print below the row
                echo '| ';
                $this->stats['queens-placed'] += (int)($this->result[$row] === $col);
                echo $this->result[$row] === $col ? 'Q ' : '  ';
            }

            echo "|\r\n";
            echo $sep . "\n";    // print the seperator row -------
        }

        echo '== Stats ==', PHP_EOL;
        foreach ($this->stats as $key => $value) {
            echo $key, ': ', number_format($value), PHP_EOL;
        }
    }
}

// Run main ...
$queens = new Queens($_SERVER['argv'][1]);
$queens->optimize();
$queens->solve();
$queens->display();
