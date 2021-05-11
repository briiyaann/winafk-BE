<?php


namespace App\Services;


use App\Models\Repositories\Team\TeamRepositoryInterface;

class TeamServices
{
    public $team;

    public function __construct(
        TeamRepositoryInterface $team
    ){
        $this->team = $team;
    }

    public function getTeamsPaginate()
    {
        return $this->team->getTeamsPaginate();
    }

    public function store($data)
    {
        return $this->team->store($data);
    }

    public function show($id)
    {
        return $this->team->show($id);
    }

    public function update($id, $data)
    {
        return $this->team->update($id, $data);
    }

    public function delete($id)
    {
        return $this->team->delete($id);
    }

    public function getTeamByGameType($id)
    {
        return $this->team->getTeamByGameType($id);
    }
}
