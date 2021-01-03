<?php


namespace App\Services;


use App\Models\Repositories\Match\MatchRepositoryInterface;

class MatchServices
{
    public $match;

    public function __construct(
        MatchRepositoryInterface $match
    ){
        $this->match = $match;
    }

}
