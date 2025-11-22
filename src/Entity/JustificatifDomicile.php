<?php

namespace App\Entity;

use App\Repository\JustificatifDomicileRepository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JustificatifDomicileRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class JustificatifDomicile extends BaseFileEntity
{
}
