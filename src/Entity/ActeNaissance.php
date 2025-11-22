<?php

namespace App\Entity;

use App\Repository\ActeNaissanceRepository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActeNaissanceRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'reference_data')]
class ActeNaissance extends BaseFileEntity
{
}
