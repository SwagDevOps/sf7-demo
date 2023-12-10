<?php

namespace App\Repository;

use App\Entity\Event;
use Carbon\Carbon;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends AbstractRepository
{
    /**
     * Paginates events (used for an index page)
     *
     * @see \App\Controller\EventsController::index()
     */
    public function paginate(int $page = 1, int $pageSize = 10): PaginationInterface
    {
        $query = $this->getQueryBuilderForNotEnded();

        return $this->paginateQueryBuilder($query, $page, $pageSize);
    }

    /**
     * Creates basic query-builder with default sort order (by begin date).
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('event')
            ->orderBy('event.begin_date', 'ASC');
    }

    /**
     * Creates a query-builder for events finishing in the future.
     *
     * @param null|DateTimeInterface $currentDate
     */
    protected function getQueryBuilderForNotEnded(DateTimeInterface $currentDate = null): QueryBuilder
    {
        return $this->getQueryBuilder()
            ->where('event.end_date > :currentDate')
            ->setParameter('currentDate', $currentDate ?: Carbon::now());
    }
}
