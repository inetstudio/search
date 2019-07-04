<?php

namespace InetStudio\SearchPackage\Search\Contracts\Services\Front;

/**
 * Interface SearchServiceContract.
 */
interface SearchServiceContract
{
    /**
     * Выполняем поиск.
     *
     * @param array $searchParams
     * @param array $services
     * @param array $params
     * @param string $cacheKey
     *
     * @return array
     */
    public function search(array $searchParams, array $services, array $params, string $cacheKey = '');
}
