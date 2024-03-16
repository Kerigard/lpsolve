<?php

namespace Kerigard\LPSolve;

use Exception;

class Solver
{
    const MIN = 'set_minim';

    const MAX = 'set_maxim';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $scaling = SCALE_NONE;

    /**
     * @var int
     */
    protected $verbose = IMPORTANT;

    /**
     * @param \Kerigard\LPSolve\Solver::MIN|\Kerigard\LPSolve\Solver::MAX $type Type of optimization (minimize or maximize)
     *
     * @throws \Exception
     */
    public function __construct($type = self::MIN)
    {
        if (!function_exists('lpsolve')) {
            throw new Exception('Extension lpsolve not found');
        }
        if (!in_array($type, [self::MIN, self::MAX], true)) {
            throw new Exception('Objective function must be minimized or maximized');
        }

        $this->type = $type;
    }

    /**
     * Set scaling option.
     *
     * @param int $scaling Flags: SCALE_NONE, SCALE_EXTREME, SCALE_RANGE, SCALE_MEAN, SCALE_GEOMETRIC,
     *                     SCALE_CURTISREID, SCALE_QUADRATIC, SCALE_LOGARITHMIC, SCALE_USERWEIGHT, SCALE_POWER2,
     *                     SCALE_EQUILIBRATE, SCALE_INTEGERS, SCALE_DYNUPDATE, SCALE_ROWSONLY, SCALE_COLSONLY
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/set_scaling.htm
     */
    public function setScaling($scaling)
    {
        $this->scaling = $scaling;

        return $this;
    }

    /**
     * Set verbose level.
     *
     * @param int $verbose Flag: NEUTRAL, CRITICAL, SEVERE, IMPORTANT, NORMAL, DETAILED, FULL
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/set_verbose.htm
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;

        return $this;
    }

    /**
     * Solve problem.
     *
     * @param \Kerigard\LPSolve\Problem $problem Defined problem
     * @return \Kerigard\LPSolve\Solution
     */
    public function solve(Problem $problem)
    {
        $lpsolve = lpsolve('make_lp', 0, $problem->countCols());

        lpsolve('set_verbose', $lpsolve, $this->verbose);
        lpsolve('set_obj_fn', $lpsolve, $problem->getObjective());
        lpsolve($this->type, $lpsolve);

        foreach ($problem->getConstraints() as $constraint) {
            lpsolve(
                'add_constraint',
                $lpsolve,
                $constraint->getCoefficients(),
                $constraint->getComparison(),
                $constraint->getValue()
            );
        }

        if ($problem->getLowerBounds()) {
            lpsolve('set_lowbo', $lpsolve, $problem->getLowerBounds());
        }
        if ($problem->getUpperBounds()) {
            lpsolve('set_upbo', $lpsolve, $problem->getUpperBounds());
        }
        if ($this->scaling) {
            lpsolve('set_scaling', $lpsolve, $this->scaling);
        }

        lpsolve('solve', $lpsolve);

        $solution = new Solution(
            lpsolve('get_working_objective', $lpsolve),
            lpsolve('get_solutioncount', $lpsolve),
            lpsolve('get_variables', $lpsolve)[0],
            $statusCode = lpsolve('get_status', $lpsolve),
            lpsolve('get_statustext', $lpsolve, $statusCode),
            (int) lpsolve('get_total_iter', $lpsolve)
        );

        lpsolve('delete_lp', $lpsolve);

        return $solution;
    }
}
