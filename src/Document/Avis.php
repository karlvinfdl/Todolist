<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'avis')]
class Avis
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $nom;

    #[ODM\Field(type: 'int')]
    private int $note;

    #[ODM\Field(type: 'string')]
    private string $commentaire;

    #[ODM\Field(type: 'date')]
    private \DateTime $createdAt;

    #[ODM\Field(type: 'bool')]
    private bool $valide = false;

    public function __construct(string $nom, int $note, string $commentaire)
    {
        $this->nom         = $nom;
        $this->note        = $note;
        $this->commentaire = $commentaire;
        $this->createdAt   = new \DateTime();
    }

    public function getId(): ?string         { return $this->id; }
    public function getNom(): string         { return $this->nom; }
    public function getNote(): int           { return $this->note; }
    public function getCommentaire(): string { return $this->commentaire; }
    public function getCreatedAt(): \DateTime { return $this->createdAt; }
    public function isValide(): bool         { return $this->valide; }
    public function setValide(bool $valide): void { $this->valide = $valide; }
}
