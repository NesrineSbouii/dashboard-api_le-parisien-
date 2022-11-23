<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;

use ApiPlatform\Metadata\ApiResource;
use App\State\UserProcessor;


#[ApiResource(
    operations: [
        new Get(),
        new Post(),
        new GetCollection(),
        new Put(securityPostDenormalize: "is_granted('ROLE_USER') and object == user"),
        new Delete(securityPostDenormalize: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext:["groups"=>["user:read"]],
    denormalizationContext:["groups"=>["user:write"]],
    processor: UserProcessor::class,
    mercure: true
)]
#[UniqueEntity(fields:["username"])]
#[UniqueEntity(fields:["email"])]
#[ORM\Entity()]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type:"integer")]
    
    private $id;

    
    #[ORM\Column(type:"string", length:180, unique:true)]
    #[Groups(["user:read", "user:write"])]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    
    private $email;

    
    #[ORM\Column(type:"json")]
    
    private $roles = [];

    
    #[ORM\Column(type:"string")]
    
    private $password;

    
    #[ORM\Column(type:"string", length:255, unique:true)]
    #[Groups(["user:read", "user:write"])]
    #[Assert\NotBlank()]
    
    private $username;

    #[ORM\Column(type:"string", length:255, unique:true)]
    #[Groups(["user:read", "user:write"])]
    #[Assert\NotBlank()]
    
    private $firstname;

    #[ORM\Column(type:"string", length:255, unique:true)]
    #[Groups(["user:read", "user:write"])]
    #[Assert\NotBlank()]
    
    private $lastname;


    
    #[Groups("user:write")]
    #[Assert\NotBlank(groups:["create"])]
    #[SerializedName("password")]
    
    private $plainPassword;

    #[Groups(["user:read"])]
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Article::class, orphanRemoval: true)]
    private Collection $articles;

    public function __construct()
    {
        $this->cheeseListings = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
    public function getUsername(): string
    {
        return (string) $this->username;
    }
    public function getFirstname(): string
    {
        return (string) $this->firstname;
    }
    public function getLastname(): string
    {
        return (string) $this->lastname;
    }
    
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function setLastname(string $lastrname): self
    {
        $this->lastname = $lastrname;

        return $this;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }
}
