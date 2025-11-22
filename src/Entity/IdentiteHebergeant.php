<?php

namespace App\Entity;

use App\Repository\IdentiteHebergeantRepository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IdentiteHebergeantRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class IdentiteHebergeant extends BaseFileEntity
{
}
