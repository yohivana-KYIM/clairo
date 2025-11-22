<?php

namespace App\Entity;

use App\Repository\Gies0Repository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Gies0Repository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class Gies0 extends BaseFileEntity
{
}
