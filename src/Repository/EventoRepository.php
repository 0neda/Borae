<?php

namespace App\Repository;

use App\Entity\Evento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evento>
 */
class EventoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evento::class);
    }

    public function findByCriador($criador): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.criador = :dono')
            ->setParameter('dono', $criador)
            ->orderBy('e.data_inicio', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retorna todos os eventos ordenados por data de início (ascendente)
     *
     * @return Evento[]
     */
    public function findAllOrderByDataInicio(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.data_inicio', 'ASC')
            ->getQuery()
            ->getResult();
    }

    
    //    /**
    //     * @return Evento[] Returns an array of Evento objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evento
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}