<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Action\NotFoundAction;
use DateTime;
use DateTimeImmutable;
use ApiPlatform\Core\Annotation\ApiProperty;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"daily-stats:read"}},
 *    denormalizationContext={"groups"={"daily-stats:write"}},
 *    itemOperations={
 *         "get",
 *     "put",
 *     },
 *     collectionOperations={"get"},
 *    paginationItemsPerPage=7
 * )
 */
class DailyStats
{



    /**
     * @Groups({"daily-stats:read"})
     */
    public DateTimeInterface $date;

    /**
     * @Groups({"daily-stats:read", "daily-stats:write"})
     */
    public int $totalVisitors;

    /**
     *  The 5 most popular cheese listings from this date!
     * @Groups({"daily-stats:read"})
     * @var array<CheeseListing>|CheeseListing[]
     */
    public array $mostPopularListings;

    /**
     * @param array|CheeseListing[] $mostPopularListings
     */
    public function __construct(\DateTimeInterface $date, int $totalVisitors, array $mostPopularListings)
    {
        $this->date = $date;
        $this->totalVisitors = $totalVisitors;
        $this->mostPopularListings = $mostPopularListings;
    }


    /**
     * @ApiProperty(identifier=true)
     */
    public function getDateString(): string
    {
        return $this->date->format('Y-m-d');
    }
}
