<?php

namespace InetStudio\SearchPackage\Search\Providers;

use Laravel\Scout\EngineManager;
use Elasticsearch\ClientBuilder as ElasticBuilder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Загрузка сервиса.
     */
    public function boot(): void
    {
        app(EngineManager::class)->extend('elasticsearch', function () {
            return app()->make(
                'InetStudio\SearchPackage\Search\Contracts\Engines\ElasticSearchEngineContract',
                [
                    'elastic' => ElasticBuilder::create()
                        ->setHosts(config('scout.elasticsearch.hosts'))
                        ->build(),
                    'index' => config('scout.elasticsearch.index'),
                ]
            );
        });
    }
}
