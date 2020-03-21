<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByAdmin($role) {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
                ->from($this->_entityName, 'u')
                ->where('u.roles LIKE :roles')
                ->andWhere('u.confirmed = :confirmed')
                ->setParameter('roles', '%"' . $role . '"%')
                ->setParameter('confirmed', true)
                ->orderBy('u.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findByRole($role) {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
                ->from($this->_entityName, 'u')
                ->where('u.roles LIKE :roles')
                ->andWhere('u.confirmed = :confirmed')
                ->setParameter('roles', '%"' . $role . '"%')
                ->setParameter('confirmed', false)
                ->orderBy('u.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findByRolePaginated($role) {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
                ->from($this->_entityName, 'u')
                ->where('u.roles LIKE :roles')
                ->setParameter('roles', '%"' . $role . '"%');
        return $qb->getQuery()->getResult();

    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }
}
