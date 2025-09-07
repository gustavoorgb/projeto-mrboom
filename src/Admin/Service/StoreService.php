<?php

namespace App\Admin\Service;

use App\Entity\Store;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StoreService {

    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $entityManager) {
    }

    public function saveStore(Store $store, User $user, string $actionType): void {
        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $this->logger->info('Loja ' . $actionType, [
            'id' => $store->getId(),
            'nome' => $store->getCorporateName(),
            'user' => $user->getUserIdentifier(),
        ]);
    }

    public function deleteStore(Store $store, User $user): void {
        $storeName = $store->getCorporateName();

        $this->entityManager->remove($store);
        $this->entityManager->flush();

        $this->logger->critical('Loja excluída', [
            'id' => $store->getId(),
            'nome' => $storeName,
            'user' => $user->getUserIdentifier(),
        ]);
    }
}
