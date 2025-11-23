# LPSolve

<p align="center">
  <a href="https://github.com/Kerigard/lpsolve/actions"><img src="https://github.com/Kerigard/lpsolve/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/Kerigard/lpsolve"><img src="https://img.shields.io/packagist/dt/Kerigard/lpsolve" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/Kerigard/lpsolve"><img src="https://img.shields.io/packagist/v/Kerigard/lpsolve" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/Kerigard/lpsolve"><img src="https://img.shields.io/packagist/l/Kerigard/lpsolve" alt="License"></a>
</p>

PHP wrapper around [LPSolve 5.5](https://lpsolve.sourceforge.net), providing a safe and convenient interface for building and solving linear optimization problems.

## Installation

Install via Composer

```bash
composer require kerigard/lpsolve
```

Load the Composer autoloader

```php
require 'vendor/autoload.php'
```

> [!IMPORTANT]
> This library requires the lp_solve PHP extension.

> [!NOTE]
> The official lp_solve extension only supports PHP 5, so it needs to be compiled manually for modern PHP versions.

## Building the lp_solve extension

*(for PHP 5.6 - 8.x)*

The repository [Kerigard/lp-solve-php-docker](https://github.com/Kerigard/lp-solve-php-docker) contains maintained source directories for building the lp_solve extension for different PHP versions.:

| Branch | PHP version |
| ------ | ----------- |
| `5.x`  | PHP 5.x     |
| `7.x`  | PHP 7.x     |
| `8.x`  | PHP 8.x     |

### 1. Install lp-solve system package

```bash
apt update && apt install -y lp-solve
```

### 2. Download source code

```bash
curl -fsSL https://github.com/Kerigard/lp-solve-php-docker/archive/8.x.tar.gz \
    | tar -xz -C /tmp --strip-components=1
```

Replace `8.x` with the desired branch.

### 3. Build and install

```bash
(cd /tmp/lp-solve/extra/PHP && phpize && ./configure && make && make install)
rm -r /tmp/lp-solve
```

### 4. Enable the extension

Add to your `php.ini`:

```
extension=phplpsolve55.so
```

## Usage

### Maximization with constraints

```php
use Kerigard\LPSolve\Constraint;
use Kerigard\LPSolve\Problem;
use Kerigard\LPSolve\Solver;

$problem = new Problem(
    objective: [143, 60, 195],
    constraints: [
        new Constraint([120, 210, 150.75], LE, 15000),
        new Constraint([110, 30, 125], LE, 4000),
        new Constraint([1, 1, 1], LE, 75),
    ]
);

$solver = new Solver(Solver::MAX);
$solution = $solver->solve($problem);

var_dump($solution->getStatus()); // OPTIMAL solution
var_dump($solution->getCount()); // 1
var_dump($solution->getIterations()); // 2

if ($solution->getCode() === OPTIMAL) {
    var_dump($solution->getObjective()); // 6986.842105...
    var_dump($solution->getVariables()); // [0, 56.578947..., 18.421052...]
}
```

### Minimization with bounds

```php
$problem = new Problem(
    objective: [-1, -2, 0.1, 3],
    constraints: [
        new Constraint([1, 1, 0, 0], LE, 5),
        new Constraint([2, -1, 0, 0], GE, 0),
        new Constraint([-1, 3, 0, 0], GE, 0),
        new Constraint([0, 0, 1, 1], GE, 0.5),
    ],
    lowerBounds: [0, 0, 1.1, 0],
    upperBounds: []
);

$solver = new Solver(Solver::MIN);
$solution = $solver->setScaling(SCALE_MEAN | SCALE_INTEGERS)->solve($problem);

var_dump($solution->getObjective()); // -8.223333...
```

### Integer and Binary Variables

```php
$problem = new Problem(
    objective: [-1, -2, 0.1, 3],
    constraints: [
        new Constraint([1, 1, 0, 0], LE, 5),
        new Constraint([2, -1, 0, 0], GE, 0),
        new Constraint([-1, 3, 0, 0], GE, 0),
        new Constraint([0, 0, 1, 1], GE, 0.5),
    ],
    integerVariables: [0, 0, 1, 0], // integer variables (only variable #3 is integer)
    binaryVariables: [1, 0, 0, 1] // binary variables (variables #1 and #4 are binary)
);

$solver = new Solver(Solver::MIN);
$solution = $solver->setVerbose(DETAILED)->solve($problem);

var_dump($solution->getVariables()); // [1, 2, 1, 0]
```

If you pass `true` instead of an array, all variables will become integer or binary.

### Error handling

Invalid problem definition

```php
$problem = new Problem(
    [1],
    [new Constraint([0, 78.26, 0, 2.9], GE, 92.3)]
);

try {
    $solver = new Solver();
    $solver->setTimeout(0)->solve($problem);
} catch (\LPSolveException $e) {
    var_dump($e->getMessage()); // Invalid vector
}
```

Throwing exceptions for non-optimal solutions

```php
$problem = new Problem(
    [10, 10],
    [
        Constraint::fromString('1x + 1y = 20'),
        Constraint::fromString('0x + 1y <= 5'),
        Constraint::fromString('1x + 0y <= 5'),
    ]
);

try {
    $solver = new Solver(Solver::MIN);
    $solver->throw()->solve($problem);
} catch (\LPSolveException $e) {
    var_dump($e->getMessage()); // Model is primal INFEASIBLE
    var_dump($e->getCode() === INFEASIBLE); // true
}
```

Additional behavioral notes:

* The original lp_solve extension may produce **fatal errors** for malformed equations.
* When built via [Kerigard/lp-solve-php-docker](https://github.com/Kerigard/lp-solve-php-docker), internal errors become `LPSolveException`.
* Calling `->throw()` forces an exception for all non-optimal results.

### Callbacks

```php
$problem = new Problem(
    [1, 3, 6.24, 0.1],
    [
        new Constraint([0, 78.26, 0, 2.9], GE, 92.3),
        new Constraint([0.24, 0, 11.31, 0], LE, 14.8),
        new Constraint([12.68, 0, 0.08, 0.9], GE, 4),
    ]
);

$solver = (new Solver())
    ->beforeSolve(function ($lpsolve, $problem) {
        lpsolve('set_improve', $lpsolve, IMPROVE_SOLUTION);
    })
    ->afterSolve(function ($lpsolve, $problem, $solution) {
        lpsolve('write_lp', $lpsolve, 'model.lp');
    });

$solution = $solver->solve($problem);
```

## License

MIT. Please see the [LICENSE FILE](LICENSE.md) for more information.
