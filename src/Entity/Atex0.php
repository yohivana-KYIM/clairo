<?php

namespace App\Entity;

use App\Repository\Atex0Repository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Atex0Repository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class Atex0 extends BaseFileEntity
{
}
