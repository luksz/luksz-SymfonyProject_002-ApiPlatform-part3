<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Service\StatsHelper;
use IteratorAggregate;
use Traversable;

class DailyStatsPaginator implements PaginatorInterface, IteratorAggregate

{
    private $dailyStatsIterator;
    private $statsHelper;
    protected int $currentPage;

    protected int $maxResults;

    public function __construct(StatsHelper $statsHelper, int $currentPage, int $maxResults)
    {
        $this->statsHelper = $statsHelper;

        $this->currentPage = $currentPage;
        $this->maxResults = $maxResults;
    }

    public function getLastPage(): float
    {
        return ceil($this->getTotalItems() / $this->getItemsPerPage()) ?: 1.;
    }
    public function getTotalItems(): float
    {
        return $this->statsHelper->count();
    }
    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }
    public function getItemsPerPage(): float
    {
        return $this->maxResults;
    }
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }
    public function getIterator(): Traversable
    {
        if ($this->dailyStatsIterator === null) {
            $offset = (($this->getCurrentPage() - 1) * $this->getItemsPerPage());
            $this->dailyStatsIterator = new \ArrayIterator(
                $this->statsHelper->fetchMany(
                    $this->getItemsPerPage(),
                    $offset
                )
            );
        }

        return $this->dailyStatsIterator;
    }
}
