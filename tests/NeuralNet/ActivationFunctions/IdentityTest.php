<?php

namespace Rubix\Tests\NeuralNet\ActivationFunctions;

use MathPHP\LinearAlgebra\Matrix;
use Rubix\ML\NeuralNet\ActivationFunctions\Identity;
use Rubix\ML\NeuralNet\ActivationFunctions\ActivationFunction;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    protected $input;

    protected $activationFunction;

    public function setUp()
    {
        $this->input = new Matrix([[1.0], [-0.5], [0.0], [20.0], [-10.0]]);

        $this->activationFunction = new Identity();
    }

    public function test_build_activation_function()
    {
        $this->assertInstanceOf(Identity::class, $this->activationFunction);
        $this->assertInstanceOf(ActivationFunction::class, $this->activationFunction);
    }

    public function test_get_range()
    {
        $this->assertEquals([-INF, INF], $this->activationFunction->range());
    }

    public function test_compute()
    {
        $activations = $this->activationFunction->compute($this->input);

        $this->assertEquals(1.0, $activations[0][0]);
        $this->assertEquals(-0.5, $activations[1][0]);
        $this->assertEquals(0.0, $activations[2][0]);
        $this->assertEquals(20.0, $activations[3][0]);
        $this->assertEquals(-10.0, $activations[4][0]);
    }

    public function test_differentiate()
    {
        $activations = $this->activationFunction->compute($this->input);

        $derivatives = $this->activationFunction->differentiate($this->input, $activations);

        $this->assertEquals(1.0, $derivatives[0][0]);
        $this->assertEquals(1.0, $derivatives[1][0]);
        $this->assertEquals(1.0, $derivatives[2][0]);
        $this->assertEquals(1.0, $derivatives[3][0]);
        $this->assertEquals(1.0, $derivatives[4][0]);
    }

    public function test_within_range()
    {
        list($min, $max) = $this->activationFunction->range();

        $activations = $this->activationFunction->compute($this->input);

        $this->assertThat($activations[0][0], $this->logicalAnd(
            $this->greaterThanOrEqual($min), $this->lessThanOrEqual($max))
        );

        $this->assertThat($activations[1][0], $this->logicalAnd(
            $this->greaterThanOrEqual($min), $this->lessThanOrEqual($max))
        );

        $this->assertThat($activations[2][0], $this->logicalAnd(
            $this->greaterThanOrEqual($min), $this->lessThanOrEqual($max))
        );

        $this->assertThat($activations[3][0], $this->logicalAnd(
            $this->greaterThanOrEqual($min), $this->lessThanOrEqual($max))
        );

        $this->assertThat($activations[4][0], $this->logicalAnd(
            $this->greaterThanOrEqual($min), $this->lessThanOrEqual($max))
        );
    }
}