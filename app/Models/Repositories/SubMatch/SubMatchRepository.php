<?php


namespace App\Models\Repositories\SubMatch;


use App\Models\Core\SubMatch;

class SubMatchRepository implements SubMatchRepositoryInterface
{
    public function store($data)
    {
        return SubMatch::create($data);
    }

    public function show($id)
    {
        return SubMatch::where('id', $id)->first();
    }

    public function index()
    {
        return SubMatch::all();
    }
}
