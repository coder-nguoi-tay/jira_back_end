<?php

namespace App\Repositories\Projects;

use App\Models\Project;
use App\Repositories\Base;

class ProjectRepository extends Base
{
    public function model()
    {
        return Project::class;
    }
}
