<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WorkshopSession;
use Illuminate\Http\Request;

class WorkshopSessionController extends Controller
{
    public function index($workshopId) {
        $sessions = WorkshopSession::where('work_shop_id', $workshopId)->get();
        return response()->json($sessions, 200);
    }

    public function store(Request $request, $workshopId) {
        $validated = $request->validate([
            'session_date' => 'nullable|date',
            'start_time'   => 'nullable',
            'end_time'     => 'nullable',
            'location'     => 'nullable|string',
            'link'         => 'nullable|string',
            'title'        => 'nullable|string',
            'description'  => 'nullable|string',
        ]);

        $validated['work_shop_id'] = $workshopId;

        $session = WorkshopSession::create($validated);

        return response()->json($session, 201);
    }

    public function show($workshopId, $id) {
        $session = WorkshopSession::where('work_shop_id', $workshopId)
                                  ->findOrFail($id);
        return response()->json($session, 200);
    }

    public function update(Request $request, $workshopId, $id) {
        $session = WorkshopSession::where('work_shop_id', $workshopId)
                                  ->findOrFail($id);

        $validated = $request->validate([
            'session_date' => 'nullable|date',
            'start_time'   => 'nullable',
            'end_time'     => 'nullable',
            'location'     => 'nullable|string',
            'link'         => 'nullable|string',
            'title'        => 'nullable|string',
            'description'  => 'nullable|string',
        ]);

        $session->update($validated);

        return response()->json($session, 200);
    }

    public function destroy($workshopId, $id) {
        $session = WorkshopSession::where('work_shop_id', $workshopId)
                                  ->findOrFail($id);

        $session->delete();

        return response()->json(['message' => 'جلسه با موفقیت حذف شد.'], 200);
    }
}
