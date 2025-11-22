<?php

namespace App\Entity;

use App\Repository\PhotoIdentiteRepository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoIdentiteRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class PhotoIdentite extends BaseFileEntity
{
}
