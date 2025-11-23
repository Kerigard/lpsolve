<?php

namespace Kerigard\LPSolve\Tests;

use Kerigard\LPSolve\Constraint;
use Kerigard\LPSolve\Problem;
use Kerigard\LPSolve\Solution;
use Kerigard\LPSolve\Solver;
use LPSolveException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SolverTest extends TestCase
{
    /**
     * @return void
     */
    public function test_extension()
    {
        $this->assertTrue(function_exists('lpsolve'));
    }

    /**
     * @return void
     */
    public function test_solver_throw_exception()
    {
        $this->expectException(LPSolveException::class);
        $this->expectExceptionMessage('Objective function must be minimized or maximized');

        new Solver('Dummy objective direction');

        $this->expectExceptionMessage('Invalid vector');

        $problem = new Problem(
            [1],
            [
                new Constraint([0, 78.26, 0, 2.9], GE, 92.3),
            ]
        );

        $solver = new Solver();
        $solver->solve($problem);
    }

    /**
     * @param \Kerigard\LPSolve\Solver::MIN|\Kerigard\LPSolve\Solver::MAX $type
     * @param \Kerigard\LPSolve\Solution $expectedSolution
     * @return void
     *
     * @dataProvider problems
     */
    #[DataProvider('problems')]
    public function test_solver_success(Problem $problem, $type, $expectedSolution)
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
        $this->assertTrue(property_exists($solution, 'iterations'));

        $this->assertEquals(round($expectedSolution->getObjective(), 12), round($solution->getObjective(), 12));
        $this->assertEquals($expectedSolution->getCount(), $solution->getCount());
        $this->assertEquals($expectedSolution->getVariables(), array_map(function ($variable) {
            return round($variable, 12);
        }, $solution->getVariables()));
        $this->assertEquals($expectedSolution->getCode(), $solution->getCode());
        $this->assertEquals($expectedSolution->getStatus(), $solution->getStatus());
        $this->assertEquals($expectedSolution->getIterations(), $solution->getIterations());
    }

    /**
     * @return list<array{
     *  \Kerigard\LPSolve\Problem,
     *  string,
     *  \Kerigard\LPSolve\Solution
     * }>
     */
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
                    [Infinite, Infinite, Infinite, 48.98],
                    [],
                    []
                ),
                Solver::MIN,
                new Solution(31.78275862069, 1, [28.6, 0, 0, 31.827586206897], 0, 'OPTIMAL solution', 1),
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
                    [],
                    [],
                    []
                ),
                Solver::MAX,
                new Solution(6986.842105263158, 1, [0, 56.578947368421, 18.421052631579], 0, 'OPTIMAL solution', 2),
            ],
            [
                new Problem(
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
                    []
                ),
                Solver::MIN,
                new Solution(-8.1333333333330007, 1, [1.666666666667, 3.333333333333, 2, 0], 0, 'OPTIMAL solution', 2),
            ],
            [
                new Problem(
                    [-1, -2, 0.1, 3],
                    [
                        new Constraint([1, 1, 0, 0], LE, 5),
                        new Constraint([2, -1, 0, 0], GE, 0),
                        new Constraint([-1, 3, 0, 0], GE, 0),
                        new Constraint([0, 0, 1, 1], GE, 0.5),
                    ],
                    [0, 0, 1.1, 0],
                    [],
                    [false, 0, true, false],
                    [true, false, false, 1]
                ),
                Solver::MIN,
                new Solution(-4.8, 1, [1, 2, 2, 0], 0, 'OPTIMAL solution', 2),
            ],
            [
                new Problem(
                    [-1, -2, 0.1, 3],
                    [
                        new Constraint([1, 1, 0, 0], LE, 5),
                        new Constraint([2, -1, 0, 0], GE, 0),
                        new Constraint([-1, 3, 0, 0], GE, 0),
                        new Constraint([0, 0, 1, 1], GE, 0.5),
                    ],
                    [0, 0, 1.1, 0],
                    [],
                    false,
                    true
                ),
                Solver::MIN,
                new Solution(-2.9, 1, [1, 1, 1, 0], 0, 'OPTIMAL solution', 3),
            ],
            [
                new Problem([1]),
                Solver::MIN,
                new Solution(0, 1, [0], 0, 'OPTIMAL solution', 0),
            ],
        ];
    }

    /**
     * @return void
     */
    public function test_solver_failure()
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
        $this->assertEquals(2, $solution->getIterations());
    }

    /**
     * @return void
     */
    public function test_solver_failure_throw_exception()
    {
        $this->expectException(LPSolveException::class);
        $this->expectExceptionMessage('Model is primal INFEASIBLE');
        $this->expectExceptionCode(2);

        $problem = new Problem(
            [10, 10],
            [
                Constraint::fromString('1x + 1y = 20'),
                Constraint::fromString('0x + 1y <= 5'),
                Constraint::fromString('1x + 0y <= 5'),
            ]
        );

        $solver = new Solver(Solver::MIN);
        $solver->throw()->solve($problem);
    }

    /**
     * @return void
     */
    public function test_solver_callbacks()
    {
        $problem = new Problem(
            [1, 3, 6.24, 0.1],
            [
                new Constraint([0, 78.26, 0, 2.9], GE, 92.3),
                new Constraint([0.24, 0, 11.31, 0], LE, 14.8),
                new Constraint([12.68, 0, 0.08, 0.9], GE, 4),
            ]
        );
        $columns = $iterations = 0;

        $solver = new Solver();
        $solution = $solver->beforeSolve(function ($lpsolve, $problem) use (&$columns) {
            $columns = lpsolve('get_Ncolumns', $lpsolve);

            $this->assertEquals($columns, $problem->countCols());
        })->afterSolve(function ($lpsolve, $problem, $solution) use (&$iterations) {
            $iterations = lpsolve('get_total_iter', $lpsolve);

            $this->assertEquals($iterations, $solution->getIterations());
        })->solve($problem);

        $this->assertEquals(3.18275862069, round($solution->getObjective(), 12));
        $this->assertEquals(4, $columns);
        $this->assertEquals(1, $iterations);
    }
}
