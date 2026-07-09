<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\Doctor;
use App\Models\Resume;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\WorkShop;
use App\Models\About;
use Illuminate\Http\Request;
use Storage;

class BackupController extends Controller
{
    public function backupAdmins()
    {
        $admins = Admin::all();
        $jsonData = $admins->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/admins/admins_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'admin',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }

    public function backupDoctors()
    {
        $doctors = Doctor::all();
        $jsonData = $doctors->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/doctors/doctors_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'doctor',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }
    public function backupDoctorResumes()
    {
        $doctorResumes = Resume::all();
        $jsonData = $doctorResumes->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/resumes/doctor_resumes_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'doctorResume',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }
    public function backupClients()
    {
        $clients = Client::all();
        $jsonData = $clients->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/clients/clients_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'client',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }

    public function backupPosts()
    {
        $posts = Post::all();
        $jsonData = $posts->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/posts/posts_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'post',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }

    public function backupCategories()
    {
        $categories = Category::all();
        $jsonData = $categories->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/categories/categories_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'category',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }

    public function backupTags()
    {
        $tags = Tag::all();
        $jsonData = $tags->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/tags/tags_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'tag',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }

    public function backupWorkShops()
    {
        $workshops = WorkShop::all();
        $jsonData = $workshops->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/workshops/workshops_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'workshop',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }

    public function backupAbout()
    {
        $about = About::all();
        $jsonData = $about->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backups/about/about_backup_' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::disk('public')->put($fileName, $jsonData);
        $url = url(Storage::url($fileName));

        Backup::create([
            'type' => 'about',
            'file_path' => $fileName,
            'file_url' => $url,
        ]);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }
}
