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
     *
     * @return array
     */
    public function search(array $searchParams, array $services);
}
