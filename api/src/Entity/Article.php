<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Get(),
        new Post(),
        new GetCollection(),
        new Put(securityPostDenormalize: "is_granted('ROLE_USER') "),
        new Delete(securityPostDenormalize: "is_granted('ROLE_USER')"),
    ],
    normalizationContext:["groups"=>["article:read"]],
    denormalizationContext:["groups"=>["article:write"]],
    processor: UserProcessor::class,
    mercure: true
)]
#[ORM\Entity(repositoryClass:"App\Repository\Article")]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    #[Groups(["article:read", "article:write"])]
    #[Assert\NotBlank()]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(["article:read", "article:write"])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[Groups(["article:read", "article:write"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
