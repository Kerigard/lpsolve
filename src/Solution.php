<?php

namespace Kerigard\LPSolve;

class Solution
{
    /**
     * @var int|float
     */
    protected $objective;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var int[]|float[]
     */
    protected $variables;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $iterations;

    /**
     * @param int|float $objective Objective value
     * @param int $count Solutions count
     * @param int[]|float[] $variables Variables value
     * @param int $code Status code
     * @param string $status Status text
     * @param int $iterations Total number of iterations
     */
    public function __construct($objective, $count, array $variables, $code, $status, $iterations)
    {
        $this->objective = $objective;
        $this->count = $count;
        $this->variables = $variables;
        $this->code = $code;
        $this->status = $status;
        $this->iterations = $iterations;
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

    /**
     * @return int
     */
    public function getIterations()
    {
        return $this->iterations;
    }
}
