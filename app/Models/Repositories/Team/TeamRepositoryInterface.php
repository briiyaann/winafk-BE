<?php


namespace App\Models\Repositories\Team;


interface TeamRepositoryInterface
{
    public function getTeamsPaginate();

    public function store($data);

    public function show($id);

    public function update($id, $data);

    public function delete($id);

    public function getTeamByGameType($id);
}
