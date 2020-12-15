<?php


namespace App\Models\Repositories\GameType;


use App\Models\Core\GameType;

class GameTypeRepository implements GameTypeRepositoryInterface
{
    public function index()
    {
        return GameType::paginate(10);
    }

    public function store($data)
    {
        return GameType::create($data);
    }

    public function update($id, $data)
    {
        return GameType::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return GameType::where('id', $id)->delete();
    }

    public function getGameType($id)
    {
        return GameType::find($id);
    }

}
