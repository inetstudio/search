<?php

namespace InetStudio\SearchPackage\Search\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class BindingsServiceProvider.
 */
class BindingsServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * @var  array
     */
    public $bindings = [
        'InetStudio\SearchPackage\Search\Contracts\Engines\ElasticSearchEngineContract' => 'InetStudio\SearchPackage\Search\Engines\ElasticSearchEngine',
        'InetStudio\SearchPackage\Search\Contracts\Services\Front\SearchServiceContract' => 'InetStudio\SearchPackage\Search\Services\Front\SearchService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
