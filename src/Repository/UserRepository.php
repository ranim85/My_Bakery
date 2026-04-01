<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Webauthn\PublicKeyCredentialUserEntity;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }
        $user->setPasswordHash($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findOneByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        $users = $this->findAll();
        foreach ($users as $u) {
            if (hash_equals(base64_encode($u->getUsername()), base64_encode($userHandle))
                || $u->getUsername() === $userHandle) {
                return new PublicKeyCredentialUserEntity(
                    $u->getUsername(),
                    $u->getUsername(),
                    $u->getUsername()
                );
            }
        }
        return null;
    }

    public function findOneByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        $user = $this->findOneBy(['username' => $username]);
        if (!$user) return null;

        return new PublicKeyCredentialUserEntity(
            $user->getUsername(),
            $user->getUsername(),
            $user->getUsername()
        );
    }

    public function findUserByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }
}
