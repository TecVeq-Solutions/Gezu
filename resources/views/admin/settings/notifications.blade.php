@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 42px; height: 42px; background: #6366f1;">
                        <i class="las la-bell fs-3"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Notification Protocols & Gateway Setup</h5>
                        <small class="text-muted">Configure outbound SMTP transactional email and Firebase Web Push notifications.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#pushConfigJsonModal" style="height: 44px; display: inline-flex; align-items: center;">
                        <i class="las la-upload me-1"></i> Upload Firebase JSON
                    </button>
                    @if(!empty($fileExists))
                        <a href="{{ route('admin.setting.notification.push.download') }}" class="btn btn-outline-success" style="height: 44px; display: inline-flex; align-items: center;" title="Download uploaded Firebase JSON">
                            <i class="las la-download me-1"></i> Download JSON
                        </a>
                    @endif
                    <a href="{{ safeRoute('admin.notifications.all', [], '/admin/notifications/send-all') }}" class="btn btn-primary" style="height: 44px; display: inline-flex; align-items: center;">
                        <i class="las la-broadcast-tower me-1"></i> Send Community Broadcast
                    </a>
                </div>
            </div>

            <!-- Tab Navigation (HYIPLab Inspired) -->
            <div class="border-bottom bg-light px-4 pt-3">
                <ul class="nav nav-tabs border-0" id="notifyTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold small" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#tab-smtp" type="button" role="tab">
                            <i class="las la-envelope me-1 fs-5"></i> SMTP Outbound Email
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold small" id="firebase-tab" data-bs-toggle="tab" data-bs-target="#tab-firebase" type="button" role="tab">
                            <i class="las la-fire me-1 fs-5 text-warning"></i> Firebase Web Push (FCM)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.settings.notifications') }}" method="POST">
                    @csrf

                    <div class="tab-content" id="notifyTabsContent">

                        <!-- TAB 1: SMTP EMAIL CONFIGURATION -->
                        <div class="tab-pane fade show active" id="tab-smtp" role="tabpanel">
                            <div class="alert alert-info py-2.5 px-3 mb-4 rounded d-flex align-items-center gap-2 small">
                                <i class="las la-info-circle fs-4"></i>
                                <span>SMTP is used for email verifications, password resets, payout receipts, and brand deposit receipts.</span>
                            </div>

                            <div class="row gy-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-dark small mb-1">SMTP Server Host <span class="text-danger">*</span></label>
                                    <input type="text" name="mail_host" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.mailtrap.io" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small mb-1">SMTP Port <span class="text-danger">*</span></label>
                                    <input type="text" name="mail_port" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['mail_port'] ?? '587' }}" placeholder="587" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">SMTP Username</label>
                                    <input type="text" name="mail_username" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['mail_username'] ?? '' }}" placeholder="Enter SMTP Username">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">SMTP Password</label>
                                    <input type="password" name="mail_password" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['mail_password'] ?? '' }}" placeholder="••••••••">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">From Sender Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="mail_from_address" class="form-control" style="height: 44px;" value="{{ $settings['mail_from_address'] ?? 'noreply@gezux.com' }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Sender Display Name <span class="text-danger">*</span></label>
                                    <input type="text" name="mail_from_name" class="form-control" style="height: 44px;" value="{{ $settings['mail_from_name'] ?? 'GEZUX Network' }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: FIREBASE FCM PUSH NOTIFICATIONS (HYIPLab Inspired) -->
                        <div class="tab-pane fade" id="tab-firebase" role="tabpanel">
                            <div class="alert alert-warning py-2.5 px-3 mb-4 rounded d-flex align-items-center gap-2 small">
                                <i class="las la-shield-alt fs-4 text-warning"></i>
                                <span>To deliver Firebase Browser Web Push notifications, your domain must be SSL certified (HTTPS). Web push works across desktop and Android mobile browsers.</span>
                            </div>

                            <div class="form-check form-switch mb-4 p-3 bg-light rounded border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" name="firebase_enabled" id="firebaseEnabled" value="1" {{ !empty($settings['firebase_enabled']) ? 'checked' : '' }} style="width: 44px; height: 22px;">
                                <label class="form-check-label fw-bold text-dark" for="firebaseEnabled" style="padding-top: 2px;">
                                    Enable Firebase Web Push Protocol
                                </label>
                                <small class="text-muted d-block ms-5">Toggles prompt on frontend asking creators and brands to subscribe to push notifications.</small>
                            </div>

                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Firebase API Key</label>
                                    <input type="text" name="firebase_api_key" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['firebase_api_key'] ?? '' }}" placeholder="AIzaSy...">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Auth Domain</label>
                                    <input type="text" name="firebase_auth_domain" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['firebase_auth_domain'] ?? '' }}" placeholder="gezux-app.firebaseapp.com">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small mb-1">Project ID</label>
                                    <input type="text" name="firebase_project_id" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['firebase_project_id'] ?? '' }}" placeholder="gezux-app">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small mb-1">Storage Bucket</label>
                                    <input type="text" name="firebase_storage_bucket" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['firebase_storage_bucket'] ?? '' }}" placeholder="gezux-app.appspot.com">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small mb-1">Messaging Sender ID</label>
                                    <input type="text" name="firebase_messaging_sender_id" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['firebase_messaging_sender_id'] ?? '' }}" placeholder="1029384756">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">App ID</label>
                                    <input type="text" name="firebase_app_id" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['firebase_app_id'] ?? '' }}" placeholder="1:1029384756:web:abcd1234ef">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Measurement ID (Optional)</label>
                                    <input type="text" name="firebase_measurement_id" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['firebase_measurement_id'] ?? '' }}" placeholder="G-XXXXXX">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small mb-1">
                                        <i class="las la-key text-warning me-1"></i> FCM Server Key (Legacy Server Key for Outbound API Push)
                                    </label>
                                    <input type="password" name="firebase_server_key" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['firebase_server_key'] ?? '' }}" placeholder="AAAA...:APA91b...">
                                    <small class="text-muted">Obtained from Firebase Console -> Project Settings -> Cloud Messaging.</small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="border-top mt-4 pt-3 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="height: 44px;">
                            <i class="las la-save me-1"></i> Save Notification Protocol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Upload Firebase Config JSON Modal (HYIPLab Inspired) -->
<div class="modal fade" id="pushConfigJsonModal" tabindex="-1" aria-labelledby="pushConfigJsonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="pushConfigJsonModalLabel">
                    <i class="las la-upload text-primary me-1"></i> Upload Firebase Configuration (.JSON)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.setting.notification.push.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Upload your downloaded Firebase web app config or service account JSON file. Keys will be automatically extracted and populated into system configuration.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select JSON File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".json,application/json" required>
                        <small class="text-muted d-block mt-1">Supported file formats: .json (Max 2MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="las la-cloud-upload-alt me-1"></i> Upload & Sync Keys
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
