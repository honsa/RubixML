<?php

namespace Rubix\ML;

use Rubix\ML\Datasets\Dataset;

interface Probabilistic extends Estimator
{
    /**
     * Output a vector of probabilities estimates.
     *
     * @param  \Rubix\ML\Datasets\Dataset  $dataset
     * @return array
     */
    public function proba(Dataset $dataset) : array;
}