<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Symfony\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "export_logs")]
class ExportLog
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $entity;

    #[ORM\Column(type: "string")]
    private string $format;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $exportedAt;

    #[ORM\Column(type: "string")]
    private string $exportedBy;

    public function __construct(string $entity, string $format, string $exportedBy)
    {
        $this->entity = $entity;
        $this->format = $format;
        $this->exportedAt = new \DateTime();
        $this->exportedBy = $exportedBy;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getExportedAt(): \DateTimeInterface
    {
        return $this->exportedAt;
    }

    public function setExportedAt(\DateTimeInterface $exportedAt): void
    {
        $this->exportedAt = $exportedAt;
    }

    public function getExportedBy(): string
    {
        return $this->exportedBy;
    }

    public function setExportedBy(string $exportedBy): void
    {
        $this->exportedBy = $exportedBy;
    }
}
