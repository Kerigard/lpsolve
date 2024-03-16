<?php

namespace Kerigard\LPSolve;

class Problem
{
    /**
     * @var int[]|float[]
     */
    protected $objective = [];

    /**
     * @var \Kerigard\LPSolve\Constraint[]
     */
    protected $constraints = [];

    /**
     * @var int[]|float[]
     */
    protected $upperBounds = [];

    /**
     * @var int[]|float[]
     */
    protected $lowerBounds = [];

    /**
     * @param int[]|float[] $objective Array of objective coefficients
     * @param \Kerigard\LPSolve\Constraint[] $constraints Array of Constraint objects
     * @param int[]|float[] $lowerBounds Array of lower bounds coefficients
     * @param int[]|float[] $upperBounds Array of upper bounds coefficients
     */
    public function __construct(
        array $objective = [],
        array $constraints = [],
        array $lowerBounds = [],
        array $upperBounds = []
    ) {
        $this->objective = $objective;
        $this->constraints = $constraints;
        $this->lowerBounds = $lowerBounds;
        $this->upperBounds = $upperBounds;
    }

    /**
     * @return int[]|float[]
     */
    public function getObjective()
    {
        return $this->objective;
    }

    /**
     * @param int[]|float[] $objective
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/set_obj_fn.htm
     */
    public function setObjective(array $objective)
    {
        $this->objective = $objective;

        return $this;
    }

    /**
     * @return \Kerigard\LPSolve\Constraint[]
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param \Kerigard\LPSolve\Constraint[] $constraints
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/add_constraint.htm
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/add_constraint.htm
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    /**
     * @return int[]|float[]
     */
    public function getLowerBounds()
    {
        return $this->lowerBounds;
    }

    /**
     * @param int[]|float[] $lowerBounds
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/set_lowbo.htm
     */
    public function setLowerBounds(array $lowerBounds)
    {
        $this->lowerBounds = $lowerBounds;

        return $this;
    }

    /**
     * @return int[]|float[]
     */
    public function getUpperBounds()
    {
        return $this->upperBounds;
    }

    /**
     * @param int[]|float[] $upperBounds
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/set_upbo.htm
     */
    public function setUpperBounds(array $upperBounds)
    {
        $this->upperBounds = $upperBounds;

        return $this;
    }

    /**
     * @return int
     */
    public function countRows()
    {
        return count($this->constraints);
    }

    /**
     * @return int
     */
    public function countCols()
    {
        return count($this->objective);
    }
}
