<?php

namespace App\Entity;

use App\Repository\AutreDocumentRepository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutreDocumentRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class AutreDocument extends BaseFileEntity
{
}
