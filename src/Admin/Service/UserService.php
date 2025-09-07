<?php

namespace App\Admin\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService {

    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function saveUser(User $user, ?string $plainPassword = null, string $actionType): void {
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info('Usuário ' . $actionType, [
            'id' => $user->getId(),
            'nome' => $user->getName(),
            'email' => $user->getUserIdentifier(),
        ]);
    }

    public function deleteUser(User $user): void {
        $userName = $user->getName();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->logger->critical('Usuário excluído', [
            'id' => $user->getId(),
            'nome' => $userName,
            'email' => $user->getUserIdentifier(),
        ]);
    }
}
