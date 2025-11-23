<?php

namespace Kerigard\LPSolve;

use InvalidArgumentException;

/**
 * @link https://lpsolve.sourceforge.net/5.5/add_constraint.htm
 */
class Constraint
{
    /**
     * Constraint left side.
     *
     * @var int[]|float[]
     */
    protected $coefficients = [];

    /**
     * Comparison sign.
     *
     * @var LE|GE|EQ
     */
    protected $comparison;

    /**
     * Constraint right side.
     *
     * @var int|float
     */
    protected $value;

    /**
     * @param int[]|float[] $coefficients Constraint left side.
     * @param LE|GE|EQ $comparison Comparison sign.
     * @param int|float $value Constraint right side.
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
     * @param string $string String constraint.
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public static function fromString($string)
    {
        $split = preg_split('/(<=|=|>=)/', trim($string), -1, PREG_SPLIT_DELIM_CAPTURE);

        if (! $split || count($split) !== 3) {
            throw new InvalidArgumentException('Invalid constraint string.');
        }

        $coefficients = self::parseCoefficients($split[0]);
        $comparison = self::parseComparison($split[1]);
        $value = (float) $split[2];

        return new static($coefficients, $comparison, $value);
    }

    /**
     * Get constraint left side.
     *
     * @return int[]|float[]
     */
    public function getCoefficients()
    {
        return $this->coefficients;
    }

    /**
     * Set constraint left side.
     *
     * @param int[]|float[] $coefficients
     * @return $this
     */
    public function setCoefficients(array $coefficients)
    {
        $this->coefficients = $coefficients;

        return $this;
    }

    /**
     * Get comparison sign.
     *
     * @return LE|GE|EQ
     */
    public function getComparison()
    {
        return $this->comparison;
    }

    /**
     * Set comparison sign.
     *
     * @param LE|GE|EQ $comparison
     * @return $this
     */
    public function setComparison($comparison)
    {
        $this->comparison = $comparison;

        return $this;
    }

    /**
     * Get constraint right side.
     *
     * @return int|float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set constraint right side.
     *
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
     * @return list<float>
     */
    protected static function parseCoefficients($expression)
    {
        $coefficients = [];
        $expression = preg_replace('/\s+/', '', $expression);

        if (is_null($expression)) {
            return $coefficients;
        }

        $expression = preg_replace('/^([^+-])/', '+$1', $expression);

        if (is_null($expression)) {
            return $coefficients;
        }

        $split = preg_split('/([a-zA-Z]+\d*)/', $expression, -1, PREG_SPLIT_NO_EMPTY);

        if ($split === false) {
            return $coefficients;
        }

        foreach ($split as $coefficient) {
            $coefficients[] = (float) preg_replace('/([\+-])$/', '${1}1', $coefficient);
        }

        return $coefficients;
    }

    /**
     * Parse comparison sign.
     *
     * @param string $comparison
     * @return LE|GE|EQ
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
