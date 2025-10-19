<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(type: 'integer', message: 'L\'ID doit être un entier.')]
    private ?int $id = null;

    #[Groups(['getUser', 'createUser', 'updateUser'])]
    #[Assert\NotBlank(groups: ['default', 'create'])]
    #[ORM\Column]
    #[Assert\NotBlank(groups: ['Default', 'create'], message: 'Le nom d\'utilisateur ne peut pas être vide.')]
    #[Assert\Type(type: 'string', message: 'Le nom d\'utilisateur doit être une chaîne.')]
    public string $username;

    #[Groups(['getUser', 'createUser', 'updateUser'])]
    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide.')]
    #[Assert\Email(message: 'L\'adresse email "{{ value }}" n\'est pas valide.')]
    #[Assert\Type(type: 'string', message: 'L\'email doit être une chaîne.')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[Groups(['getUser', 'createUser', 'updateUser'])]
    #[ORM\Column]
    #[Assert\Type(type: 'array', message: 'Les rôles doivent être un tableau.')]
    #[Assert\All([
        new Assert\Type(type: 'string', message: 'Chaque rôle doit être une chaîne.')
    ])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['getUser', 'createUser', 'updateUser'])]
    #[Assert\NotBlank(message: 'Le mot de passe est requis.')]
    #[Assert\Type(type: 'string', message: 'Le mot de passe doit être une chaîne.')]
    private ?string $password = null;

    /**
     * @var bool subscribe to news letter
     */
    #[ORM\Column(type: 'boolean')]
    #[Groups(['getUser', 'createUser', 'updateUser'])]
    #[Assert\Type(type: 'bool', message: 'La souscription à la newsletter doit être un booléen.')]
    private ?bool $subcription_to_newsletter = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    // SETTER pour username
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getSubcription(): ?bool
    {
        return $this->subcription_to_newsletter;
    }

    public function setSubcription(bool $subcription_to_newsletter): static
    {
        $this->subcription_to_newsletter = $subcription_to_newsletter;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }
}
