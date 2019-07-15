<?php

namespace InetStudio\SearchPackage\Search\Services\Front;

use Elasticsearch\ClientBuilder;
use InetStudio\SearchPackage\Search\Contracts\Services\Front\SearchServiceContract;

/**
 * Class SearchService.
 */
class SearchService implements SearchServiceContract
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
    public function search(array $searchParams, array $services, array $params, string $cacheKey = '')
    {
        if (isset($params['paging'])) {
            $searchParams['body']['from'] = $params['paging']['page']*$params['paging']['limit'];
            $searchParams['body']['size'] = $params['paging']['limit'];
        } else {
            $searchParams['body']['size'] = 9999;
        }

        $elastic = ClientBuilder::create()
            ->setHosts(config('scout.elasticsearch.hosts'))
            ->build();

        $response = $elastic->search($searchParams);

        $ids = [];
        $weights = [];

        collect($response['hits']['hits'])->each(function ($item) use (&$ids, &$weights) {
            $ids[$item['_source']['type']][] = $item['_source']['id'];
            $weights[$item['_score'].'_'.$item['_source']['type'].'_'.$item['_source']['id']] = [
                'type' => $item['_source']['type'],
                'id' => $item['_source']['id'],
            ];
        })->toArray();

        $items = collect([]);
        $unsortedItems = collect([]);

        foreach ($ids as $type => $itemIDs) {
            if (isset($services[$type])) {
                $searchItems = call_user_func_array([$services[$type]['service'], $services[$type]['method']], [$itemIDs, $services[$type]['params'], [$cacheKey]]);
                $unsortedItems = collect([$unsortedItems, $searchItems])->collapse();
            }
        }

        foreach ($weights as $weight => $itemData) {
            $searchItem = $unsortedItems->where('id', $itemData['id'])->where('type', $itemData['type'])->first();

            if ($searchItem) {
                $items->push($searchItem);
            }
        }

        return [
            'stop' => isset($params['paging']) && ($params['paging']['page'] + 1) * $params['paging']['limit'] >= $response['hits']['total']['value'],
            'count' => $response['hits']['total']['value'],
            'items' => $items,
        ];
    }
}
