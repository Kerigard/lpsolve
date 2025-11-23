<?php

namespace Kerigard\LPSolve;

class Problem
{
    /**
     * Array of objective coefficients.
     *
     * @var int[]|float[]
     */
    protected $objective = [];

    /**
     * Array of Constraint objects.
     *
     * @var \Kerigard\LPSolve\Constraint[]
     */
    protected $constraints = [];

    /**
     * Array of upper bounds coefficients.
     *
     * @var int[]|float[]
     */
    protected $upperBounds = [];

    /**
     * Array of lower bounds coefficients.
     *
     * @var int[]|float[]
     */
    protected $lowerBounds = [];

    /**
     * Array of integer variables.
     *
     * @var bool[]|int[]|bool
     */
    protected $integerVariables = [];

    /**
     * Array of binary variables.
     *
     * @var bool[]|int[]|bool
     */
    protected $binaryVariables = [];

    /**
     * @param int[]|float[] $objective Array of objective coefficients.
     * @param \Kerigard\LPSolve\Constraint[] $constraints Array of constraint objects.
     * @param int[]|float[] $lowerBounds Array of lower bounds coefficients.
     * @param int[]|float[] $upperBounds Array of upper bounds coefficients.
     * @param bool[]|int[]|bool $integerVariables Array of integer variables.
     * @param bool[]|int[]|bool $binaryVariables Array of binary variables.
     */
    public function __construct(
        array $objective = [],
        array $constraints = [],
        array $lowerBounds = [],
        array $upperBounds = [],
        $integerVariables = [],
        $binaryVariables = []
    ) {
        $this->objective = $objective;
        $this->constraints = $constraints;
        $this->lowerBounds = $lowerBounds;
        $this->upperBounds = $upperBounds;
        $this->integerVariables = $integerVariables;
        $this->binaryVariables = $binaryVariables;
    }

    /**
     * Get objective coefficients.
     *
     * @return int[]|float[]
     */
    public function getObjective()
    {
        return $this->objective;
    }

    /**
     * Set objective coefficients.
     *
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
     * Get constraint objects.
     *
     * @return \Kerigard\LPSolve\Constraint[]
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * Set constraint objects.
     *
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
     * Add constraint object.
     *
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
     * Get lower bounds coefficients.
     *
     * @return int[]|float[]
     */
    public function getLowerBounds()
    {
        return $this->lowerBounds;
    }

    /**
     * Set lower bounds coefficients.
     *
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
     * Get upper bounds coefficients.
     *
     * @return int[]|float[]
     */
    public function getUpperBounds()
    {
        return $this->upperBounds;
    }

    /**
     * Set upper bounds coefficients.
     *
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
     * Get integer variables.
     *
     * @return bool[]|int[]|bool
     */
    public function getIntegerVariables()
    {
        return $this->integerVariables;
    }

    /**
     * Set integer variables. If set to true, all variables will be integer.
     *
     * @param bool[]|int[]|bool $integerVariables
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/set_int.htm
     */
    public function setIntegerVariables($integerVariables)
    {
        $this->integerVariables = $integerVariables;

        return $this;
    }

    /**
     * Get binary variables.
     *
     * @return bool[]|int[]|bool
     */
    public function getBinaryVariables()
    {
        return $this->binaryVariables;
    }

    /**
     * Set binary variables. If set to true, all variables will be binary.
     *
     * A binary variable is an integer variable with a lower bound of 0 and an upper bound of 1.
     *
     * @param bool[]|int[]|bool $binaryVariables
     * @return $this
     *
     * @link https://lpsolve.sourceforge.net/5.5/set_binary.htm
     */
    public function setBinaryVariables($binaryVariables)
    {
        $this->binaryVariables = $binaryVariables;

        return $this;
    }

    /**
     * Get number of rows.
     *
     * @return int
     */
    public function countRows()
    {
        return count($this->constraints);
    }

    /**
     * Get number of columns.
     *
     * @return int
     */
    public function countCols()
    {
        return count($this->objective);
    }
}
