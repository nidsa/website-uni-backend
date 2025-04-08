<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Rsdl;
use App\Http\Requests\RsdlRequest;
use Exception;

class RsdlController extends Controller
{
    public function index()
    {
        try {
            $rsdls = Rsdl::where('active', 1)->get();
            return response()->json(['status' => 200, 'data' => $rsdls]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $rsdl = Rsdl::find($id);
            return $rsdl
                ? response()->json(['status' => 200, 'data' => $rsdl])
                : response()->json(['status' => 404, 'message' => 'Not Found']);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function create(RsdlRequest $request)
    {
        try {
            $data = $request->validated();

            if (!isset($data['rsdl_order'])) {
                $data['rsdl_order'] = Rsdl::max('rsdl_order') + 1;
            }

            $event = Rsdl::create($data);
            return $this->sendResponse($event, 201, 'Rsdl created');
        } catch (Exception $e) {
            return $this->sendError('Failed to create Rsdl', 500, ['error' => $e->getMessage()]);
        }
    }

    public function update(RsdlRequest $request, $id)
    {
        try {
            $rsdl = Rsdl::find($id);
            if (!$rsdl) return response()->json(['status' => 404, 'message' => 'Not Found']);
            $rsdl->update($request->validated());
            return response()->json(['status' => 200, 'message' => 'Updated', 'data' => $rsdl]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function visibility($id)
    {
        $rsdl = Rsdl::find($id);
        if (!$rsdl) return response()->json(['status' => 404, 'message' => 'Not Found']);
        $rsdl->active = $rsdl->active ? 0 : 1;
        $rsdl->save();
        return response()->json(['status' => 200, 'message' => 'Visibility toggled']);
    }

    public function duplicate($id)
{
    try {
        $rsdl = Rsdl::find($id);

        if (!$rsdl) {
            return $this->sendError('Rsdl not found', 404);
        }

        $newRsdl = $rsdl->replicate();
        $newRsdl->rsdl_title = $rsdl->rsdl_title . ' (Copy)';
        $newRsdl->rsdl_order = Rsdl::max('rsdl_order') + 1;
        $newRsdl->save();

        return $this->sendResponse($newRsdl, 200, 'Rsdl duplicated successfully');
    } catch (Exception $e) {
        return $this->sendError('Failed to duplicate Rsdl', 500, ['error' => $e->getMessage()]);
    }
}

public function reorder(Request $request)
{
    try {
        $data = $request->validate([
            '*.rsdl_id' => 'required|integer|exists:trsdl,rsdl_id',
            '*.rsdl_order' => 'required|integer'
        ]);

        foreach ($data as $item) {
            Rsdl::where('rsdl_id', $item['rsdl_id'])->update([
                'rsdl_order' => $item['rsdl_order']
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Rsdl order updated successfully',
        ]);
    } catch (Exception $e) {
        return $this->sendError('Failed to reorder Rsdl', 500, ['error' => $e->getMessage()]);
    }
}


}
