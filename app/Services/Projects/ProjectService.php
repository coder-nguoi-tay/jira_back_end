<?php

namespace App\Services\Projects;

use App\Repositories\Projects\ProjectRepository;
use Illuminate\Support\Arr;

class ProjectService
{
    public $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getList($query)
    {
        $array = [];

        $page = Arr::get($query, 'page_size', 1);

        return $this->projectRepository->filter($array)->paginate(10, ['*'], $page);
    }
}
