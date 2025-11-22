<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserValidationToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\ByteString;

class UserValidationTokenManager
{
    private EntityManagerInterface $em;
    private string $appSecret;

    public function __construct(EntityManagerInterface $em,  #[Autowire('%env(APP_SECRET)%')] string $appSecret)
    {
        $this->em = $em;
        $this->appSecret = $appSecret;
    }

    public function generate(User $user, string $type, \DateTimeInterface $expiresAt): UserValidationToken
    {
        $data = sprintf('%d|%s|%s',
            $user->getId(),
            $user->getEmail(),
            $user->getCreatedAt()->format('Y-m-d H:i:s')
        );

        $randomSalt = ByteString::fromRandom(16)->toString();
        $token = hash_hmac('sha256', $data . '|' . $type . '|' . $randomSalt, $this->appSecret);

        $tokenEntity = (new UserValidationToken())
            ->setUser($user)
            ->setToken($token)
            ->setType($type)
            ->setUsed(false)
            ->setExpiresAt($expiresAt);

        $this->em->persist($tokenEntity);

        return $tokenEntity;
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}
