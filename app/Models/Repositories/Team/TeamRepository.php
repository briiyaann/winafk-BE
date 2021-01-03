<?php


namespace App\Models\Repositories\Team;


use App\Models\Core\Team;

class TeamRepository implements TeamRepositoryInterface
{
    public function getTeamsPaginate()
    {
        return Team::with('gameType')->get();
    }

    public function store($data)
    {
        return Team::create($data);
    }

    public function show($id)
    {
        return Team::where('id', $id)->first();
    }

    public function delete($id)
    {
        return Team::where('id', $id)->delete();
    }

    public function update($id, $data)
    {
        return Team::where('id', $id)->update($data);
    }

}
