<?php


namespace App\Services;


use App\Models\Repositories\GameType\GameTypeRepositoryInterface;

class GameTypeServices
{
    public $gameType;

    public function __construct(
        GameTypeRepositoryInterface $gameType
    ){
        $this->gameType = $gameType;
    }

    public function index()
    {
        return $this->gameType->index();
    }

    public function store($data)
    {
        return $this->gameType->store($data);
    }

    public function update($id, $data)
    {
        return $this->gameType->update($id, $data);
    }

    public function delete($id)
    {
        return $this->gameType->delete($id);
    }

    public function getGameType($id)
    {
        return $this->gameType->getGameType($id);
    }

}
