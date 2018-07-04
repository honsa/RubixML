<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use Rubix\ML\Pipeline;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\NeuralNet\Layers\Dense;
use Rubix\ML\Regressors\MLPRegressor;
use Rubix\ML\NeuralNet\Optimizers\Adam;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\NeuralNet\ActivationFunctions\LeakyReLU;
use Rubix\ML\CrossValidation\Reports\AggregateReport;
use Rubix\ML\CrossValidation\Reports\PredictionSpeed;
use Rubix\ML\CrossValidation\Reports\ResidualAnalysis;
use Rubix\ML\CrossValidation\Metrics\MeanSquaredError;
use League\Csv\Reader;

echo '╔═════════════════════════════════════════════════════╗' . "\n";
echo '║                                                     ║' . "\n";
echo '║ Wine Quality Tester using an MLP Regressor          ║' . "\n";
echo '║                                                     ║' . "\n";
echo '╚═════════════════════════════════════════════════════╝' . "\n";

echo  "\n";

$reader = Reader::createFromPath(dirname(__DIR__) . '/datasets/winequality-red.csv')
    ->setDelimiter(';')->setEnclosure('"')->setHeaderOffset(0);

$samples = iterator_to_array($reader->getRecords([
    'fixed_acidity', 'volatile_acidity', 'citric_acid', 'residual_sugar',
    'chlorides', 'free_sulfur_dioxide', 'total_sulfur_dioxide', 'density', 'pH',
    'sulphates', 'alcohol',
]));

$labels = iterator_to_array($reader->fetchColumn('quality'));

$dataset = new Labeled($samples, $labels);

$dataset->randomize();

$hidden = [
    new Dense(10, new LeakyReLU()),
    new Dense(10, new LeakyReLU()),
    new Dense(10, new LeakyReLU()),
    new Dense(10, new LeakyReLU()),
];

$estimator = new Pipeline(new MLPRegressor($hidden, 50, new Adam(0.001),
    1e-4, new MeanSquaredError(), 0.1, 3, 1e-3, 100), [
        new NumericStringConverter(),
    ]);

$report = new AggregateReport([
    new ResidualAnalysis(),
    new PredictionSpeed(),
]);

list($training, $testing) = $dataset->stratifiedSplit(0.8);

$estimator->train($training);

var_dump($estimator->progress());

var_dump($report->generate($estimator, $testing));

var_dump($estimator->predict($testing->head(3)));