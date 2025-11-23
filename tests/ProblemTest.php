<?php

namespace Kerigard\LPSolve\Tests;

use Kerigard\LPSolve\Constraint;
use Kerigard\LPSolve\Problem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProblemTest extends TestCase
{
    /**
     * @param list<int|float> $objective
     * @param list<\Kerigard\LPSolve\Constraint> $constraints
     * @param list<int|float> $lowerBounds
     * @param list<int|float> $upperBounds
     * @param list<bool>|bool $integerVariables
     * @param list<bool>|bool $binaryVariables
     * @return void
     *
     * @dataProvider problems
     */
    #[DataProvider('problems')]
    public function test_problem_constructor(
        $objective,
        $constraints,
        $lowerBounds,
        $upperBounds,
        $integerVariables,
        $binaryVariables
    ) {
        $problem = new Problem(
            $objective,
            $constraints,
            $lowerBounds,
            $upperBounds,
            $integerVariables,
            $binaryVariables
        );

        $this->assertEquals($objective, $problem->getObjective());
        $this->assertEquals($constraints, $problem->getConstraints());
        $this->assertEquals($lowerBounds, $problem->getLowerBounds());
        $this->assertEquals($upperBounds, $problem->getUpperBounds());
        $this->assertEquals($integerVariables, $problem->getIntegerVariables());
        $this->assertEquals($binaryVariables, $problem->getBinaryVariables());
        $this->assertEquals(count($constraints), $problem->countRows());
        $this->assertEquals(count($objective), $problem->countCols());
    }

    /**
     * @param list<int|float> $objective
     * @param list<\Kerigard\LPSolve\Constraint> $constraints
     * @param list<int|float> $lowerBounds
     * @param list<int|float> $upperBounds
     * @param list<bool>|bool $integerVariables
     * @param list<bool>|bool $binaryVariables
     * @return void
     *
     * @dataProvider problems
     */
    #[DataProvider('problems')]
    public function test_problem_accessors(
        $objective,
        $constraints,
        $lowerBounds,
        $upperBounds,
        $integerVariables,
        $binaryVariables
    ) {
        $problem = new Problem();
        $problem
            ->setObjective($objective)
            ->setConstraints($constraints)
            ->setLowerBounds($lowerBounds)
            ->setUpperBounds($upperBounds)
            ->setIntegerVariables($integerVariables)
            ->setBinaryVariables($binaryVariables);

        $this->assertEquals($objective, $problem->getObjective());
        $this->assertEquals($constraints, $problem->getConstraints());
        $this->assertEquals($lowerBounds, $problem->getLowerBounds());
        $this->assertEquals($upperBounds, $problem->getUpperBounds());
        $this->assertEquals($integerVariables, $problem->getIntegerVariables());
        $this->assertEquals($binaryVariables, $problem->getBinaryVariables());
        $this->assertEquals(count($constraints), $problem->countRows());
        $this->assertEquals(count($objective), $problem->countCols());

        $testConstraint = new Constraint();
        $constraints[] = $testConstraint;

        $problem->addConstraint($testConstraint);

        $this->assertEquals($constraints, $problem->getConstraints());
        $this->assertEquals(count($constraints), $problem->countRows());
    }

    /**
     * @return list<array{
     *  list<int|float>,
     *  list<\Kerigard\LPSolve\Constraint>,
     *  list<int|float>,
     *  list<int|float>,
     *  list<bool>|bool,
     *  list<bool>|bool
     * }>
     */
    public static function problems()
    {
        return [
            [
                [1, 3, 6.24, 0.1],
                [
                    new Constraint([0, 78.26, 0, 2.9], GE, 92.3),
                    new Constraint([0.24, 0, 11.31, 0], LE, 14.8),
                    new Constraint([12.68, 0, 0.08, 0.9], GE, 4),
                ],
                [28.6, 0, 0, 18],
                [Infinite, Infinite, Infinite, 48.98],
                [],
                [],
            ],
            [
                [143, 60, 195],
                [
                    new Constraint([120, 210, 150.75], LE, 15000),
                    new Constraint([110, 30, 125], LE, 4000),
                    new Constraint([1, 1, 1], LE, 75),
                ],
                [],
                [],
                [],
                [],
            ],
            [
                [-1, -2, 0.1, 3],
                [
                    new Constraint([1, 1, 0, 0], LE, 5),
                    new Constraint([2, -1, 0, 0], GE, 0),
                    new Constraint([-1, 3, 0, 0], GE, 0),
                    new Constraint([0, 0, 1, 1], GE, 0.5),
                ],
                [0, 0, 1.1, 0],
                [],
                [false, false, true, false],
                [true, false, false, true],
            ],
        ];
    }
}
