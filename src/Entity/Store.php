<?php

namespace App\Entity;

use App\Repository\StoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_CNPJ', fields: ['cnpj'])]
#[UniqueEntity(fields: ['cnpj'], message: 'Já existe uma empresa com esse CNPJ!')]
#[SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]

class Store {
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 18)]
    private ?string $cnpj = null;

    #[ORM\Column(length: 255)]
    private ?string $corporate_name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dt_foundation = null;

    /**
     * @var Collection<int, Address>
     */
    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'store_id')]
    private Collection $addresses;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getCnpj(): ?string {
        return $this->cnpj;
    }

    public function setCnpj(string $cnpj): static {
        $this->cnpj = $cnpj;

        return $this;
    }

    public function getCorporateName(): ?string {
        return $this->corporate_name;
    }

    public function setCorporateName(string $corporate_name): static {
        $this->corporate_name = $corporate_name;

        return $this;
    }

    public function getDtFoundation(): ?\DateTime {
        return $this->dt_foundation;
    }

    public function setDtFoundation(\DateTime $dt_foundation): static {
        $this->dt_foundation = $dt_foundation;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setStoreId($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getStoreId() === $this) {
                $address->setStoreId(null);
            }
        }

        return $this;
    }
}
