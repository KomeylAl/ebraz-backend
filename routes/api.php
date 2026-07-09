<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\RestoreController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MedicalRecordController;

// 👨‍⚕️ پزشک‌ها، مراجعان، ادمین‌ها
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;

// 🧾 نوبت‌دهی و پرداخت
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\PaymentController;

// 🎓 کلاس‌ها و ورک‌شاپ‌ها
use App\Http\Controllers\ClassController;
use App\Http\Controllers\WorkShopController;
use App\Http\Controllers\WorkshopSessionController;
use App\Http\Controllers\WorkshopParticipantController;

// 📝 وبلاگ
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\InitAssessmentController;

use App\Http\Controllers\DepartmentController;

use App\Http\Controllers\v2\V2ResumeController;
use App\Http\Controllers\v2\V2AppointmentController;
use App\Http\Controllers\v2\V2AssessmentController;
use App\Http\Controllers\v2\V2NotificationController;
use App\Http\Controllers\v2\V2DoctorResourceController;


/// ---------------------------
/// 🔐 احراز هویت عمومی
/// ---------------------------
// Route::post('/login', [AuthController::class, 'login']);

Route::post('/auth/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/auth/doctor/login', [AuthController::class, 'doctorLogin']);
Route::post('/auth/client/login', [AuthController::class, 'clientLogin']);

Route::get('/notifications', [NotificationController::class, 'index']);
Route::get('/notifications/unread', [NotificationController::class, 'unreadNotifications']);

// ایجاد نوتیف جدید
Route::post('/notifications', [NotificationController::class, 'store']);
Route::get('/notifications/test', [NotificationController::class, 'test']);

// نوتیف‌های مربوط به کاربر لاگین‌شده
Route::get('/notifications/me', [NotificationController::class, 'userNotifications']);

// مارک کردن نوتیف به عنوان خوانده‌شده
Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

/// ---------------------------
/// ثبت ارزیابی اولیه عمومی
/// ---------------------------
Route::post('/assessments', [InitAssessmentController::class, 'store']);

/// ---------------------------
/// 📚 اطلاعات عمومی
/// ---------------------------
Route::get('/about', [AboutController::class, 'index']);
Route::post('/about/upsert', [AboutController::class, 'upsert']); // می‌تونه فقط برای ادمین باشه

/// ---------------------------
/// 🧾 سیستم پزشکی / مراجعه
/// ---------------------------
Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/clients/{id}/record', [MedicalRecordController::class, 'getClientRecord']);
Route::post('/clients/{id}/record', [MedicalRecordController::class, 'store']);

/// ---------------------------
/// 📆 نوبت پزشکان (عمومی)
/// ---------------------------
Route::prefix('app/doctors/{id}/appointments')->group(function () {
    Route::get('today', [DoctorController::class, 'todaysClients']);
    Route::get('yesterday', [DoctorController::class, 'yesterdaysClients']);
    Route::get('tomorrow', [DoctorController::class, 'tomorrowsClients']);
    Route::get('7-days', [DoctorController::class, 'lastSevenDaysClients']);
    Route::get('30-days', [DoctorController::class, 'last30DaysClients']);
    Route::get('next-30-days', [DoctorController::class, 'next30DaysClients']);
    Route::get('all', [DoctorController::class, 'allClients']);
});
Route::get('/app/doctors/{id}/clients', [DoctorController::class, 'getAllDoctorClients']);

/// ---------------------------
/// 🧠 وبلاگ: نمایش عمومی
/// ---------------------------
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

Route::prefix('v1')->group(function () {
    Route::get('doctors/{doctor}', [DoctorController::class, 'show']);
});

Route::middleware('auth:doctor')->group(function () {
    Route::prefix('v2/doctors')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('resume', [V2ResumeController::class, 'show']);
        Route::post('resume', [V2ResumeController::class, 'store']);
        Route::get('appointments', [V2AppointmentController::class, 'index']);
        Route::get('resources', [V2DoctorResourceController::class, 'index']);
        Route::get('assessments', [V2AssessmentController::class, 'index']);
        Route::get('notifications', [V2NotificationController::class, 'index']);
    });
});

/// ---------------------------
/// 👮‍♂️ مسیرهای محافظت‌شده برای admin
/// ---------------------------
Route::middleware('auth:admin')->group(function () {
    /// 🔐 احراز هویت
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-info', [AuthController::class, 'getUserInfo']);

    /// 👤 مدیریت کاربران
    Route::patch('/user/{id}/edit', [UserController::class, 'editUser']);

    Route::prefix('backup')->group(function () {
        Route::get('/admins', [BackupController::class, 'backupAdmins']);
        Route::get('/doctors', [BackupController::class, 'backupDoctors']);
        Route::get('/doctor-resumes', [BackupController::class, 'backupDoctorResumes']);
        Route::get('/clients', [BackupController::class, 'backupClients']);
        Route::get('/posts', [BackupController::class, 'backupPosts']);
        Route::get('/categories', [BackupController::class, 'backupCategories']);
        Route::get('/tags', [BackupController::class, 'backupTags']);
        Route::get('/workshops', [BackupController::class, 'backupWorkshops']);
        Route::get('/about', [BackupController::class, 'backupAbout']);
    });

    Route::prefix('restore')->group(function () {
        Route::post('/admins', [RestoreController::class, 'restoreAdmins']);
        Route::post('/doctors', [RestoreController::class, 'restoreDoctors']);
        Route::post('/doctor-resumes', [RestoreController::class, 'restoreDoctorResumes']);
        Route::post('/clients', [RestoreController::class, 'restoreClients']);
        Route::post('/posts', [RestoreController::class, 'restorePosts']);
        Route::post('/categories', [RestoreController::class, 'restoreCategories']);
        Route::post('/tags', [RestoreController::class, 'restoreTags']);
        Route::post('/workshops', [RestoreController::class, 'restoreWorkshops']);
        Route::post('/about', [RestoreController::class, 'restoreAbout']);
    });

    /// 🧾 مدیریت نوبت‌ها
    Route::prefix('appointments')->group(function () {
        Route::get('/', [ReferralController::class, 'getAllReferrals']);
        Route::get('{id}', [ReferralController::class, 'getReferral']);
        Route::get('date/{date}', [ReferralController::class, 'getReferralByDate']);
        Route::post('/', [ReferralController::class, 'addReferral']);
        Route::patch('{id}', [ReferralController::class, 'editReferral']);
        Route::delete('{id}', [ReferralController::class, 'deleteReferral']);
    });

    /// 🧾 مدیریت ارزیابی اولیه
    Route::prefix('assessments')->group(function () {
        Route::get('/', [InitAssessmentController::class, 'index']);
        Route::delete('{assessment:id}', [InitAssessmentController::class, 'destroy']);
    });

    Route::get('/payments', [PaymentController::class, 'index']);

    /// 👨‍👩‍👧‍👦 کاربران
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::get('{client:id}', [ClientController::class, 'show']);
        Route::post('/', [ClientController::class, 'store']);
        Route::patch('{id}', [ClientController::class, 'update']);
        Route::delete('{id}', [ClientController::class, 'destroy']);
    });

    Route::prefix('doctors')->group(function () {
        Route::get('{doctor}', [DoctorController::class, 'show']);
        Route::post('/', [DoctorController::class, 'store']);
        Route::post('/{doctor}', [DoctorController::class, 'update']);
        Route::delete('{id}', [DoctorController::class, 'destroy']);
        Route::post('{doctor}/password', [DoctorController::class, 'storePassword']);
        Route::get('{id}/panel/seven-days', [DoctorController::class, 'lastSevenDaysClients']);
        Route::get('{id}/panel/30-days', [DoctorController::class, 'last30DaysClients']);
        Route::get('{id}/panel/today-sms', [DoctorController::class, 'sendTodaysSms']);
        Route::get('{id}/panel/tomorrow-sms', [DoctorController::class, 'sendTomorrowsSms']);
    });

    Route::prefix('doctors/{doctor}')->group(function () {
        Route::get('/resume', [ResumeController::class, 'show']);
        Route::post('/resume', [ResumeController::class, 'store']);
    });

    Route::prefix('admins')->group(function () {
        Route::get('/', [AdminController::class, 'index']);
        Route::get('/rec-admins', [AdminController::class, 'getAllRecAdmins']);
        Route::get('{id}', [AdminController::class, 'getAdmin']);
        Route::post('/', [AdminController::class, 'addAdmin']);
        Route::post('{id}', [AdminController::class, 'editAdmin']);
        Route::delete('{id}', [AdminController::class, 'destroy']);
    });

    /// ✉️ پیامک
    Route::prefix('sms')->group(function () {
        Route::post('/single', [SmsController::class, 'singleSms']);
        Route::post('/multi', [SmsController::class, 'multiSms']);
    });

    /// 🎓 کلاس‌ها
    Route::prefix('classes')->group(function () {
        Route::get('/', [ClassController::class, 'getAllClasses']);
        Route::post('add', [ClassController::class, 'addClass']);
        Route::patch('{id}', [ClassController::class, 'editClass']);
        Route::get('{id}', [ClassController::class, 'getClass']);
        Route::delete('{id}', [ClassController::class, 'deleteClass']);
    });

    /// 🧪 فاکتورها
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices/add', [InvoiceController::class, 'getReferrals']);
});

/// ---------------------------
/// 🎓 ورکشاپ‌ها (sessions / participants)
/// ---------------------------
Route::prefix('workshops')->group(function () {
    Route::get('/', [WorkShopController::class, 'index']);
    Route::post('/', [WorkShopController::class, 'store']);
    Route::post('{id}', [WorkShopController::class, 'update']);
    Route::get('{id}', [WorkShopController::class, 'show']);
    Route::delete('{id}', [WorkShopController::class, 'destroy']);

    Route::prefix('{workshopId}/sessions')->group(function () {
        Route::get('/', [WorkshopSessionController::class, 'index']);
        Route::post('/', [WorkshopSessionController::class, 'store']);
        Route::get('{id}', [WorkshopSessionController::class, 'show']);
        Route::post('{id}', [WorkshopSessionController::class, 'update']);
        Route::delete('{id}', [WorkshopSessionController::class, 'destroy']);
    });

    Route::prefix('{workshop}/participants')->group(function () {
        Route::get('/', [WorkshopParticipantController::class, 'index']);
        Route::post('/', [WorkshopParticipantController::class, 'store']);
        Route::post('{participant}', [WorkshopParticipantController::class, 'update']);
        Route::delete('{participant}', [WorkshopParticipantController::class, 'destroy']);
        Route::patch('{participant}/approve', [WorkshopParticipantController::class, 'approve']);
        Route::patch('{participant}/unapprove', [WorkshopParticipantController::class, 'unapprove']);
    });
});

/// ---------------------------
/// ✍️ مسیرهای مربوط به وبلاگ (ایجاد/ویرایش/حذف)
/// فقط برای admin‌هایی که نقش author دارند
/// ---------------------------
Route::middleware(['auth:admin', 'author.only'])->group(function () {
    // Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::post('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::post('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
    Route::apiResource('tags', TagController::class)->except(['index', 'show']);
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
});

Route::prefix('/posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::post('{post:slug}', [PostController::class, 'update']);
    Route::get('{post:slug}', [PostController::class, 'show']);
    Route::delete('{post:slug}', [PostController::class, 'destroy']);
});

Route::prefix('/categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('{category:slug}', [CategoryController::class, 'show']);
});

Route::prefix('/departments')->group(function () {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::post('/', [DepartmentController::class, 'store']);
    Route::post('{department:slug}', [DepartmentController::class, 'update']);
    Route::get('{department:slug}', [DepartmentController::class, 'show']);
    Route::delete('{department:slug}', [DepartmentController::class, 'destroy']);
});