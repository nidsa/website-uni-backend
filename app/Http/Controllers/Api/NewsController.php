<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\News;
use App\Http\Requests\NewsRequest;
use Exception;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        try {
            $news = News::with('img')->where('active', 1)->get();
            return $this->sendResponse($news->count() === 1 ? $news->first() : $news);
        } catch (Exception $e) {
            return $this->sendError('Failed to fetch news', 500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $news = News::with('img')->find($id);
            if (!$news) return $this->sendError('News not found', 404);
            return $this->sendResponse($news);
        } catch (Exception $e) {
            return $this->sendError('Failed to fetch news', 500, ['error' => $e->getMessage()]);
        }
    }

    public function create(NewsRequest $request)
    {
        try {
            $data = $request->validated();

            $data['ref_id'] = $data['ref_id'] ?? (News::max('ref_id') ? News::max('ref_id') + 1 : 100);

            if (!isset($data['n_order'])) {
                $data['n_order'] = News::max('n_order') + 1;
            }

            $event = News::create($data);
            return $this->sendResponse($event, 201, 'News created');
        } catch (Exception $e) {
            return $this->sendError('Failed to create news', 500, ['error' => $e->getMessage()]);
        }
    }

    public function update(NewsRequest $request, $id)
    {
        try {
            $news = News::find($id);
            if (!$news) return $this->sendError('News not found', 404);

            if (!$news->ref_id && $request->has('ref_id')) {
                $news->ref_id = $request->input('ref_id');
                $news->save();
            }

            $news->update($request->all());
            return $this->sendResponse($news, 200, 'News updated');
        } catch (Exception $e) {
            return $this->sendError('Failed to update news', 500, ['error' => $e->getMessage()]);
        }
    }

    public function visibility($id)
    {
        try {
            $news = News::find($id);
            if (!$news) return $this->sendError('News not found', 404);
            $news->active = $news->active ? 0 : 1;
            $news->save();
            return $this->sendResponse([], 200, 'Visibility toggled');
        } catch (Exception $e) {
            return $this->sendError('Failed to update visibility', 500, ['error' => $e->getMessage()]);
        }
    }

    public function duplicate($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        $newNews = $news->replicate();
        $newNews->n_title = $news->n_title . ' (Copy)';
        $newNews->n_order = News::max('n_order') + 1;
        $newNews->save();

        return response()->json(['message' => 'News duplicated', 'data' => $newNews], 200);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            '*.n_id' => 'required|integer|exists:tbnew,n_id',
            '*.n_order' => 'required|integer'
        ]);

        foreach ($data as $item) {
            News::where('n_id', $item['n_id'])->update(['n_order' => $item['n_order']]);
        }

        return response()->json([
            'message' => 'News order updated successfully',
        ], 200);
    }

    public function assignRefIds()
    {
        try {
            $news = News::whereNull('ref_id')->get();
            $nextRef = News::max('ref_id') ?? 99;

            foreach ($news as $new) {
                $new->ref_id = ++$nextRef;
                $new->save();
            }

            return response()->json(['message' => 'Ref IDs assigned successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to assign Ref IDs', 'error' => $e->getMessage()], 500);
        }
    }
}
