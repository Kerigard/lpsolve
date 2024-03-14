<?php

namespace Kerigard\LPSolve\Tests;

use Kerigard\LPSolve\Solution;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SolutionTest extends TestCase
{
    /**
     * @dataProvider solutions
     */
    #[DataProvider('solutions')]
    public function testSolutionObjectConstructor($objective, $count, $variables, $code, $status)
    {
        $solution = new Solution($objective, $count, $variables, $code, $status);

        $this->assertEquals($objective, $solution->getObjective());
        $this->assertEquals($count, $solution->getCount());
        $this->assertEquals($variables, $solution->getVariables());
        $this->assertEquals($code, $solution->getCode());
        $this->assertEquals($status, $solution->getStatus());
    }

    public static function solutions()
    {
        return [
            [31.78275862069, 1, [28.6, 0, 0, 31.827586206897], 0, 'OPTIMAL solution'],
            [6986.8421052632, 1, [0, 56.578947368421, 18.421052631579], 0, 'OPTIMAL solution'],
            [1.7272337110189E-77, 0, [0, 0], 2, 'Model is primal INFEASIBLE'],
        ];
    }
}
