<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\WorkShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\WorkshopParticipantResource;

class WorkshopParticipantController extends Controller
{
    // ثبت شرکت‌کننده جدید و اضافه کردن به کارگاه
    public function store(Request $request, $workshopId)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'national_code' => 'nullable|string|max:20',
            'phone'         => 'required|string|max:20',
            'gender'        => 'nullable|in:male,female',
            'approved'      => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workshop = Workshop::findOrFail($workshopId);

        // اگر شرکت‌کننده با national_code یا شماره تلفن وجود داشت، استفاده کن، در غیر این صورت بساز
        $participant = null;
        if ($request->filled('national_code')) {
            $participant = Participant::where('national_code', $request->national_code)->first();
        }
        if (!$participant) {
            // اگر با national_code پیدا نشد، تلاش کن با شماره تلفن پیداش کنی
            $participant = Participant::where('phone', $request->phone)->first();
        }

        if (!$participant) {
            // ساخت شرکت‌کننده جدید
            $participant = Participant::create([
                'name'          => $request->name,
                'name_en'       => $request->name_en,
                'national_code' => $request->national_code,
                'phone'         => $request->phone,
                'gender'        => $request->gender,
            ]);
        } else {
            // اگر شرکت‌کننده بود، آپدیت کن اطلاعات رو (اختیاری)
            $participant->update([
                'name'          => $request->name,
                'name_en'       => $request->name_en,
                'national_code' => $request->national_code,
                'phone'         => $request->phone,
                'gender'        => $request->gender,
            ]);
        }

        // ارتباط با کارگاه و ذخیره‌ی اطلاعات پیتوت (pivot)
        $workshop->participants()->syncWithoutDetaching([
            $participant->id => [
                'registered_at' => now(),
                'approved'      => $request->boolean('approved', false),
            ],
        ]);

        return response()->json([
            'message' => 'Participant registered and added to workshop successfully',
            'participant' => new WorkshopParticipantResource($participant),
        ]);
    }

    // بروزرسانی اطلاعات شرکت‌کننده و اطلاعات ارتباط با کارگاه (مثل تایید یا لغو تایید)
    public function update(Request $request, $workshopId, $participantId)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'sometimes|required|string|max:255',
            'name_en'       => 'sometimes|nullable|string|max:255',
            'national_code' => 'sometimes|nullable|string|max:20',
            'phone'         => 'sometimes|required|string|max:20',
            'gender'        => 'sometimes|nullable|in:male,female',
            'approved'      => 'sometimes|boolean',
            'registered_at' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workshop = Workshop::findOrFail($workshopId);
        $participant = Participant::findOrFail($participantId);

        // آپدیت اطلاعات شرکت‌کننده
        $participant->update($request->only([
            'name',
            'name_en',
            'national_code',
            'phone',
            'gender',
        ]));

        // آپدیت اطلاعات ارتباط (pivot)
        $pivotData = $request->only(['approved', 'registered_at']);
        if (!empty($pivotData)) {
            $workshop->participants()->updateExistingPivot($participantId, $pivotData);
        }

        return response()->json([
            'message' => 'Participant and workshop relation updated successfully',
            'participant' => $participant,
        ]);
    }

    // حذف شرکت‌کننده از کارگاه (فقط حذف ارتباط)
    public function destroy(Request $request, $workshopId, $participantId)
    {
        $workshop = Workshop::findOrFail($workshopId);

        $workshop->participants()->detach($participantId);

        return response()->json([
            'message' => 'Participant removed from workshop successfully',
        ]);
    }

    // نمایش لیست شرکت‌کنندگان کارگاه
    public function index($workshopId)
    {
        $workshop = Workshop::with('participants')->findOrFail($workshopId);
        return response()->json([
            'participants' => $workshop->participants,
        ]);
    }

    // تایید یا لغو تایید شرکت‌کننده در کارگاه (از طریق ادمین)
    public function approve(Request $request, $workshopId, $participantId)
    {
        $validator = Validator::make($request->all(), [
            'approved' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workshop = Workshop::findOrFail($workshopId);

        $workshop->participants()->updateExistingPivot($participantId, [
            'approved' => $request->approved,
        ]);

        return response()->json([
            'message' => $request->approved
                ? 'Participant approved successfully'
                : 'Participant approval revoked',
        ]);
    }
}
