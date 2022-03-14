<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Usage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class UsageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usage::class);
    }

    public function save(Usage $usage): void
    {
        $this->_em->persist($usage);
        $this->_em->flush();
    }
}
