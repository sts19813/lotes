<?php

namespace App\Factories;

use App\Contracts\LotsDataSourceInterface;
use App\DataSources\AdaraDataSource;
use App\DataSources\NabooDataSource;

class LotsDataSourceFactory
{
    public static function make(string $source): LotsDataSourceInterface
    {
        return match ($source) {
            'naboo' => app(NabooDataSource::class),
            default => app(AdaraDataSource::class),
        };
    }
}
