<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    protected PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Event::class);

        $this->paginator = $paginator;
    }

    /**
     * Paginates given query-builder.
     *
     * @see https://github.com/KnpLabs/KnpPaginatorBundle
     */
    protected function paginateQueryBuilder(QueryBuilder $query, int $page = 1, int $pageSize = 10): PaginationInterface
    {
        $page = (max($page, 1));

        return $this->paginator->paginate($query, $page, $pageSize);
    }
}
