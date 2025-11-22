<?php

namespace App\Entity;

use App\Repository\Gies2Repository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Gies2Repository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class Gies2 extends BaseFileEntity
{
}
