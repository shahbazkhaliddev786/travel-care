<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Profile\UserProfileController;
use App\Http\Controllers\Profile\DoctorProfileController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TagManagementController;
use App\Http\Controllers\Admin\HospitalManagementController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\FooterPageController;
use App\Http\Controllers\Admin\FooterPageManagementController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\AgoraController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FacebookController;
use Illuminate\Http\Request;

// Public routes (no authentication required)
Route::get('/', [AuthController::class, 'showGetStarted'])->name('get-started');
// Google OAuth routes
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Facebook OAuth routes
Route::get('/auth/facebook/redirect', [FacebookController::class, 'redirect'])->name('auth.facebook.redirect');
Route::get('/auth/facebook/callback', [FacebookController::class, 'callback'])->name('auth.facebook.callback');
Route::redirect('/customer-signup', '/customer-signup/basic-info');
Route::get('/customer-signup/basic-info', [AuthController::class, 'showCustomerSignup'])->name('c-signup');
Route::get('/customer-signup/otp', [SignUpController::class, 'showCustomerOtp'])->name('c-otp');
Route::get('/professional-signup/basic-info', [AuthController::class, 'showProfessionalSignup'])->name('p-signup');
Route::post('/professional-signup/basic-info', [SignUpController::class, 'professionalBasicInfo'])->name('p-basic-info');
Route::get('/professional-signup/general-info', [AuthController::class, 'showProfessionalGeneralInfo'])->name('p-general-info');
Route::get('/signin', [SignInController::class, 'showSignIn'])->name('signin');
Route::post('/signin', [SignInController::class, 'login'])->name('login');
Route::get('/logout', [SignInController::class, 'logout'])->name('logout');
Route::post('/customer-signup/{p_name}', [SignUpController::class, 'customerSignup'])->name('c-info');
Route::get('/password-recovery/{recovery_type}', [SignInController::class, 'showSignIn'])->name('recover');
Route::post('/start-recovery', [SignInController::class, 'startPasswordRecovery'])->name('start-recovery');
Route::get('/recovery-otp', [SignInController::class, 'showRecoveryOtp'])->name('recovery-otp');
Route::post('/verify-recovery-otp', [SignInController::class, 'verifyRecoveryOtp'])->name('verify-recovery-otp');
Route::get('/new-password', [SignInController::class, 'showNewPassword'])->name('new-password');
Route::post('/reset-password', [SignInController::class, 'resetPassword'])->name('reset-password');
Route::post('/resend-recovery-otp', [SignInController::class, 'resendRecoveryOtp'])->name('resend-recovery-otp');
Route::post('/professional-signup/general-info', [SignUpController::class, 'professionalSignup'])->name('p-info');
Route::post('/verify-otp', [SignUpController::class, 'verifyOtp'])->name('verify-otp');
Route::post('/resend-otp', [SignUpController::class, 'resendOtp'])->name('resend-otp');

// Footer Pages (public routes)
Route::post('/contact-us', [FooterPageController::class, 'submitContactForm'])->name('footer.contact-us.submit');

// Dynamic footer page route (must be after static routes to avoid conflicts)
Route::get('/{slug}', [FooterPageController::class, 'showDynamicPage'])->name('footer.dynamic')->where('slug', '^(?!admin|api|home|category|public-profile|appointment|payment|schedule|analysis|bills|safe-travel|settings|assistance|services|chats|profile|user-profile|doctor-profile).*$');

// Stripe webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// Protected routes (require authentication)
Route::middleware(['auth.user'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('restrict.doctor.lab');
    Route::get('/category', [HomeController::class, 'category'])->name('category')->middleware('restrict.doctor.lab');
    Route::get('/category/{specialty}', [HomeController::class, 'categoryBySpecialty'])->name('category.specialty')->middleware('restrict.doctor.lab');
    Route::get('/public-profile', [DoctorProfileController::class, 'showPublicProfile'])->name('public-profile');
    Route::get('/public-profile/{id}', [DoctorProfileController::class, 'showPublicProfile'])->name('public-profile.show');
    Route::get('/appointment/{doctor}', [AppointmentController::class, 'show'])->name('appointment');
    Route::get('/payment', [PaymentController::class, 'show'])->name('payment');
    Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
    
    // Stripe payment routes
    Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::post('/payment/confirm', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::get('/payment/methods', [PaymentController::class, 'getPaymentMethods'])->name('payment.methods');
    Route::get('/payment/debug-stripe', [PaymentController::class, 'debugStripe'])->name('payment.debug-stripe');
    Route::post('/payment/migrate-methods', [PaymentController::class, 'migratePaymentMethods'])->name('payment.migrate-methods');
    Route::get('/payment/migrate-methods', [PaymentController::class, 'migratePaymentMethods'])->name('payment.migrate-methods-get');

    // Transaction routes
    Route::get('/transactions', [PaymentController::class, 'listTransactions'])->name('transactions.index');
    Route::get('/transactions/{transactionId}', [PaymentController::class, 'showTransaction'])->name('transactions.show');
    Route::get('/transactions/{transactionId}/receipt', [PaymentController::class, 'showReceipt'])->name('transactions.receipt');
    Route::view('/pass', 'auth.new-password');

    // Nav bar Routes
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/appointment/{transactionId}', [ScheduleController::class, 'getAppointmentDetails'])->name('schedule.appointment.details');

    Route::get('/analysis', function () {
        return view('analysis');
    });

    Route::get('/bills', function () {
        return view('bills');
    });

    // Menu items
    Route::get('/safe-travel', function () {
        return view('safe-travel');
    });

    Route::get('/settings', function () {
        return view('settings');
    });

    Route::get('/services', function () {
        return view('services');
    });

    Route::get('/chats', [ChatsController::class, 'index'])->name('chats.index');
    Route::get('/chats/messages', [ChatsController::class, 'getMessages'])->name('chats.messages');
    Route::post('/chats/send', [ChatsController::class, 'sendMessage'])->name('chats.send');
    Route::post('/chats/mark-read', [ChatsController::class, 'markAsRead'])->name('chats.mark-read');
    Route::post('/chats/set-online', [ChatsController::class, 'setOnline'])->name('chats.set-online');
    Route::post('/chats/set-offline', [ChatsController::class, 'setOffline'])->name('chats.set-offline');
    Route::post('/chats/set-typing', [ChatsController::class, 'setTyping'])->name('chats.set-typing');
    Route::get('/chats/user-status', [ChatsController::class, 'getUserStatus'])->name('chats.user-status');
    Route::post('/chats/user-statuses', [ChatsController::class, 'getMultipleUserStatuses'])->name('chats.user-statuses');
    
    // Agora Video/Audio Call
    Route::post('/agora/token', [AgoraController::class, 'generateToken'])->name('agora.token');

    // User Profile Routes
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/delete-photo', [UserProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
    Route::post('/profile/upload-photo', [UserProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    
    // Public Profile Routes
    Route::get('/user-profile/{userId}', [PublicProfileController::class, 'show'])->name('user-profile.show');

    Route::post('/payment-methods/store', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::post('/payment-methods/store-legacy', [PaymentMethodController::class, 'storeLegacy'])->name('payment-methods.store-legacy');
    Route::delete('/payment-methods/{id}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::post('/payment-methods/setup-intent', [PaymentMethodController::class, 'createSetupIntent'])->name('payment-methods.setup-intent');
    Route::post('/payment-methods/{id}/set-default', [PaymentMethodController::class, 'setDefault'])->name('payment-methods.set-default');

    // Doctor Profile Routes
    Route::get('/doctor-profile', [DoctorProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-service/{id}', [DoctorProfileController::class, 'updateService'])->name('profile.update-service');
    Route::post('/profile/add-service', [DoctorProfileController::class, 'addService'])->name('profile.add-service');
    Route::put('/profile/update-fees', [DoctorProfileController::class, 'updateFees'])->name('profile.update-fees');
    Route::put('/profile/update-working-hours', [DoctorProfileController::class, 'updateWorkingHours'])->name('profile.update-working-hours');
    Route::put('/profile/update-profile', [DoctorProfileController::class, 'updateProfile'])->name('profile.update-profile');
    Route::delete('/profile/delete-profile-image', [DoctorProfileController::class, 'deleteProfileImage'])->name('profile.delete-profile-image');
    Route::post('/profile/add-payment-method', [DoctorProfileController::class, 'addPaymentMethod'])->name('profile.add-payment-method');
    Route::delete('/profile/delete-payment-method/{id}', [DoctorProfileController::class, 'deletePaymentMethod'])->name('profile.delete-payment-method');
    Route::put('/profile/update-paypal-email', [DoctorProfileController::class, 'updatePaypalEmail'])->name('profile.update-paypal-email');
    Route::post('/profile/upload-gallery-image', [DoctorProfileController::class, 'uploadGalleryImage'])->name('profile.upload-gallery-image');
    Route::post('/profile/delete-gallery-image', [DoctorProfileController::class, 'deleteGalleryImage'])->name('profile.delete-gallery-image');
});

// Admin routes (require admin authentication)
Route::prefix('admin')->name('admin.')->middleware(['auth.admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/update-password', [AdminController::class, 'updatePassword'])->name('profile.update-password');
    
    // User Management - Customers
    Route::get('/customers', [UserManagementController::class, 'customers'])->name('customers.index');
    Route::get('/customers/create', [UserManagementController::class, 'createCustomer'])->name('customers.create');
    Route::get('/customers/export', [UserManagementController::class, 'exportCustomers'])->name('customers.export');
    Route::post('/customers', [UserManagementController::class, 'storeCustomer'])->name('customers.store');
    Route::get('/customers/{id}', [UserManagementController::class, 'showCustomer'])->name('customers.show');
    Route::get('/customers/{id}/edit', [UserManagementController::class, 'editCustomer'])->name('customers.edit');
    Route::put('/customers/{id}', [UserManagementController::class, 'updateCustomer'])->name('customers.update');
    Route::delete('/customers/{id}', [UserManagementController::class, 'destroyCustomer'])->name('customers.destroy');
    Route::post('/customers/{id}/toggle-status', [UserManagementController::class, 'toggleCustomerStatus'])->name('customers.toggle-status');
    Route::post('/customers/bulk-action', [UserManagementController::class, 'bulkCustomerAction'])->name('customers.bulk-action');
    
    // User Management - Doctors
    Route::get('/doctors', [UserManagementController::class, 'doctors'])->name('doctors.index');
    Route::get('/doctors/create', [UserManagementController::class, 'createDoctor'])->name('doctors.create');
    Route::get('/doctors/export', [UserManagementController::class, 'exportDoctors'])->name('doctors.export');
    Route::post('/doctors', [UserManagementController::class, 'storeDoctor'])->name('doctors.store');
    Route::get('/doctors/{id}', [UserManagementController::class, 'showDoctor'])->name('doctors.show');
    Route::get('/doctors/{id}/edit', [UserManagementController::class, 'editDoctor'])->name('doctors.edit');
    Route::put('/doctors/{id}', [UserManagementController::class, 'updateDoctor'])->name('doctors.update');
    Route::delete('/doctors/{id}', [UserManagementController::class, 'destroyDoctor'])->name('doctors.destroy');
    Route::post('/doctors/bulk-action', [UserManagementController::class, 'bulkDoctorAction'])->name('doctors.bulk-action');
    Route::post('/doctors/{id}/update-status', [UserManagementController::class, 'updateDoctorStatus'])->name('doctors.update-status');
    Route::post('/doctors/{id}/toggle-video', [UserManagementController::class, 'toggleVideoConsultation'])->name('doctors.toggle-video');
    
    // User Management - Laboratories
    Route::get('/laboratories', [UserManagementController::class, 'laboratories'])->name('laboratories.index');
    Route::get('/laboratories/create', [UserManagementController::class, 'createLaboratory'])->name('laboratories.create');
    Route::post('/laboratories', [UserManagementController::class, 'storeLaboratory'])->name('laboratories.store');
    Route::get('/laboratories/{id}', [UserManagementController::class, 'showLaboratory'])->name('laboratories.show');
    Route::get('/laboratories/{id}/edit', [UserManagementController::class, 'editLaboratory'])->name('laboratories.edit');
    Route::put('/laboratories/{id}', [UserManagementController::class, 'updateLaboratory'])->name('laboratories.update');
    Route::delete('/laboratories/{id}', [UserManagementController::class, 'destroyLaboratory'])->name('laboratories.destroy');
    Route::post('/laboratories/{id}/update-status', [UserManagementController::class, 'updateLaboratoryStatus'])->name('laboratories.update-status');
    Route::post('/laboratories/{id}/toggle-video', [UserManagementController::class, 'toggleLaboratoryVideo'])->name('laboratories.toggle-video');
    Route::get('/laboratories/export', [UserManagementController::class, 'exportLaboratories'])->name('laboratories.export');
    Route::post('/laboratories/bulk-action', [UserManagementController::class, 'bulkLaboratoryAction'])->name('laboratories.bulk-action');
    
    // User Management - Translators
    Route::get('/translators', [UserManagementController::class, 'translators'])->name('translators.index');
    Route::get('/translators/create', [UserManagementController::class, 'createTranslator'])->name('translators.create');
    // Add export route for translators
    Route::get('/translators/export', [UserManagementController::class, 'exportTranslators'])->name('translators.export');
    Route::post('/translators', [UserManagementController::class, 'storeTranslator'])->name('translators.store');
    Route::get('/translators/{id}', [UserManagementController::class, 'showTranslator'])->name('translators.show');
    Route::get('/translators/{id}/edit', [UserManagementController::class, 'editTranslator'])->name('translators.edit');
    Route::put('/translators/{id}', [UserManagementController::class, 'updateTranslator'])->name('translators.update');
    Route::delete('/translators/{id}', [UserManagementController::class, 'destroyTranslator'])->name('translators.destroy');
    Route::post('/translators/{id}/update-status', [UserManagementController::class, 'updateTranslatorStatus'])->name('translators.update-status');
    Route::post('/translators/{id}/toggle-availability', [UserManagementController::class, 'toggleTranslatorAvailability'])->name('translators.toggle-availability');
    // Add bulk-action route for translators
    Route::post('/translators/bulk-action', [UserManagementController::class, 'bulkTranslatorAction'])->name('translators.bulk-action');
    Route::post('/translators/{id}/update-status', [UserManagementController::class, 'updateTranslatorStatus'])->name('translators.update-status');
    Route::post('/translators/{id}/toggle-availability', [UserManagementController::class, 'toggleTranslatorAvailability'])->name('translators.toggle-availability');
    
    // Tag Management
    Route::get('/tags', [TagManagementController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagManagementController::class, 'store'])->name('tags.store');
    // Fixed route order - specific routes before parameterized routes
    Route::get('/tags/service/{id}', [TagManagementController::class, 'getServiceTags'])->name('tags.service');
    Route::get('/tags/{id}/edit', [TagManagementController::class, 'edit'])->name('tags.edit');
    Route::put('/tags/{id}', [TagManagementController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{id}', [TagManagementController::class, 'destroy'])->name('tags.destroy');
    Route::post('/services/{id}/tags', [TagManagementController::class, 'assignToService'])->name('services.assign-tags');
    
    // Hospital Management
    Route::get('/hospitals/export', [HospitalManagementController::class, 'export'])->name('hospitals.export');
    Route::get('/hospitals', [HospitalManagementController::class, 'index'])->name('hospitals.index');
    Route::get('/hospitals/create', [HospitalManagementController::class, 'create'])->name('hospitals.create');
    Route::post('/hospitals', [HospitalManagementController::class, 'store'])->name('hospitals.store');
    Route::get('/hospitals/{id}', [HospitalManagementController::class, 'show'])->name('hospitals.show');
    Route::get('/hospitals/{id}/edit', [HospitalManagementController::class, 'edit'])->name('hospitals.edit');
    Route::put('/hospitals/{id}', [HospitalManagementController::class, 'update'])->name('hospitals.update');
    Route::delete('/hospitals/{id}', [HospitalManagementController::class, 'destroy'])->name('hospitals.destroy');
    Route::post('/hospitals/{id}/status', [HospitalManagementController::class, 'updateStatus'])->name('hospitals.update-status');
    Route::post('/hospitals/{id}/profile', [HospitalManagementController::class, 'updateProfile'])->name('hospitals.update-profile');
    Route::post('/hospitals/{id}/gallery-image', [HospitalManagementController::class, 'uploadGalleryImage'])->name('hospitals.upload-gallery-image');
    Route::delete('/hospitals/{id}/gallery-image', [HospitalManagementController::class, 'deleteGalleryImage'])->name('hospitals.delete-gallery-image');
    Route::post('/hospitals/{id}/seal', [HospitalManagementController::class, 'addSealOfQuality'])->name('hospitals.add-seal');
    Route::delete('/hospitals/{id}/seal', [HospitalManagementController::class, 'removeSealOfQuality'])->name('hospitals.remove-seal');
    Route::post('/hospitals/{id}/payment-methods', [HospitalManagementController::class, 'updatePaymentMethods'])->name('hospitals.update-payment-methods');
    
    // Footer Pages Management
    Route::prefix('footer-pages')->name('footer-pages.')->group(function () {
        // Footer Pages CRUD
        Route::get('/', [FooterPageManagementController::class, 'index'])->name('index');
        Route::get('/{footerPage}', [FooterPageManagementController::class, 'show'])->name('show');
        Route::get('/{footerPage}/edit', [FooterPageManagementController::class, 'edit'])->name('edit');
        Route::put('/{footerPage}', [FooterPageManagementController::class, 'update'])->name('update');
        Route::delete('/{footerPage}', [FooterPageManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{footerPage}/toggle-status', [FooterPageManagementController::class, 'toggleStatus'])->name('toggle-status');
        
        // Social Media Links Management
        Route::get('/social-media/manage', [FooterPageManagementController::class, 'socialMedia'])->name('social-media');
        Route::post('/social-media', [FooterPageManagementController::class, 'storeSocialMedia'])->name('social-media.store');
        Route::put('/social-media/{socialMediaLink}', [FooterPageManagementController::class, 'updateSocialMedia'])->name('social-media.update');
        Route::delete('/social-media/{socialMediaLink}', [FooterPageManagementController::class, 'destroySocialMedia'])->name('social-media.destroy');
        Route::post('/social-media/{socialMediaLink}/toggle-status', [FooterPageManagementController::class, 'toggleSocialMediaStatus'])->name('social-media.toggle-status');
        Route::post('/social-media/update-order', [FooterPageManagementController::class, 'updateSocialMediaOrder'])->name('social-media.update-order');
    });
});