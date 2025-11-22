<?php

namespace App\Entity;

use App\Repository\AttestationHebergeantRepository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttestationHebergeantRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class AttestationHebergeant extends BaseFileEntity
{
}
