<?php

namespace App\Admin\Security;

class RoleProvider {
    private array $roles;

    public function __construct(array $rolesHierarchy) {
        $this->roles = array_unique(array_merge(
            array_keys($rolesHierarchy),
            ...array_values($rolesHierarchy)
        ));
    }

    public function getRoles(): array {
        return $this->roles;
    }

    public function getRoleChoices(): array {
        $choices = [];
        $this->roles[] = 'ROLE_USER';
        $this->roles = array_unique($this->roles);

        foreach ($this->roles as $role) {
            $label = ucwords(strtolower(str_replace(['ROLE_', '_'], ['', ' '], $role)));
            $choices[$label] = $role;
        }
        return $choices;
    }
}
