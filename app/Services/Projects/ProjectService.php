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
    /**
     * Summary of create
     * @param mixed $request
     * @return array{data: array, message: string, status: bool|array{data: mixed, staus: bool}}
     */
    public function create($request)
    {
        $respone = $this->projectRepository->create([
            'name' => Arr::get($request, 'name')
        ]);

        if (!$respone) {
            return [
                'status' => false,
                'data' => [],
                'message' => 'Tạo project thất bại'
            ];
        }

        return [
            'staus' => true,
            'data' => $respone,
            'message' => 'Tạo project thành công'
        ];
    }
    public function show($id)
    {
        $project = $this->projectRepository->find($id);

        if (!$project) {

            return [
                'status' => false,
                'data' => null,
                'message' => 'Không tìm thấy dự án '
            ];
        }

        return $project;
    }
}
