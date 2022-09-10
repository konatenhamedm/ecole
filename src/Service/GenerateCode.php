<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class GenerateCode
{
    private $entityClass;
    private  $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    public function getTotal()
    {
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        return $total;
    }

    public function getData($code)
    {

        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
       $format = $code .'-'.$total;
        return $format;
    }




    public function setEntityClass($entityClass)
    {
        $this->entityClass=$entityClass;
        return $this;
    }


    public function getEntityClass()
    {
        return $this->entityClass;
    }
}