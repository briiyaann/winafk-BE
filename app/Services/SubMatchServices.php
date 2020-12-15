<?php


namespace App\Services;


use App\Models\Repositories\SubMatch\SubMatchRepositoryInterface;

class SubMatchServices
{
    public $sub_match;

    public function __construct(
        SubMatchRepositoryInterface $sub_match
    ){
         $this->sub_match = $sub_match;
    }

    public function store($data)
    {
        return $this->sub_match->store($data);
    }

    public function show($id)
    {
        return $this->sub_match->show($id);
    }

    public function index()
    {
        return $this->sub_match->index();
    }
}
