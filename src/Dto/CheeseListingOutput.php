<?php


namespace App\Dto;

use App\Entity\User;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\Groups;

class CheeseListingOutput
{
    /**
     * @Groups({"cheese:read"}) * @Groups({"cheese:read", "user:read"})
     */
    public string  $title;

    /**
     * @var string
     * @Groups({"cheese:read"})
     */
    public $description;
    /**
     * @var integer
     * @Groups({"cheese:read", "user:read"})
     */
    public $price;

    /**
     
     * @Groups({"cheese:read"})
     */
    public User $owner;


    public $createdAt;



    /**
     * @Groups("cheese:read")
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40) {
            return $this->description;
        }

        return substr($this->description, 0, 40) . '...';
    }

    /**
     * How long ago in text that this cheese listing was added.
     *
     * @Groups("cheese:read")
     */
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }
}
