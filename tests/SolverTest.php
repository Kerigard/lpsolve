<?php

namespace Kerigard\LPSolve\Tests;

use Kerigard\LPSolve\Solver;
use Kerigard\LPSolve\Problem;
use Kerigard\LPSolve\Solution;
use Kerigard\LPSolve\Constraint;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SolverTest extends TestCase
{
    public function testExtension()
    {
        $this->assertTrue(function_exists('lpsolve'));
    }

    public function testSolverTypeException()
    {
        $this->expectException(\Exception::class);

        new Solver('Dummy objective direction');
    }

    /**
     * @dataProvider problems
     */
    #[DataProvider('problems')]
    public function testSolverSuccess(Problem $problem, $type, $expectedSolution)
    {
        $solver = new Solver($type);
        $solver->setScaling(SCALE_MEAN | SCALE_INTEGERS)->setVerbose(NEUTRAL);
        $solution = $solver->solve($problem);

        $this->assertInstanceOf(Solution::class, $solution);
        $this->assertTrue(property_exists($solution, 'objective'));
        $this->assertTrue(property_exists($solution, 'count'));
        $this->assertTrue(property_exists($solution, 'variables'));
        $this->assertTrue(property_exists($solution, 'code'));
        $this->assertTrue(property_exists($solution, 'status'));

        $this->assertEquals($expectedSolution->getObjective(), round($solution->getObjective(), 12));
        $this->assertEquals($expectedSolution->getCount(), $solution->getCount());
        $this->assertEquals($expectedSolution->getVariables(), array_map(function ($variable) {
            return round($variable, 12);
        }, $solution->getVariables()));
        $this->assertEquals($expectedSolution->getCode(), $solution->getCode());
        $this->assertEquals($expectedSolution->getStatus(), $solution->getStatus());
    }

    public static function problems()
    {
        return [
            [
                new Problem(
                    [1, 3, 6.24, 0.1],
                    [
                        new Constraint([0, 78.26, 0, 2.9], GE, 92.3),
                        new Constraint([0.24, 0, 11.31, 0], LE, 14.8),
                        new Constraint([12.68, 0, 0.08, 0.9], GE, 4),
                    ],
                    [28.6, 0, 0, 18],
                    [Infinite, Infinite, Infinite, 48.98]
                ),
                Solver::MIN,
                new Solution(31.78275862069, 1, [28.6, 0, 0, 31.827586206897], 0, 'OPTIMAL solution'),
            ],
            [
                new Problem(
                    [143, 60, 195],
                    [
                        new Constraint([120, 210, 150.75], LE, 15000),
                        new Constraint([110, 30, 125], LE, 4000),
                        new Constraint([1, 1, 1], LE, 75),
                    ],
                    [],
                    []
                ),
                Solver::MAX,
                new Solution(6986.842105263158, 1, [0, 56.578947368421, 18.421052631579], 0, 'OPTIMAL solution'),
            ],
        ];
    }

    public function testSolverFailure()
    {
        $problem = new Problem(
            [10, 10],
            [
                Constraint::fromString('1x + 1y = 20'),
                Constraint::fromString('0x + 1y <= 5'),
                Constraint::fromString('1x + 0y <= 5'),
            ]
        );

        $solver = new Solver(Solver::MIN);
        $solution = $solver->solve($problem);

        $this->assertEquals(0, $solution->getCount());
        $this->assertEquals(2, $solution->getCode());
        $this->assertEquals('Model is primal INFEASIBLE', $solution->getStatus());
    }
}
