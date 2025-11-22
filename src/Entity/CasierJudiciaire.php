<?php

namespace App\Entity;

use App\Repository\CasierJudiciaireRepository;
use App\Traits\FileUploadTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasierJudiciaireRepository::class)]
class CasierJudiciaire extends BaseFileEntity
{
}
