<?php

namespace Rubix\ML\Tests\Clusterers;

use Rubix\ML\Learner;
use Rubix\ML\Verbose;
use Rubix\ML\Estimator;
use Rubix\ML\Persistable;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Other\Loggers\BlackHole;
use Rubix\ML\Other\Helpers\DataType;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Kernels\Distance\Euclidean;
use Rubix\ML\Datasets\Generators\Agglomerate;
use Rubix\ML\CrossValidation\Metrics\VMeasure;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

class KMeansTest extends TestCase
{
    protected const TRAIN_SIZE = 400;
    protected const TEST_SIZE = 10;
    protected const MIN_SCORE = 0.9;

    protected $generator;

    protected $estimator;

    protected $metric;

    public function setUp()
    {
        $this->generator = new Agglomerate([
            'red' => new Blob([255, 0, 0], 30.),
            'green' => new Blob([0, 128, 0], 10.),
            'blue' => new Blob([0, 0, 255], 20.),
        ]);

        $this->estimator = new KMeans(3, new Euclidean(), 300);

        $this->estimator->setLogger(new BlackHole());

        $this->metric = new VMeasure();
    }

    public function test_build_clusterer()
    {
        $this->assertInstanceOf(KMeans::class, $this->estimator);
        $this->assertInstanceOf(Learner::class, $this->estimator);
        $this->assertInstanceOf(Persistable::class, $this->estimator);
        $this->assertInstanceOf(Verbose::class, $this->estimator);
        $this->assertInstanceOf(Estimator::class, $this->estimator);

        $this->assertEquals(Estimator::CLUSTERER, $this->estimator->type());

        $this->assertNotContains(DataType::CATEGORICAL, $this->estimator->compatibility());
        $this->assertContains(DataType::CONTINUOUS, $this->estimator->compatibility());

        $this->assertFalse($this->estimator->trained());
    }

    public function test_train_partial_predict()
    {
        $training = $this->generator->generate(self::TRAIN_SIZE);
        
        $testing = $this->generator->generate(self::TEST_SIZE);

        $this->estimator->train($training);

        $this->assertTrue($this->estimator->trained());

        $predictions = $this->estimator->predict($testing);

        $score = $this->metric->score($predictions, $testing->labels());

        $this->assertGreaterThanOrEqual(self::MIN_SCORE, $score);
    }

    public function test_train_incompatible()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->estimator->train(Unlabeled::quick([['bad']]));
    }

    public function test_predict_untrained()
    {
        $this->expectException(RuntimeException::class);

        $this->estimator->predict(Unlabeled::quick([[1.0]]));
    }
}
