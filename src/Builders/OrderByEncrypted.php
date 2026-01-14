<?php

namespace Canaryau\LaravelEncryption\Builders;

trait OrderByEncrypted
{
    public function orderByEncrypted($column, $direction = 'desc')
    {
        $results = $this->get();

        $results = $results->sortBy(function ($result) use ($column) {
            return $result->$column;
        }, SORT_REGULAR, $direction === 'desc');

        return $results;
    }
}
