<?php


namespace App\Data;


use App\Entity\Campus;

class SearchData
{



    /**
     * @var Campus[]
     */
    public $campus = [];

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var null|\DateTime
     */
    public $min;

    /**
     * @var null|\DateTime
     */
    public $max;

    /**
     * @var boolean
     */
    public $isOrga = false;

    /**
     * @var boolean
     */
    public $isInscrit = false;

    /**
     * @var boolean
     */
    public $isNotInscrit = false;

    /**
     * @var boolean
     */
    public $sortiesPassees = false;

}