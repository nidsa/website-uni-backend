<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Models\Rsdltag;
use App\Services\RsdltagService;
use App\Http\Requests\RsdltagRequest;
use Exception;

class RsdltagController extends Controller
{
    protected $rsdltagService;

    public function __construct(RsdltagService $rsdltagService)
    {
        $this->rsdltagService = $rsdltagService;
    }

    public function index()
    {
        try {
            $data = Rsdltag::with([
                'rsdl:rsdl_id,rsdl_title',
                'img:image_id,img'
            ])->get();

            return $this->sendResponse($data);
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve RSDL Tags', 500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $item = Rsdltag::with([
                'rsdl:rsdl_id,rsdl_title',
                'img:image_id,img'
            ])->find($id);

            if (!$item) return $this->sendError('RSDL Tag not found', 404);

            return $this->sendResponse($item);
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve RSDL Tag', 500, ['error' => $e->getMessage()]);
        }
    }

    public function create(RsdltagRequest $request)
    {
        try {
            $validated = $request->validated();
            $created = $this->rsdltagService->create($validated);
            return $this->sendResponse($created, 201, 'RSDL Tag created successfully');
        } catch (Exception $e) {
            return $this->sendError('Failed to create RSDL Tag', 500, ['error' => $e->getMessage()]);
        }
    }

    public function update(RsdltagRequest $request, $id)
    {
        try {
            $rsdltag = Rsdltag::find($id);
            if (!$rsdltag) return $this->sendError('RSDL Tag not found', 404);

            $updated = $this->rsdltagService->update($rsdltag, $request->validated());
            return $this->sendResponse($updated, 200, 'RSDL Tag updated successfully');
        } catch (Exception $e) {
            return $this->sendError('Failed to update RSDL Tag', 500, ['error' => $e->getMessage()]);
        }
    }
}
