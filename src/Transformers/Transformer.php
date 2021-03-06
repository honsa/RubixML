<?php

namespace Rubix\ML\Transformers;

interface Transformer
{
    public const EPSILON = 1e-8;

    /**
     * Transform the dataset in place.
     *
     * @param array $samples
     */
    public function transform(array &$samples) : void;
}
