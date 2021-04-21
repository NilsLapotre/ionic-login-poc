<?php

namespace App\Repository;

use App\Entity\FailedLoginAttempt;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use function sprintf;

/**
 * @method FailedLoginAttempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method FailedLoginAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method FailedLoginAttempt[]    findAll()
 * @method FailedLoginAttempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FailedLoginAttemptRepository extends ServiceEntityRepository
{
    /** @var int */
    private $maxFailedLoginAttempts;

    /** @var int */
    private $loginAttemptDelay;

    public function __construct(ManagerRegistry $registry, int $maxFailedLoginAttempts = 3, $loginAttemptDelay = 10)
    {
        parent::__construct($registry, FailedLoginAttempt::class);
        $this->maxFailedLoginAttempts = $maxFailedLoginAttempts;
        $this->loginAttemptDelay = $loginAttemptDelay;
    }

    // /**
    //  * @return LoginAttempt[] Returns an array of LoginAttempt objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoginAttempt
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function hasExceededMaxFailedLoginAttempt(string $username): bool
    {
        $date = new DateTimeImmutable(sprintf('-%d minutes', $this->loginAttemptDelay));

        $qb = $this->createQueryBuilder('fla')
            ->select('COUNT(fla)')
            ->where('fla.occuredAt >= :date')
            ->andWhere('fla.username = :username')
            ->setParameters(
                [
                    'date' => $date,
                    'username' => $username,
                ]
            );

        $nbFailedLoginAttempt = $qb->getQuery()->getSingleScalarResult();

        return ($nbFailedLoginAttempt > $this->maxFailedLoginAttempts) ? true : false;
    }
}
