<?php

namespace Kerigard\LPSolve\Tests;

use Kerigard\LPSolve\Solution;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SolutionTest extends TestCase
{
    /**
     * @param int|float $objective
     * @param int $count
     * @param list<int|float> $variables
     * @param int $code
     * @param string $status
     * @param int $iterations
     * @return void
     *
     * @dataProvider solutions
     */
    #[DataProvider('solutions')]
    public function test_solution_object_constructor($objective, $count, $variables, $code, $status, $iterations)
    {
        $solution = new Solution($objective, $count, $variables, $code, $status, $iterations);

        $this->assertEquals($objective, $solution->getObjective());
        $this->assertEquals($count, $solution->getCount());
        $this->assertEquals($variables, $solution->getVariables());
        $this->assertEquals($code, $solution->getCode());
        $this->assertEquals($status, $solution->getStatus());
        $this->assertEquals($iterations, $solution->getIterations());
    }

    /**
     * @return list<array{
     *  int|float,
     *  int,
     *  list<int|float>,
     *  int,
     *  string,
     *  int
     * }>
     */
    public static function solutions()
    {
        return [
            [31.78275862069, 1, [28.6, 0, 0, 31.827586206897], 0, 'OPTIMAL solution', 1],
            [6986.8421052632, 1, [0, 56.578947368421, 18.421052631579], 0, 'OPTIMAL solution', 2],
            [1.7272337110189E-77, 0, [0, 0], 2, 'Model is primal INFEASIBLE', 0],
        ];
    }
}
