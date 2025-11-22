<?php

namespace App\Entity;

use App\Repository\TitreSejourRepository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TitreSejourRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'reference_data')]
class TitreSejour extends BaseFileEntity
{
}
