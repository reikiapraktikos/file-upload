<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Usage;
use App\Repository\UsageRepository;
use DateTimeInterface;

final class UsageHandler
{
    public function __construct(private UsageRepository $usageRepository)
    {
    }

    public function handle(string $ip, DateTimeInterface $date): void
    {
        $usage = $this->usageRepository->findOneBy(['ip' => $ip, 'createdAt' => $date]);

        if ($usage === null) {
            $usage = (new Usage())->setIp($ip)->setCreatedAt($date);
        }

        $usage->incrementAmount();
        $this->usageRepository->save($usage);
    }
}
