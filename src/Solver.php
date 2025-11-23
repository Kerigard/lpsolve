<?php

namespace Kerigard\LPSolve;

use BadMethodCallException;
use Exception;
use LPSolveException;

/**
 * @method $this throw() Throw an exception if the optimal solution was not obtained.
 */
class Solver
{
    /**
     * Sets the objective function to minimize.
     */
    const MIN = 'set_minim';

    /**
     * Sets the objective function to maximize.
     */
    const MAX = 'set_maxim';

    /**
     * Exception class.
     *
     * @var class-string<Exception>
     */
    protected $exception = Exception::class;

    /**
     * Type of optimization (minimize or maximize).
     *
     * @var self::MIN|self::MAX
     */
    protected $type;

    /**
     * Scaling option.
     *
     * @var int
     */
    protected $scaling = SCALE_NONE;

    /**
     * Verbosity level.
     *
     * @var int
     */
    protected $verbose = IMPORTANT;

    /**
     * Time limit.
     *
     * @var int
     */
    protected $timeout = 0;

    /**
     * Throw an exception if the optimal solution was not obtained.
     *
     * @var bool
     */
    protected $throw = false;

    /**
     * Callback to run before solving the problem.
     *
     * @var (\Closure(mixed, \Kerigard\LPSolve\Problem): void)|null
     */
    protected $beforeCallback = null;

    /**
     * Callback to run after solving the problem.
     *
     * @var (\Closure(mixed, \Kerigard\LPSolve\Problem, \Kerigard\LPSolve\Solution): void)|null
     */
    protected $afterCallback = null;

    /**
     * @param self::MIN|self::MAX $type Type of optimization (minimize or maximize).
     *
     * @throws \LPSolveException
     * @throws \Exception
     */
    public function __construct($type = self::MIN)
    {
        if (class_exists('\LPSolveException')) {
            $this->exception = LPSolveException::class;
        }

        if (! function_exists('lpsolve')) {
            throw new $this->exception('Extension lpsolve not found');
        }
        if (! in_array($type, [self::MIN, self::MAX], true)) {
            throw new $this->exception('Objective function must be minimized or maximized');
        }

        $this->type = $type;
    }

    /**
     * Set scaling option.
     *
     * @param int $scaling Flags: SCALE_NONE, SCALE_EXTREME, SCALE_RANGE, SCALE_MEAN, SCALE_GEOMETRIC,
     *                     SCALE_CURTISREID, SCALE_QUADRATIC, SCALE_LOGARITHMIC, SCALE_USERWEIGHT, SCALE_POWER2,
     *                     SCALE_EQUILIBRATE, SCALE_INTEGERS, SCALE_DYNUPDATE, SCALE_ROWSONLY, SCALE_COLSONLY.
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
     * @param int $verbose Flags: NEUTRAL, CRITICAL, SEVERE, IMPORTANT, NORMAL, DETAILED, FULL.
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
     * Set number of seconds after which a timeout occurs. If zero, then no timeout will occur.
     *
     * @param int $seconds
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/set_timeout.htm
     */
    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Set callback before solve.
     *
     * @param \Closure(mixed, \Kerigard\LPSolve\Problem): void $callback
     * @return $this
     */
    public function beforeSolve($callback)
    {
        $this->beforeCallback = $callback;

        return $this;
    }

    /**
     * Set callback after solve.
     *
     * @param \Closure(mixed, \Kerigard\LPSolve\Problem, \Kerigard\LPSolve\Solution): void $callback
     * @return $this
     */
    public function afterSolve($callback)
    {
        $this->afterCallback = $callback;

        return $this;
    }

    /**
     * Solve problem.
     *
     * @param \Kerigard\LPSolve\Problem $problem Defined problem.
     * @return \Kerigard\LPSolve\Solution
     *
     * @throws \LPSolveException
     * @throws \Exception
     *
     * @link https://lpsolve.sourceforge.net/5.5/solve.htm
     */
    public function solve(Problem $problem)
    {
        $lpsolve = lpsolve('make_lp', 0, $columns = $problem->countCols());

        if (is_null($lpsolve)) {
            throw new $this->exception('Unable to create new LP model');
        }

        lpsolve('set_scaling', $lpsolve, $this->scaling);
        lpsolve('set_verbose', $lpsolve, $this->verbose);
        lpsolve('set_timeout', $lpsolve, $this->timeout);
        lpsolve('set_obj_fn', $lpsolve, array_values($problem->getObjective()));
        lpsolve($this->type, $lpsolve);

        foreach ($problem->getConstraints() as $constraint) {
            lpsolve(
                'add_constraint',
                $lpsolve,
                array_values($constraint->getCoefficients()),
                $constraint->getComparison(),
                $constraint->getValue()
            );
        }

        if ($problem->getLowerBounds()) {
            lpsolve('set_lowbo', $lpsolve, array_values($problem->getLowerBounds()));
        }
        if ($problem->getUpperBounds()) {
            lpsolve('set_upbo', $lpsolve, array_values($problem->getUpperBounds()));
        }

        $integerVariables = $problem->getIntegerVariables();
        if (is_array($integerVariables)) {
            foreach (array_values($integerVariables) as $key => $value) {
                if ($value) {
                    lpsolve('set_int', $lpsolve, $key + 1, 1);
                }
            }
        } elseif ($integerVariables) {
            for ($i = 1; $i <= $columns; $i++) {
                lpsolve('set_int', $lpsolve, $i, 1);
            }
        }

        $binaryVariables = $problem->getBinaryVariables();
        if (is_array($binaryVariables)) {
            foreach (array_values($binaryVariables) as $key => $value) {
                if ($value) {
                    lpsolve('set_binary', $lpsolve, $key + 1, 1);
                }
            }
        } elseif ($binaryVariables) {
            for ($i = 1; $i <= $columns; $i++) {
                lpsolve('set_binary', $lpsolve, $i, 1);
            }
        }

        if ($this->beforeCallback) {
            $this->beforeCallback->__invoke($lpsolve, $problem);
        }

        lpsolve('solve', $lpsolve);

        $variables = lpsolve('get_variables', $lpsolve);
        $variables = isset($variables[0]) ? $variables[0] : [];
        $solution = new Solution(
            lpsolve('get_working_objective', $lpsolve),
            lpsolve('get_solutioncount', $lpsolve),
            is_array($variables) ? $variables : [$variables],
            $statusCode = lpsolve('get_status', $lpsolve),
            lpsolve('get_statustext', $lpsolve, $statusCode),
            (int) lpsolve('get_total_iter', $lpsolve)
        );

        if ($this->afterCallback) {
            $this->afterCallback->__invoke($lpsolve, $problem, $solution);
        }

        lpsolve('delete_lp', $lpsolve);

        if ($this->throw && $solution->getCode() !== OPTIMAL) {
            throw new $this->exception($solution->getStatus(), $solution->getCode());
        }

        return $solution;
    }

    /**
     * @param string $method
     * @param list<mixed> $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, array $parameters)
    {
        if ($method === 'throw') {
            return $this->setThrow();
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            self::class,
            $method
        ));
    }

    /**
     * Throw an exception if the optimal solution was not obtained.
     *
     * @return $this
     */
    protected function setThrow()
    {
        $this->throw = true;

        return $this;
    }
}
