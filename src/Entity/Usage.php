<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsageRepository")
 * @ORM\Table(name="`usage`", uniqueConstraints={@ORM\UniqueConstraint(
 *     name="ip_date", columns={"`ip`", "`created_at`"})}
 * )
 */
final class Usage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="`id`")
     */
    private ?int $id = null;

    /** @ORM\Column(type="string", name="`ip`") */
    #[Assert\Ip]
    private ?string $ip = null;

    /** @ORM\Column(type="date", name="`created_at`") */
    #[Assert\Date]
    private ?DateTimeInterface $createdAt = null;

    /** @ORM\Column(type="integer", name="`amount`") */
    private int $amount = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function incrementAmount(): void
    {
        $this->amount++;
    }
}
