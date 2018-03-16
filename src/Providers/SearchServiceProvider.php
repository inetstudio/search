<?php

namespace InetStudio\Search\Providers;

use Laravel\Scout\EngineManager;
use Illuminate\Support\ServiceProvider;
use InetStudio\Search\ElasticsearchEngine;
use Elasticsearch\ClientBuilder as ElasticBuilder;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot(): void
    {
        app(EngineManager::class)->extend('elasticsearch', function ($app) {
            return new ElasticsearchEngine(ElasticBuilder::create()
                ->setHosts(config('scout.elasticsearch.hosts'))
                ->build(),
                config('scout.elasticsearch.index')
            );
        });
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
    }
}
