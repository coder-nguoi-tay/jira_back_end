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
    public function create(Request $request)
    {
        return response()->json($this->projectService->create($request->all()));
    }
    public function show(Request $request, $id)
    {
        return response()->json($this->projectService->show($id));
    }
}
