<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Service\Omines;

use App\Service\Omines\Filter\DateFilter;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Literal;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\QueryBuilderProcessorInterface;
use Omines\DataTablesBundle\Column\AbstractColumn;
use Omines\DataTablesBundle\DataTableState;

/**
 * SearchCriteriaProvider.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class SwSearchCriteriaProvider implements QueryBuilderProcessorInterface {
    /**
     * {@inheritdoc}
     */
    public function process(QueryBuilder $queryBuilder, DataTableState $state)
    {
        $this->processSearchColumns($queryBuilder, $state);
        $this->processGlobalSearch($queryBuilder, $state);
    }

    private function processSearchColumns(QueryBuilder $queryBuilder, DataTableState $state)
    {
       
        foreach ($state->getSearchColumns() as $searchInfo) {
            /** @var AbstractColumn $column */
            $column = $searchInfo['column'];
            $search = $searchInfo['search'];
            

            if ('' !== trim($search)) {
                if (null !== ($filter = $column->getFilter())) {
                    if (!$filter->isValidValue($search)) {
                        continue;
                    }
                }
               
                $operator = strtolower($column->getOperator());
                $isDate = $column->getFilter() instanceof DateFilter;

                if ($isDate) {
                    $method = 'eq';
                    $field = "DATE({$column->getField()})";
                    $operator = 'eq';
                } else {
                    if (in_array($operator, ['like', 'rlike', 'llike'])) {
                        $method = 'like';
                    } elseif ($operator == 'eq') {
                        $method = $operator;
                    } else {
                        $method = 'like';
                    }
                    $field = $column->getField();
                }

                switch ($operator) {
                    case 'like':
                        $search = $queryBuilder->expr()->literal('%'.addcslashes($search, '%_').'%');
                        break;
                    case 'rlike':
                        $search = $queryBuilder->expr()->literal(addcslashes($search, '%_').'%');
                        break;
                    case 'llike':
                        $search = $queryBuilder->expr()->literal('%'.addcslashes($search, '%_'));
                        break;
                    case 'eq':
                    case 'neq':
                    case 'gt':
                    case 'lt':
                        $search = $queryBuilder->expr()->literal($search);
                        break;
                    default:
                       $search = $this->getDefaultSearchValue($isDate, $queryBuilder, $search);  
                }

                $queryBuilder->andWhere($queryBuilder->expr()->$method($field, $search));
            }
        }
    }


    private function getDefaultSearchValue($isDate, $queryBuilder, $search) : Literal
    {
        if (!$isDate) {
            $search = $queryBuilder->expr()->literal('%'.addcslashes($search, '%_').'%');
        } else {
            $search = $queryBuilder->expr()->literal($search);
        }
        return $search;
    }

    private function processGlobalSearch(QueryBuilder $queryBuilder, DataTableState $state)
    {
       
        if (!empty($globalSearch = $state->getGlobalSearch())) {
            $expr = $queryBuilder->expr();
            $comparisons = $expr->orX();
            foreach ($state->getDataTable()->getColumns() as $column) {
                if ($column->isGlobalSearchable() && !empty($column->getField()) && $column->isValidForSearch($globalSearch)) {
                    $comparisons->add(new Comparison($column->getLeftExpr(), $column->getOperator(),
                        $expr->literal($column->getRightExpr($globalSearch))));
                }
            }
            $queryBuilder->andWhere($comparisons);
        }
    }
}
