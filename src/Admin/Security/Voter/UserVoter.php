<?php

namespace App\Admin\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter {

    public const EDIT = 'user_edit';
    public const VIEW = 'user_view';
    public const CREATE = 'user_create';
    public const DELETE = 'user_delete';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }


    protected function supports(string $attribute, $subject): bool {
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            and $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token, ?Vote $vote = null): bool {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User) {
            $vote?->addReason('O usuário não esta logado.');
            return false;
        }
        //--> falta implementar as roles
        switch ($attribute) {
            case self::EDIT:
            case self::VIEW:
            case self::CREATE:
                // Permite se o usuário for ROLE_USER_MANAGER
                return $this->accessDecisionManager->decide($token, ['ROLE_USER_MANAGER']);

            case self::DELETE:
                // Permite se o usuário for ROLE_USER_MANAGER e não for o próprio usuário logado
                return $this->accessDecisionManager->decide($token, ['ROLE_USER_MANAGER']) && $currentUser->getId() !== $subject->getId();
        }
        return false;
    }
}
