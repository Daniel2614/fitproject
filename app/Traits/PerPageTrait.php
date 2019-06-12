<?php

namespace App\Traits;

trait PerPageTrait
{
    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return env('PAGINATION_LIMIT', 10);
    }
}
