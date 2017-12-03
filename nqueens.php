<?php
// N-Queens problem: put N Queens on a chess board NxN
// sized such that they aren't at risk of capture
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
        'column' => [],
        'down' => [],
        'up' => [],
    ];

    public function __construct(int $size)
    {
        $this->size = $size;
        $this->result = array_fill(0, $size, null);
    }

    public function solve(int $queenNum = 0, $row = 0): bool
    {
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
            $this->result[$row] = $col;
            $this->used['column'][$col] = true;
            $this->used['down'][$down] = true;
            $this->used['up'][$up] = true;


            // If last queen or subsequent queens have been placed, return
            if(($queenNum === $this->size - 1)
                || $this->solve($queenNum + 1, $row + 1) === true
            ) {
                return true;
            }

            // otherwise, if we get here we've backtracked and have to try replacing this queen
            $this->result[$row] = null;

            $this->used['column'][$col] = false;
            $this->used['down'][$down] = false;
            $this->used['up'][$up] = false;
        }

        return false;
    }

    // Rudimentary printing method
    public function display()
    {
        for($row = 0; $row < $this->size; $row++)
        {
            $sep = '-';
            for($col = 0; $col < $this->size; $col++)
            {
                $sep .= '----';    // for every column add 4 dashes to then print below the row
                echo '| ';

                echo $this->result[$row] === $col ? 'Q ' : '  ';
            }

            echo "|\r\n";
            echo $sep . "\n";    // print the seperator row -------
        }
    }
}

// Run main ...
$queens = new Queens($_SERVER['argv'][1]);
$queens->solve();
$queens->display();
