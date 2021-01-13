<?php


namespace App\Services;


use App\Models\Repositories\Topup\TopupRepositoryInterface;

class TopupServices
{

    public $topup;

    public function __construct(
        TopupRepositoryInterface $topup
    ) {
        $this->topup = $topup;
    }

    public function getList($id)
    {
        return $this->topup->getList($id);
    }

    public function store($data)
    {
        return $this->topup->store($data);
    }

    public function update($data, $id)
    {
        return $this->topup->update($data, $id);
    }

    public function getAllByStatus($status)
    {
        return $this->topup->getAllByStatus($status);
    }

    public function getAll()
    {
        return $this->topup->getAll();
    }

    public function getTopup($id)
    {
        return $this->topup->getTopup($id);
    }
}
