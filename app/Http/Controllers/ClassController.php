<?php

namespace App\Http\Controllers;

use App\Models\ClassesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function getAllClasses() {
        $classes = ClassesModel::all();
        return response()->json($classes, 200);
    }

    public function getClass($id) {
        $class = ClassesModel::query()->where('id', $id)->first();
        if (!$class) {
            return response('کلاس مورد نظر پیدا نشد.', 404);
        }
        return response()->json($class, 200);
    }

    public function addClass(Request $request) {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'week_day' => 'required',
            'time' => 'required',
            'session_dates' => 'required',
            'teacher' => 'required',
        ], [
            'title.required' => 'فیلد عنوان الزامی است.',
            'description.required' => 'فیلد توضیحات الزامی است.',
            'start_date.required' => 'فیلد تاریخ شروع الزامی است.',
            'end_date.required' => 'فیلد تاریخ پایان الزامی است.',
            'week_day.required' => 'فیلد روز هفته الزامی است.',
            'time.required' => 'فیلد زمان الزامی است.',
            'session_dates.required' => 'فیلد تاریخ های برگزاری الزامی است.',
            'teacher.required' => 'فیلد استاد الزامی است.',
        ]);

        $class = ClassesModel::query()->create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'week_day' => $request->week_day,
            'time' => $request->time,
        ]);

        DB::table('class_user')->insert([
            'class_id' => $class->id,
            'user_id' => $request->teacher,
            'role' => 'teacher'
        ]);

        $dates = $request->input('session_dates');
        $students = $request->input('students');

        foreach ($dates as $date) {
            DB::table('class_dates')->insert([
                'class_id' => $class->id,
                'date' => $date,
            ]);
        }

        foreach ($students as $student) {
            DB::table('class_user')->insert([
                'class_id' => $class->id,
                'user_id' => $student,
                'role' => 'student'
            ]);
        }

        return response()->json([$class], 201);

    }

    public function editClass(Request $request, $id) {

    }

    public function deleteClass($id) {
        ClassesModel::query()->where('id', $id)->delete();
        return response(['success'], 200);
    }
}
