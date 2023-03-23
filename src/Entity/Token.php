<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $token_id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $create_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTokenId(): ?string
    {
        return $this->token_id;
    }

    public function setTokenId(string $token_id): self
    {
        $this->token_id = $token_id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCreateDate(): ?int
    {
        return $this->create_date;
    }

    public function setCreateDate(int $create_date): self
    {
        $this->create_date = $create_date;

        return $this;
    }
}
