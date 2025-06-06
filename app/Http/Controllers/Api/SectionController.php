<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Section;
use App\Http\Requests\SectionRequest;
use App\Services\SectionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SectionController extends Controller
{
    protected $service;

    public function __construct(SectionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $query = $sections = Section::with('page')->where('active', 1);

            if ($request->has('sec_page')) {
                $query->where('sec_page', $request->input('sec_page'));
            }

            $sections = $query->get();

            return $this->sendResponse($sections);
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve sections', 500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $section = Section::with('page')->find($id);
            return $section ? $this->sendResponse($section) : $this->sendError('Section not found', 404);
        } catch (Exception $e) {
            return $this->sendError('Failed to fetch section', 500, ['error' => $e->getMessage()]);
        }
    }

    public function create(SectionRequest $request)
    {
        try {
            $validated = $request->validated();
            $createdSections = [];

            if (isset($validated['sections']) && is_array($validated['sections'])) {
                foreach ($validated['sections'] as $item) {
                    if (!isset($item['sec_order'])) {
                        $item['sec_order'] = (Section::where('sec_page', $item['sec_page'])->max('sec_order') ?? 0) + 1;
                    }

                    $item['lang'] = $item['lang'] ?? 1;
                    $item['sec_type'] = $item['sec_type'] ?? null;
                    $item['display'] = $item['display'] ?? 1;
                    $item['active'] = $item['active'] ?? 1;

                    $createdSections[] = Section::create($item);
                }
            }

            return $this->sendResponse($createdSections, 201, 'Section records created successfully');
        } catch (Exception $e) {
            return $this->sendError('Failed to create section', 500, ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $section = Section::find($id);

        if (!$section) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        // Update display value
        if ($request->has('display')) {
            $section->display = $request->input('display');
        }

        $section->save();

        return response()->json(['message' => 'Section updated successfully']);
    }

    public function visibility($id)
    {
        try {
            $section = Section::find($id);
            if (!$section) return $this->sendError('Section not found', 404);

            $section->active = !$section->active;
            $section->save();
            return $this->sendResponse([], 200, 'Visibility toggled');
        } catch (Exception $e) {
            return $this->sendError('Failed to toggle visibility', 500, ['error' => $e->getMessage()]);
        }
    }

    public function getByPage($p_id)
    {
        $sections = Section::where('sec_page', $p_id)
            ->where('active', 1)
            ->orderBy('sec_order', 'asc')
            ->get();

        return response()->json(['data' => $sections]);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            '*.sec_id' => 'required|integer|exists:tbsection,sec_id',
            '*.sec_order' => 'required|integer'
        ]);

        foreach ($data as $item) {
            Section::where('sec_id', $item['sec_id'])->update(['sec_order' => $item['sec_order']]);
        }

        return response()->json([
            'message' => 'Section order updated successfully',
        ], 200);
    }

    // public function syncSection(Request $request)
    // {
    //     $page_id = $request->input('sec_page');

    //     if (!$page_id) {
    //         return response()->json(['message' => 'Missing page_id'], 400);
    //     }

    //     $sections = $request->input('sections', []);

    //     $existingSectionIds = collect($sections)
    //         ->pluck('sec_id')
    //         ->filter(fn($id) => !is_null($id))
    //         ->unique()
    //         ->values()
    //         ->toArray();

    //     // Delete sections in this page that are not included in the request,
    //     // but do not delete sections where active = 0 (inactive)
    //     if (!empty($existingSectionIds)) {
    //         Section::where('sec_page', $page_id)
    //             ->whereNotIn('sec_id', $existingSectionIds)
    //             ->where('active', '!=', 0)
    //             ->delete();
    //     }

    //     foreach ($sections as $section) {
    //         $existingSection = Section::where('sec_id', $section['sec_id'] ?? 0)
    //             ->where('sec_page', $page_id)
    //             ->first();

    //         if ($existingSection) {
    //             // Update existing section
    //             $existingSection->update([
    //                 'sec_order' => $section['sec_order'],
    //                 'sec_type' => $section['sec_type'],
    //                 'lang' => $section['lang'],
    //                 'active' => $section['active'] ?? 1,
    //             ]);
    //         } else {
    //             // Create new section
    //             Section::create([
    //                 'sec_page' => $page_id,
    //                 'sec_order' => $section['sec_order'],
    //                 'sec_type' => $section['sec_type'],
    //                 'lang' => $section['lang'],
    //                 'active' => $section['active'] ?? 1,
    //             ]);
    //         }
    //     }

    //     return response()->json([
    //         'message' => 'Section Synced Successfully',
    //         'data' => $sections,
    //     ], 200);
    // }

    public function syncSection(Request $request)
    {
        $page_id = $request->input('sec_page');

        if (!$page_id) {
            return response()->json(['message' => 'Missing page_id'], 400);
        }

        $sections = $request->input('sections', []);

        $existingIds = collect($sections)
            ->pluck('sec_id')
            ->filter(fn($id) => !is_null($id))
            ->unique()
            ->values()
            ->toArray();

        if (!empty($existingIds)) {
            Section::where('sec_page', $page_id)
                ->whereNotIn('sec_id', $existingIds)
                ->where('active', '!=', 0)
                ->delete();
        }

        foreach ($sections as $section) {
            $existing = null;

            if (!empty($section['sec_id'])) {
                $existing = Section::where('sec_id', $section['sec_id'])
                    ->where('sec_page', $page_id)
                    ->first();
            } elseif (!empty($section['sec_code'])) {
                $existing = Section::where('sec_code', $section['sec_code'])->first();
            }

            if ($existing) {
                $existing->update([
                    'sec_order' => $section['sec_order'],
                    'sec_type' => $section['sec_type'] ?? '',
                    'sec_code' => $section['sec_type'] . "-" . $existing->sec_id,
                    'active' => $section['active'] ?? 1,
                ]);
                continue;
            } else {
                $created = Section::create([
                    'sec_page' => $page_id,
                    'sec_order' => $section['sec_order'],
                    'sec_type' => $section['sec_type'] ?? '',
                    'sec_code' => '', // temporary
                    'active' => $section['active'] ?? 1,
                ]);

                $created->update([
                    'sec_code' => $created->sec_type . '-' . $created->sec_id,
                ]);
            }
        }

        return response()->json([
            'message' => 'Sections synced successfully',
            'data' => $sections,
        ], 200);
    }

    public function updateSecCodes()
    {
        Section::chunk(100, function ($sections) {
            foreach ($sections as $section) {
                $section->update([
                    'sec_code' => $section->sec_type . '-' . $section->sec_id
                ]);
            }
        });

        return "Update complete.";
    }
}
