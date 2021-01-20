<?php


namespace App\Models\Repositories\SubMatch;


interface SubMatchRepositoryInterface
{
    public function store($data);

    public function show($id);

    public function index();

    public function addSubmatchOdds($data);

    public function getOddsByTeam($submatch_id, $match_id);

    public function updateOdds($data, $id);

    public function getSingleOdds($sub_match_id, $match_id, $team_id);
}
