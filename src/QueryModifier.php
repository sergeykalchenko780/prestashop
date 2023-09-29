<?php

namespace PrestaShop\Module\ExtendOrderGrid;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class QueryModifier
{
    public function addFieldsForOrderGrid(array $params) : void
    {
        /** @var QueryBuilder $qb */
        $qb = $params['search_query_builder'];
        $this->extendQuery($qb);

        /** @var SearchCriteriaInterface $search_criteria */
        $search_criteria = $params['search_criteria'];
        $this->extendFiltersAndSort($search_criteria, $qb);
    }

    private function extendQuery(QueryBuilder $qb)
    {
        $qb
            ->leftJoin('o', 'ps_carrier', 'ca', 'ca.id_carrier = o.id_carrier')
            ->leftJoin('o', 'ps_carrier_lang', 'cal', 'cal.id_carrier = ca.id_carrier AND cal.id_lang = :context_lang_id')
            ->addSelect(['delay'])
        ;
    }

    private function extendFiltersAndSort(SearchCriteriaInterface $search_criteria, QueryBuilder $qb) : void
    {
        $filters = $search_criteria->getFilters();

        if ($filters) {
            foreach ($filters as $field => $filter) {
                if ($field === 'delay') {
                    $qb->andWhere($qb->expr()->like('delay', '"%' . $filter . '%"'));
                }
            }
        }

        if ($search_criteria->getOrderBy() && $search_criteria->getOrderBy() === 'delay'
            && $search_criteria->getOrderWay()) {
            $qb->orderBy($search_criteria->getOrderBy(), $search_criteria->getOrderWay());
        }
    }
}
