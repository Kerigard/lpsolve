<?php

namespace Kerigard\LPSolve;

/**
 * @link https://lpsolve.sourceforge.net/5.5/add_constraint.htm
 */
class Constraint
{
    /**
     * @var int[]|float[]
     */
    protected $coefficients = [];

    /**
     * @var int
     */
    protected $comparison;

    /**
     * @var int|float
     */
    protected $value;

    /**
     * @param int[]|float[] $coefficients Constraint left side
     * @param int $comparison Comparison sign: LE, GE, EQ
     * @param int|float $value Constraint right side
     */
    public function __construct(array $coefficients = [], $comparison = LE, $value = 0)
    {
        $this->coefficients = $coefficients;
        $this->comparison = $comparison;
        $this->value = $value;
    }

    /**
     * Create constraint from string.
     *
     * @param string $string String constraint
     * @return static
     */
    public static function fromString($string)
    {
        $split = preg_split('/(<=|=|>=)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);

        $coefficients = self::parseCoefficients($split[0]);
        $comparison = self::parseComparison($split[1]);
        $value = floatval($split[2]);

        return new static($coefficients, $comparison, $value);
    }

    /**
     * @return int[]|float[]
     */
    public function getCoefficients()
    {
        return $this->coefficients;
    }

    /**
     * @param int[]|float[] $coefficients
     * @return $this
     */
    public function setCoefficients(array $coefficients)
    {
        $this->coefficients = $coefficients;

        return $this;
    }

    /**
     * @return int
     */
    public function getComparison()
    {
        return $this->comparison;
    }

    /**
     * @param int $comparison
     * @return $this
     */
    public function setComparison($comparison)
    {
        $this->comparison = $comparison;

        return $this;
    }

    /**
     * @return int|float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int|float $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Parse coefficients from string.
     *
     * @param string $expression
     * @return float[]
     */
    protected static function parseCoefficients($expression)
    {
        $coefficients = [];
        $expression = preg_replace('/\s+/', '', $expression);
        $expression = preg_replace('/^([^+-])/', '+$1', $expression);
        $split = preg_split('/([a-zA-Z]+\d*)/', $expression, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($split as $coefficient) {
            $coefficients[] = floatval(preg_replace('/([\+-])$/', '${1}1', $coefficient));
        }

        return $coefficients;
    }

    /**
     * Parse comparison sign.
     *
     * @param string $comparison
     * @return int
     */
    protected static function parseComparison($comparison)
    {
        if ($comparison === '<=') {
            return LE;
        }
        if ($comparison === '>=') {
            return GE;
        }

        return EQ;
    }
}
