<?php


namespace App\Contracts;


interface RepositoryInterface
{
    public function get() : array ;
    public function all() : array ;
    public function limit();
    public function first();
    public function _count() : int ;
    public function orderBy() ;
    public function paginate() : array ;
    public function getAttributes() : array;
    public function getEntityIdentifier() : string;
    public function where($column, $operator, $value) ;
    public function orWhere($column, $operator, $value) ;
    public function andWhere($column, $operator, $value) ;
}