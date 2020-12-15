<?php


namespace App\Models\Repositories\GameType;


use App\Models\Core\GameType;

interface GameTypeRepositoryInterface
{
    public function index();

    public function store($data);

    public function update($id, $data);

    public function delete($id);

    public function getGameType($id);
}
