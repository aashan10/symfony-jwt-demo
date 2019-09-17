<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    /**
     * @var QueryBuilder - QueryBuilder instance for initializing the query builder
     */
    protected $queryBuilder;

    /**
     * @var int - Current Page Number for pagination
     */
    protected $page;

    /**
     * PostsRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct( ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
        $this->queryBuilder = $this->createQueryBuilder('posts');
        $request = new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
        $this->page = (int) $request->get('page') ? $request->get('page') : 0;
    }

    /**
     * @return $this
     */
    public function latest() : self
    {
        $this->queryBuilder->orderBy('posts.created_at', 'DESC');
        return $this;
    }

    /**
     * @param int $id - author id
     * @return $this
     */
    public function getFromAuthor(int $id) : self
    {
        $this->queryBuilder->where("posts.author_id = $id");
        return $this;
    }

    /**
     * @return $this
     */
    public function oldest() : self
    {
        $this->queryBuilder->orderBy('posts.created_at', 'ASC');
        return $this;
    }

    /**
     * @param string $column_name
     * @param string $operator
     * @param $value
     * @return $this
     */
    public function where(string $column_name, string $operator , $value) : self
    {
        $this->queryBuilder->where('posts.'.$column_name.' '. $operator ."'". $value."'" );
        return $this;
    }

    /**
     * @return int
     */
    public function countPosts() : int
    {
        return count($this->get());
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit) : self
    {
        $this->queryBuilder->setMaxResults($limit);
        return $this;
    }

    /**
     * @return array
     */
    public function get() : array
    {
        return $this->queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @param int $count
     * @return $this
     */
    public function paginate(int $count) : self
    {
        $this->queryBuilder->setFirstResult($count * $this->page)->setMaxResults($count);
        return $this;
    }

    public function first(){
        return $this->countPosts() > 0 ? $this->get()[0] : null;
    }
}
