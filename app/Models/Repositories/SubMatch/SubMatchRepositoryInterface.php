<?php


namespace App\Models\Repositories\SubMatch;


interface SubMatchRepositoryInterface
{
    public function store($data);

    public function show($id);

    public function index();
}
