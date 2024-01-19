<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function removeByMemberId(int $membreId): void
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery('
            DELETE FROM App\Entity\Commande c
            WHERE c.id_member = :memberId
        ');

        $query->setParameter('memberId', $membreId);
    }

    public function removeByVehiculeId(int $vehiculeId): void
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery('
            DELETE FROM App\Entity\Commande c
            WHERE c.id_vehicule = :vehiculeId
        ');

        $query->setParameter('vehiculeId', $vehiculeId);
    }

    public function removeByCommandeId(int $commandeId): void
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            DELETE FROM App\Entity\Commande c
            WHERE c.id = :commandeId
        ');

        $query->setParameter('commandeId', $commandeId);
    }
//    /**
//     * @return Commande[] Returns an array of Commande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
