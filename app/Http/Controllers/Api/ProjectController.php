<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Projects\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    public function index(Request $request)
    {
        return response()->json($this->projectService->getList($request->all()));
    }
}
