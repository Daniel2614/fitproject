<?php

namespace App\Filters\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Apply all filters on the model.
     *
     * @param Builder $query
     * @return Builder
     * @throws Exception
     */
    public function scopeFilter($query)
    {
        if (! class_exists($this->filters)) {
            throw new Exception($this->filters.' does not exist.');
        }

        return app()->make($this->filters)->apply($query);
    }
}
