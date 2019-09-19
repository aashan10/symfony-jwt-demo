<?php


namespace App\Repository;


use App\Contracts\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use ReflectionClass;

abstract class AbstractRepository extends ServiceEntityRepository implements RepositoryInterface
{

    protected $queryBuilder;
    protected $request;
    protected $page;
    protected $limit;
    protected $orderBy;
    protected $sortBy;

    public function __construct( ManagerRegistry $registry, $classname )
    {
        parent::__construct( $registry, $classname );
        $this->queryBuilder = $this->createQueryBuilder($this->getEntityIdentifier());
        $this->request = Request::createFromGlobals();
        $this->boot();
    }


    public function boot() : void {
        
        $this->page = $this->request->get('page') ? (int) $this->request->get('page') : 0;
        $this->limit = $this->request->get('limit') ? (int) $this->request->get('limit') : 10;
        $this->sortBy = in_array($this->request->get('sort_by') , $this->getAttributes())? (string) $this->request->get('sort_by') : 'id';
        $this->orderBy = $this->request->get('order_by') ? (string) $this->request->get('order_by') : 'DESC';

    }


    public function _count(): int
    {
        $this->refreshQueryBuilder();
        return count($this->get());
    }

    public function first()
    {
        return $this->queryBuilder->getQuery()->getFirstResult();
    }

    public function all(string $type = 'array'): array
    {
        $this->refreshQueryBuilder();
        switch ($type) {
            case 'array':
                return $this->queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
            default :
                return $this->queryBuilder->getQuery()->getResult();
        }
    }

    public function get(string $type = 'array'): array
    {
        switch ($type) {
            case 'array':
                return $this->queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
            default :
                return $this->queryBuilder->getQuery()->getResult();
        }
    }



    public function getAttributes(): array
    {
        $props = [];
        try {
            $reflect = new ReflectionClass($this->_entityName);
            foreach ($reflect->getProperties(\ReflectionProperty::IS_PRIVATE) as $property){
                array_push($props, $property->getName());
            };
        } catch (\ReflectionException $e) {}

        return $props;
    }

    public function getEntityIdentifier(): string
    {
        $entity = explode('\\', $this->_entityName);
        return strtolower(array_pop($entity));
    }

    public function limit(): RepositoryInterface
    {
        $this->queryBuilder->setMaxResults($this->limit);
        return $this;
    }

    public function orderBy()
    {
        $this->queryBuilder->orderBy($this->getEntityIdentifier(). '.' . $this->sortBy, $this->orderBy);
        return $this;
    }

    public function paginate(): array
    {
        return [
            'data' => $this
                ->orderBy()
                ->limit()
                ->queryBuilder
                ->setFirstResult($this->page * $this->limit)
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY),
            'pagination' => [
                'previous_page' => $this->page != 0 ? $this->page - 1 : null ,
                'current_page' => $this->page,
                'next_page' => ($this->page * ($this->limit + 1)) >= $this->_count() ? $this->page + 1 : null,
                'limit' => $this->limit,
                'total_items' => $this->_count()
            ]
        ];
    }

    public function where($column, $operator, $value): RepositoryInterface
    {
        $this->queryBuilder->where($this->getEntityIdentifier().'.'.$column.' '. $operator .' '. "'$value'");
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        $this->queryBuilder->orWhere($this->getEntityIdentifier().'.'.$column.' '. $operator .' '. "'$value'");
        return $this;
    }

    public function andWhere($column, $operator, $value) : self
    {
        $this->queryBuilder->andWhere($this->getEntityIdentifier().'.'.$column.' '. $operator .' '. "'$value'");
        return $this;
    }

    private function refreshQueryBuilder() : void {
        $this->queryBuilder = $this->createQueryBuilder($this->getEntityIdentifier());
    }
}