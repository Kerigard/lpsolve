<?php

namespace Kerigard\LPSolve;

class Solution
{
    /**
     * Objective value.
     *
     * @var int|float
     */
    protected $objective;

    /**
     * Solutions count.
     *
     * @var int
     */
    protected $count;

    /**
     * Variables value.
     *
     * @var list<int|float>
     */
    protected $variables;

    /**
     * Status code.
     *
     * @var int
     */
    protected $code;

    /**
     * Status text.
     *
     * @var string
     */
    protected $status;

    /**
     * Total number of iterations.
     *
     * @var int
     */
    protected $iterations;

    /**
     * @param int|float $objective Objective value.
     * @param int $count Solutions count.
     * @param list<int|float> $variables Variables value.
     * @param int $code Status code.
     * @param string $status Status text.
     * @param int $iterations Total number of iterations.
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
     * Value of the objective function.
     *
     * @return int|float
     */
    public function getObjective()
    {
        return $this->objective;
    }

    /**
     * Number of solutions found.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Values of the variables.
     *
     * @return list<int|float>
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Status code.
     *
     * @return int Status codes: NOMEMORY, OPTIMAL, SUBOPTIMAL, INFEASIBLE, UNBOUNDED, DEGENERATE, NUMFAILURE,
     *             USERABORT, TIMEOUT, PRESOLVED, PROCFAIL, PROCBREAK, FEASFOUND, NOFEASFOUND
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Description of the status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Total number of iterations.
     *
     * @return int
     */
    public function getIterations()
    {
        return $this->iterations;
    }
}
