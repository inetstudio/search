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
     *
     * @return array
     */
    public function search(array $searchParams, array $services)
    {
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

        foreach ($ids as $type => $itemIds) {
            if (isset($services[$type])) {
                $searchItems = call_user_func_array([$services[$type]['service'], $services[$type]['method']], [$itemIds, $services[$type]['params']]);
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
            'count' => $response['hits']['total']['value'],
            'items' => $items,
        ];
    }
}
