<?php 

namespace App\Libraries\Ghivarra;

/**
 * Datatable Library
 *
 * Created with love and proud by Ghivarra Senandika Rushdie
 *
 * @package Datatable Library
 *
 * @var https://github.com/ghivarra
 * @var https://facebook.com/bcvgr
 * @var https://twitter.com/ghivarra
 *
**/

class Datatable
{
    // needed
    private $tableName;
    private $orm;
    private $selectedColumns;
    private $joinParams;
    private $getAllData;
    private $limit;
    private $offset;
    private $orders;
    private $columnQuery;
    private $drawCount;
    
    // optional
    private $defaultParams;
    private $searchParams;
    private $drawMessage;
    private $drawStatus;

    //=====================================================================================================

    public function fetch(array $options): array
    {
        // store needed option parameters
        $this->tableName       = $options['tableName'];
        $this->orm             = $options['orm'];
        $this->selectedColumns = $options['selectedColumns'];
        $this->joinParams      = $options['joinParams'];
        $this->getAllData      = $options['getAllData'];
        $this->limit           = $options['limit'];
        $this->offset          = $options['offset'];
        $this->drawCount       = $options['drawCount'];
        $this->orders          = $options['orders'];
        $this->columnQuery     = $options['columnQuery'];

        // store optional parameters
        $this->defaultParams = isset($options['defaultParams']) ? $options['defaultParams'] : [];
        $this->searchParams  = isset($options['searchParams']) ? $options['searchParams'] : [];
        $this->drawMessage   = isset($options['drawMessage']) ? $options['drawMessage'] : 'Data berhasil ditarik';
        $this->drawStatus    = isset($options['drawStatus']) ? $options['drawStatus'] : 'success';

        // get total
        $total = $this->orm->countAllResults();

        // build query
        $currentORM = $this->orm->select($this->selectedColumns);
        $currentORM = $this->buildJoin($currentORM);
        $currentORM = $this->buildOrder($currentORM);

        if (!empty($this->defaultParams))
        {
            $currentORM = $this->buildDefaultParameter($currentORM);
        }

        // build search query
        $currentORM = $this->buildSearchQuery($currentORM);

        // get filtered total
        $filteredTotal = $currentORM->countAllResults(false);

        // build limit
        if (!$this->getAllData)
        {
            $currentORM = $currentORM->limit($this->limit, $this->offset);
        }

        // get data
        $data = $currentORM->find();

        // return
        return [
            'status'  => $this->drawStatus,
            'message' => $this->drawMessage,
            'data'    => [
                'draw'            => $this->drawCount,
                'length'          => count($data),
                'recordsTotal'    => $total,
                'recordsFiltered' => $filteredTotal,
                'row'             => numbering($data, $this->offset)
            ]
        ];
    }

    //=====================================================================================================

    private function buildDefaultParameter($currentORM)
    {
        foreach ($this->defaultParams as $param):

            $currentORM = $currentORM->$param['method'](... $param['options']);

        endforeach;

        // return
        return $currentORM;
    }

    //=====================================================================================================

    private function buildJoin($currentORM)
    {
        // joins
        if (!empty($this->joinParams))
        {
            foreach ($this->joinParams as $param):

                $currentORM = isset($param['type']) ? $currentORM->join($param['table'], $param['options'], $param['type']) : $currentORM->join($param['table'], $param['options']);

            endforeach;
        }

        // return
        return $currentORM;
    }

    //=====================================================================================================

    private function buildOrder($currentORM)
    {
        // orders
        if (!empty($this->orders))
        {
            foreach ($this->orders as $param):

                $currentORM = $currentORM->orderBy($param['column'], $param['order']);

            endforeach;
        }

        // return
        return $currentORM;
    }

    //=====================================================================================================

    private function buildSearchQueryDefault($currentORM)
    {
        // build query
        foreach ($this->columnQuery as $column):

            if (strlen($column['query']) > 0)
            {
                if (str_contains($column['key'], '.'))
                {
                    $currentORM = $currentORM->like($column['key'], $column['query'], 'both', null, true);

                } else {
                    
                    $currentORM = $currentORM->like("{$this->tableName}.{$column['key']}", $column['query'], 'both', null, true);
                }   
            }

        endforeach;

        // return
        return $currentORM;
    }

    //=====================================================================================================

    private function buildSearchQuery($currentORM)
    {
        // if empty return immediately
        if (empty($this->searchParams))
        {
            // return
            return $this->buildSearchQueryDefault($currentORM);
        }

        // set all search params types
        $types = [
            'is',
            'isLike',
            'isNotLike',
            'isIn',
            'isNotIn'
        ];

        // parse all search options
        $searchParamKeys = array_keys($this->searchParams);

        // build query
        foreach ($this->columnQuery as $column):

            if (strlen($column['query']) > 0)
            {
                if (in_array($column['key'], $searchParamKeys))
                {
                    if (!in_array($this->searchParams[$column['key']]['type'], $types))
                    {
                        throw new \Exception('Wrong types in datatable search parameters.');
                    }

                    // set search key and value
                    $searchType  = $this->searchParams[$column['key']]['type'];
                    $searchKey   = isset($this->searchParams[$column['key']]['targetKey']) ? $this->searchParams[$column['key']]['targetKey'] : $column['key'];
                    $searchValue = isset($this->searchParams[$column['key']]['targetOptions']) ? $this->searchParams[$column['key']]['targetOptions'] : $column['query'];

                    // switch up!
                    switch ($searchType) {
                        case 'is':
                            $currentORM = $currentORM->where($searchKey, $searchValue);
                            break;

                        case 'isLike':
                            $currentORM = $currentORM->like($searchKey, $searchValue, 'both', null, true);
                            break;
                        
                        case 'isNotLike':
                            $currentORM = $currentORM->notLike($searchKey, $searchValue, 'both', null, true);
                            break;

                        case 'isIn':
                            $currentORM = $currentORM->whereIn($searchKey, $searchValue);
                            break;

                        case 'isNotIn':
                            $currentORM = $currentORM->whereNotIn($searchKey, $searchValue);
                            break;
                    }

                } else {

                    if (str_contains($column['key'], '.'))
                    {
                        $currentORM = $currentORM->like($column['key'], $column['query'], 'both', null, true);
    
                    } else {
                        
                        $currentORM = $currentORM->like("{$this->tableName}.{$column['key']}", $column['query'], 'both', null, true);
                    }   
                }
            }

        endforeach;

        // return
        return $currentORM;
    }

    //=====================================================================================================
}