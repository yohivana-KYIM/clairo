<?php

namespace App\Entity;

use App\Repository\Gies1Repository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Gies1Repository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class Gies1 extends BaseFileEntity
{
}
