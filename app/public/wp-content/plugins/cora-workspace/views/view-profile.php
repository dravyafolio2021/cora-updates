<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user = wp_get_current_user();
$first_name = get_user_meta( $user->ID, 'first_name', true );
$last_name  = get_user_meta( $user->ID, 'last_name', true );
$phone      = get_user_meta( $user->ID, 'cora_phone', true );
$avatar_url = get_user_meta( $user->ID, 'cora_avatar_url', true );

// Generate circular initials fallback background color based on name hash
$full_name = trim( $user->display_name );
if ( empty( $full_name ) ) {
    $full_name = $user->user_login;
}
$initials = '';
$words = explode( ' ', $full_name );
foreach ( $words as $w ) {
    $initials .= strtoupper( substr( $w, 0, 1 ) );
}
$initials = substr( $initials, 0, 2 );

// Simple name hash to consistent hex color
$hash = md5( $full_name );
$color_hex = '#' . substr( $hash, 0, 6 );

// Work Info
$role = ! empty( $user->roles ) ? $user->roles[0] : 'subscriber';
$map = array(
    'administrator' => 'Super Admin',
    'cora_manager' => 'Agency Owner',
    'cora_branch_manager' => 'Branch Manager',
    'cora_photographer' => 'Senior Agent',
    'cora_videographer' => 'Agent',
    'cora_drone_pilot' => 'Telecaller',
    'cora_editor' => 'Back Office',
    'cora_viewer' => 'Viewer'
);
$role_label = isset( $map[$role] ) ? $map[$role] : $role;

$agency_id = get_user_meta( $user->ID, 'cora_agency_id', true );
$agencies  = cora_db_get_agencies();
$agency_name = isset( $agencies[$agency_id] ) ? $agencies[$agency_id]['name'] : 'Default Agency';

$branch_id = get_user_meta( $user->ID, 'cora_branch_id', true );
$branches  = cora_db_get_branches();
$branch_name = isset( $branches[$branch_id] ) ? $branches[$branch_id]['name'] : 'Main Branch';

$joined_on = $user->user_registered;
$joined_formatted = date( 'd/m/Y', strtotime( $joined_on ) );

// Fetch active sessions
$session_tokens = WP_Session_Tokens::get_instance( $user->ID );
$sessions = $session_tokens->get_all();
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="cora-page-header flex items-center gap-3">
            <span class="cora-page-emoji text-zinc-900 flex shrink-0">
                <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </span>
            <div>
                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">My Profile</h1>
                <p class="cora-section-desc text-xs text-zinc-500 mt-1">Manage your personal details, work credentials, and active login sessions.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile details & photo -->
        <div class="lg:col-span-2 space-y-6">
            <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-6 shadow-sm space-y-6">
                <h3 class="text-sm font-bold text-zinc-900 border-b border-zinc-100 pb-3">Personal Information</h3>
                
                <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                    <div class="relative group">
                        <?php if ( ! empty( $avatar_url ) ) : ?>
                            <img src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar" id="profile-avatar-img" class="w-20 h-20 rounded-full object-cover border border-zinc-200">
                        <?php else : ?>
                            <div id="profile-avatar-fallback" class="w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-xl border border-zinc-200" style="background-color: <?php echo esc_attr($color_hex); ?>">
                                <?php echo esc_html( $initials ); ?>
                            </div>
                        <?php endif; ?>
                        <button onclick="document.getElementById('avatar-input').click()" class="absolute inset-0 bg-black/45 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="#fff" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        </button>
                        <input type="file" id="avatar-input" accept="image/*" style="display:none" onchange="loadAvatarCrop(event)">
                    </div>
                    
                    <div class="flex-1 space-y-1">
                        <h4 class="text-sm font-bold text-zinc-900"><?php echo esc_html($full_name); ?></h4>
                        <p class="text-xs text-zinc-500"><?php echo esc_html($role_label); ?> · <?php echo esc_html($branch_name); ?></p>
                        <p class="text-[10px] text-zinc-400">Max size 2MB. Square crop is forced.</p>
                    </div>
                </div>

                <form id="profile-info-form" onsubmit="coraSaveProfileInfo(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">First Name</label>
                        <input type="text" id="profile-first-name" value="<?php echo esc_attr( $first_name ); ?>" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Last Name</label>
                        <input type="text" id="profile-last-name" value="<?php echo esc_attr( $last_name ); ?>" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Email Address</label>
                        <input type="email" value="<?php echo esc_attr( $user->user_email ); ?>" disabled class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg bg-zinc-50 text-zinc-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Phone Number</label>
                        <input type="text" id="profile-phone" value="<?php echo esc_attr( $phone ); ?>" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-950" placeholder="+91 99999 99999">
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- Active Sessions list -->
            <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                    <h3 class="text-sm font-bold text-zinc-900">Active Sessions</h3>
                    <button onclick="coraLogOutOtherSessions()" class="text-zinc-600 hover:text-zinc-900 font-bold text-[10px] border border-zinc-200 px-2.5 py-1.5 rounded-lg bg-white hover:bg-zinc-50 cursor-pointer transition-colors shadow-sm">Log Out Other Sessions</button>
                </div>
                
                <div class="divide-y divide-zinc-100">
                    <?php
                    $current_token = wp_get_session_token();
                    foreach ( $sessions as $token_key => $sess ) :
                        $is_current = ( $token_key === $current_token );
                        $login_time = date( 'd M Y, H:i', $sess['login'] );
                        // Parse simple device type
                        $ua = $sess['ua'];
                        $device = 'Unknown Browser';
                        if ( strpos( $ua, 'Chrome' ) !== false ) $device = 'Chrome';
                        elseif ( strpos( $ua, 'Firefox' ) !== false ) $device = 'Firefox';
                        elseif ( strpos( $ua, 'Safari' ) !== false ) $device = 'Safari';
                        
                        $platform = 'Unknown OS';
                        if ( strpos( $ua, 'Macintosh' ) !== false ) $platform = 'macOS';
                        elseif ( strpos( $ua, 'Windows' ) !== false ) $platform = 'Windows';
                        elseif ( strpos( $ua, 'iPhone' ) !== false ) $platform = 'iOS';
                        elseif ( strpos( $ua, 'Android' ) !== false ) $platform = 'Android';
                    ?>
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="text-zinc-400">
                                    <?php if ( strpos( $ua, 'iPhone' ) !== false || strpos( $ua, 'Android' ) !== false ) : ?>
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="5" y="2" width="14" height="20" rx="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                                    <?php else : ?>
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-zinc-800"><?php echo esc_html( "$device on $platform" ); ?> <?php if ($is_current) : ?><span class="text-[9px] font-bold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded ml-1.5">This device</span><?php endif; ?></p>
                                    <p class="text-[10px] text-zinc-400"><?php echo esc_html($sess['ip']); ?> · Logged in: <?php echo esc_html($login_time); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Work Info & Password Update side cards -->
        <div class="space-y-6">
            <!-- Work info details -->
            <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-zinc-900 border-b border-zinc-100 pb-3">Work Information</h3>
                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Role</span>
                        <span class="font-semibold text-zinc-900"><?php echo esc_html($role_label); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Branch</span>
                        <span class="font-semibold text-zinc-900"><?php echo esc_html($branch_name); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Agency</span>
                        <span class="font-semibold text-zinc-900"><?php echo esc_html($agency_name); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Joined On</span>
                        <span class="font-semibold text-zinc-900"><?php echo esc_html($joined_formatted); ?></span>
                    </div>
                </div>
            </div>

            <!-- Password update form -->
            <div class="cora-card bg-white border border-zinc-200/85 rounded-xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-zinc-900 border-b border-zinc-100 pb-3">Security & Password</h3>
                <form id="profile-password-form" onsubmit="coraSaveProfilePassword(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Current Password</label>
                        <input type="password" id="profile-curr-pass" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">New Password</label>
                        <input type="password" id="profile-new-pass" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Confirm New Password</label>
                        <input type="password" id="profile-confirm-pass" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <button type="submit" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ═══ AVATAR CROP DRAWER SHEET ═════════════════════════════════════════════ -->
<div id="cora-avatar-crop-dlg" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="avatar-crop-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-900">Crop Profile Photo</h3>
            <button class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeAvatarCrop()">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 flex flex-col items-center justify-center bg-zinc-50/20">
            <div class="relative w-72 h-72 border border-dashed border-zinc-300 rounded-lg overflow-hidden flex items-center justify-center bg-zinc-100">
                <canvas id="crop-canvas" class="max-w-full max-h-full"></canvas>
            </div>
            <p class="text-[10px] text-zinc-400 mt-4 text-center">Drag inside the canvas selection window to adjust position before saving.</p>
        </div>
        
        <div class="p-5 border-t border-zinc-200 bg-zinc-50/50 flex items-center justify-end gap-3">
            <button onclick="closeAvatarCrop()" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Cancel</button>
            <button onclick="saveCroppedAvatar()" class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Apply Crop</button>
        </div>
    </div>
</div>

<script>
    var origImageSrc = null;
    var cropCanvas = document.getElementById('crop-canvas');
    var cropCtx = cropCanvas.getContext('2d');
    var cropImg = new Image();

    function loadAvatarCrop(e) {
        var file = e.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            window.coraShowToast('File is too large. Max size is 2MB.');
            return;
        }

        var reader = new FileReader();
        reader.onload = function(event) {
            origImageSrc = event.target.result;
            cropImg.onload = function() {
                // Set canvas to forced square size
                cropCanvas.width = 300;
                cropCanvas.height = 300;
                
                // Draw square centered cropped image on canvas
                var size = Math.min(cropImg.width, cropImg.height);
                var x = (cropImg.width - size) / 2;
                var y = (cropImg.height - size) / 2;
                
                cropCtx.drawImage(cropImg, x, y, size, size, 0, 0, 300, 300);
                
                // Slide open drawer
                $('#cora-avatar-crop-dlg').css({'opacity': '1', 'pointer-events': 'auto'});
                $('#avatar-crop-card').css('transform', 'translateX(0)');
            };
            cropImg.src = origImageSrc;
        };
        reader.readAsDataURL(file);
    }

    function closeAvatarCrop() {
        $('#avatar-crop-card').css('transform', 'translateX(100%)');
        setTimeout(function() {
            $('#cora-avatar-crop-dlg').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
        $('#avatar-input').val('');
    }

    function saveCroppedAvatar() {
        var dataUrl = cropCanvas.toDataURL('image/jpeg', 0.85);
        closeAvatarCrop();
        window.coraShowToast('Saving profile photo...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_update_avatar',
            avatar_data: dataUrl,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Profile photo updated.');
                $('#profile-avatar-img').attr('src', res.data.url).show();
                $('#profile-avatar-fallback').hide();
                
                // Update nav avatar if matches
                $('.cora-user-profile img').attr('src', res.data.url);
            } else {
                window.coraShowToast(res.data.message || 'Failed to save avatar.');
            }
        });
    }

    function coraSaveProfileInfo(e) {
        e.preventDefault();
        var fname = $('#profile-first-name').val().trim();
        var lname = $('#profile-last-name').val().trim();
        var phone = $('#profile-phone').val().trim();

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_profile_info',
            first_name: fname,
            last_name: lname,
            phone: phone,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Profile updated successfully.');
            } else {
                window.coraShowToast(res.data.message || 'Failed to update profile.');
            }
        });
    }

    function coraSaveProfilePassword(e) {
        e.preventDefault();
        var curr = $('#profile-curr-pass').val();
        var pass = $('#profile-new-pass').val();
        var confirm = $('#profile-confirm-pass').val();

        if (pass !== confirm) {
            window.coraShowToast('Passwords do not match.');
            return;
        }

        var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!regex.test(pass)) {
            window.coraShowToast('Password must be at least 8 characters and contain one uppercase, one lowercase, and one number.');
            return;
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_change_password',
            current_pass: curr,
            new_pass: pass,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Password updated. Logging you out...');
                setTimeout(function() {
                    window.location.href = '<?php echo home_url( "/workspace/login?password_updated=1" ); ?>';
                }, 1500);
            } else {
                window.coraShowToast(res.data.message || 'Failed to change password.');
            }
        });
    }

    function coraLogOutOtherSessions() {
        if (!confirm('Are you sure you want to log out all other devices?')) return;
        window.coraShowToast('Logging out other devices...');
        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_logout_other_sessions',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Successfully logged out other devices.');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast('Logout failed.');
            }
        });
    }
</script>
