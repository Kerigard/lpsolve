# LPSolve

<p align="center">
  <a href="https://github.com/Kerigard/lpsolve/actions"><img src="https://github.com/Kerigard/lpsolve/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/Kerigard/lpsolve"><img src="https://img.shields.io/packagist/dt/Kerigard/lpsolve" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/Kerigard/lpsolve"><img src="https://img.shields.io/packagist/v/Kerigard/lpsolve" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/Kerigard/lpsolve"><img src="https://img.shields.io/packagist/l/Kerigard/lpsolve" alt="License"></a>
</p>

[LPSolve](https://lpsolve.sourceforge.net) is a PHP extension for solving linear programming problems. This library provides a wrapper for standard lpsolve() function.

## Installation

Run composer

```bash
composer require kerigard/lpsolve
```

Require autoloader

```php
require 'vendor/autoload.php'
```

## Usage

```php
use Kerigard\LPSolve\Constraint;
use Kerigard\LPSolve\Problem;
use Kerigard\LPSolve\Solver;

// Define constraints
$constraints = [
    new Constraint([120, 210, 150.75], LE, 15000),
    new Constraint([110, 30, 125], LE, 4000),
    new Constraint([1, 1, 1], LE, 75)
];

// Or initialize them from string
// $constraints = [
//     Constraint::fromString('120x + 210y + 150.75z <= 15000'),
//     Constraint::fromString('110x + 30y + 125z <= 4000'),
//     Constraint::fromString('x + y + z <= 75')
// ];

// Define problem
$problem = new Problem([143, 60, 195], $constraints);

// Solve it!
$solver = new Solver(Solver::MAX); // Can be either Solver::MIN for minimization
$solution = $solver->solve($problem);

var_dump($solution);
```

> [!NOTE]
> Do not omit coefficients when create constraint from string.

For more information please visit: https://lpsolve.sourceforge.net

## License

[MIT](LICENSE.md)
