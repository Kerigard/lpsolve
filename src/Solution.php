<?php

namespace Kerigard\LPSolve;

class Solution
{
    /**
     * @var int|float
     */
    private $objective;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int[]|float[]
     */
    private $variables;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $status;

    /**
     * @param int|float $objective Objective value
     * @param int $count Solutions count
     * @param int[]|float[] $variables Variables value
     * @param int $code Status code
     * @param string $status Status text
     */
    public function __construct($objective, int $count, array $variables, int $code, string $status)
    {
        $this->objective = $objective;
        $this->count = $count;
        $this->variables = $variables;
        $this->code = $code;
        $this->status = $status;
    }

    /**
     * @return int|float
     */
    public function getObjective()
    {
        return $this->objective;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return int[]|float[]
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @return int Status codes: NOMEMORY, OPTIMAL, SUBOPTIMAL, INFEASIBLE, UNBOUNDED, DEGENERATE, NUMFAILURE,
     *             USERABORT, TIMEOUT, PRESOLVED, PROCFAIL, PROCBREAK, FEASFOUND, NOFEASFOUND
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
