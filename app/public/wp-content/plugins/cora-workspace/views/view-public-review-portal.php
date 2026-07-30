<?php
/**
 * Public Customer Review Collection Portal — Judge.me / Yotpo Style
 * Cora Studio Workspace Platform
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$request_id     = sanitize_text_field( $_GET['cora_rev_id'] ?? '' );
$client_name    = sanitize_text_field( $_GET['cora_client'] ?? 'Valued Client' );
$project_title  = sanitize_text_field( $_GET['cora_project'] ?? 'Studio Photography & Media' );
$google_url     = get_option( 'cora_google_business_url', 'https://g.page/r/cora_studio/review' );
$workspace_name = get_option( 'cora_workspace_name', 'Cora Studio' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave a Review — <?php echo esc_html( $workspace_name ); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        .star-rating svg { transition: all 0.15s ease-in-out; }
        .star-rating button:hover svg,
        .star-rating button:hover ~ button svg { fill: #EAB308; stroke: #CA8A04; }
        .attr-pill.selected { background-color: #18181B; color: #FFFFFF; border-color: #18181B; }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 font-sans min-h-screen flex items-center justify-center p-4 sm:p-6">

    <div class="max-w-md w-full bg-white border border-zinc-200/80 rounded-3xl shadow-xl overflow-hidden relative">
        <!-- Brand Header -->
        <div class="p-6 bg-zinc-950 text-white text-center relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-zinc-800/40 rounded-full blur-xl pointer-events-none"></div>
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-zinc-800 border border-zinc-700 text-lg font-bold mb-3 shadow-inner">
                ⭐
            </div>
            <h2 class="text-lg font-extrabold tracking-tight"><?php echo esc_html( $workspace_name ); ?></h2>
            <p class="text-xs text-zinc-400 mt-0.5">Share your experience for <?php echo esc_html( $project_title ); ?></p>

            <!-- Verified Anti-Spam Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 mt-3 rounded-full bg-emerald-950/60 border border-emerald-800/60 text-[10px] font-semibold text-emerald-400">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Verified Customer • Anti-Spam Protected</span>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Greeting & Rating Step -->
            <div id="cora-review-step-1" class="text-center space-y-4">
                <div>
                    <h3 class="text-base font-bold text-zinc-900">How was your experience, <?php echo esc_html( strtok( $client_name, ' ' ) ); ?>?</h3>
                    <p class="text-xs text-zinc-500 mt-1">Tap a star rating below to share your honest feedback.</p>
                </div>

                <!-- Star Rating Interactive Selector -->
                <div class="flex justify-center items-center gap-2 star-rating py-2" id="cora-star-container">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <button type="button" onclick="coraSelectPublicRating(<?php echo $i; ?>)" class="p-1 cursor-pointer focus:outline-none transition-transform active:scale-95" data-star="<?php echo $i; ?>" title="<?php echo $i; ?> Stars">
                            <svg viewBox="0 0 24 24" width="36" height="36" stroke="#D4D4D8" stroke-width="1.8" fill="#F4F4F5" class="star-icon star-<?php echo $i; ?>"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </button>
                    <?php endfor; ?>
                </div>

                <div id="cora-rating-label" class="text-xs font-semibold text-zinc-400 h-4">Tap a star to rate</div>
            </div>

            <!-- Detailed Feedback Form (Hidden until star rating selected) -->
            <div id="cora-review-step-2" class="hidden space-y-5">
                <!-- Attribute Pills -->
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-2">What impressed you most? (Select all that apply)</label>
                    <div class="flex flex-wrap gap-2" id="cora-attr-pills">
                        <button type="button" onclick="coraToggleAttr(this)" class="attr-pill px-3 py-1.5 rounded-xl border border-zinc-200 bg-zinc-50 text-xs font-medium text-zinc-700 hover:border-zinc-400 transition-all cursor-pointer inline-flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            <span>Lighting & Framing</span>
                        </button>
                        <button type="button" onclick="coraToggleAttr(this)" class="attr-pill px-3 py-1.5 rounded-xl border border-zinc-200 bg-zinc-50 text-xs font-medium text-zinc-700 hover:border-zinc-400 transition-all cursor-pointer inline-flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            <span>Fast Turnaround</span>
                        </button>
                        <button type="button" onclick="coraToggleAttr(this)" class="attr-pill px-3 py-1.5 rounded-xl border border-zinc-200 bg-zinc-50 text-xs font-medium text-zinc-700 hover:border-zinc-400 transition-all cursor-pointer inline-flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Professional & Punctual</span>
                        </button>
                        <button type="button" onclick="coraToggleAttr(this)" class="attr-pill px-3 py-1.5 rounded-xl border border-zinc-200 bg-zinc-50 text-xs font-medium text-zinc-700 hover:border-zinc-400 transition-all cursor-pointer inline-flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="13.5" cy="6.5" r=".5"></circle><circle cx="17.5" cy="10.5" r=".5"></circle><circle cx="8.5" cy="7.5" r=".5"></circle><circle cx="6.5" cy="12.5" r=".5"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.92 0 1.7-.75 1.7-1.67 0-.42-.16-.8-.43-1.08-.27-.28-.44-.68-.44-1.12 0-.92.75-1.67 1.67-1.67H16c3.31 0 6-2.69 6-6 0-4.96-4.49-9-10-9z"></path></svg>
                            <span>Pristine Editing</span>
                        </button>
                    </div>
                </div>

                <!-- Written Feedback -->
                <div>
                    <label class="block text-xs font-bold text-zinc-700 mb-1">Your Comments / Suggestions</label>
                    <textarea id="cora-public-feedback-text" rows="3" placeholder="Tell us what you liked or how we can improve..." class="w-full p-3 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-900 focus:outline-none focus:border-zinc-950 focus:ring-1 focus:ring-zinc-950"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="button" id="cora-submit-public-btn" onclick="coraSubmitPublicReview()" class="w-full py-3 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl shadow-lg transition-all cursor-pointer flex items-center justify-center gap-2">
                    <span>Submit Review</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>

            <!-- Step 3: Success Screen 4-5 Stars (Confetti + Google Link) -->
            <div id="cora-review-success-high" class="hidden text-center space-y-4 py-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 text-3xl mb-1 shadow-sm">
                    🎉
                </div>
                <h3 class="text-lg font-extrabold text-zinc-900">Thank You, <?php echo esc_html( strtok( $client_name, ' ' ) ); ?>!</h3>
                <p class="text-xs text-zinc-600 leading-relaxed">Your 5-star rating means the world to us! Could you take 5 seconds to post it on our official Google Business page?</p>

                <!-- AI Review Snippet Box -->
                <div class="p-4 bg-zinc-50 border border-zinc-200/80 rounded-2xl text-left space-y-2">
                    <div class="flex items-center justify-between text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                        <span>Pre-written AI Review Snippet</span>
                        <button type="button" onclick="coraCopyPublicSnippet()" class="text-zinc-900 hover:underline cursor-pointer">Copy Text</button>
                    </div>
                    <p id="cora-public-ai-snippet" class="text-xs text-zinc-800 font-mono bg-white p-3 rounded-xl border border-zinc-200 leading-relaxed">
                        "Exceptional photography coverage by <?php echo esc_html( $workspace_name ); ?>! Turnaround was super fast, lighting was flawless, and team was extremely professional. Highly recommended!"
                    </p>
                </div>

                <!-- Google Business Button -->
                <a href="<?php echo esc_url( $google_url ); ?>" target="_blank" rel="noopener noreferrer" class="w-full py-3.5 bg-[#4285F4] hover:bg-blue-600 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/></svg>
                    <span>Post Review on Google Business</span>
                </a>
            </div>

            <!-- Step 3: Success Screen 1-3 Stars (Private Shield) -->
            <div id="cora-review-success-low" class="hidden text-center space-y-4 py-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 text-amber-600 text-3xl mb-1 shadow-sm">
                    🤝
                </div>
                <h3 class="text-lg font-extrabold text-zinc-900">Feedback Submitted Privately</h3>
                <p class="text-xs text-zinc-600 leading-relaxed">Thank you for letting us know! Your feedback has been routed directly to our studio director. We will review your comments and reach out to resolve any issues.</p>
            </div>
        </div>

        <div class="px-6 py-3 bg-zinc-50 border-t border-zinc-100 text-center text-[10px] text-zinc-400">
            Powered by <?php echo esc_html( $workspace_name ); ?> Reputation Engine
        </div>
    </div>

    <script>
        var selectedRating = 0;
        var selectedAttrs = [];
        var requestId = '<?php echo esc_js( $request_id ); ?>';

        function coraSelectPublicRating(rating) {
            selectedRating = rating;
            var labels = ['', 'Poor (1/5)', 'Fair (2/5)', 'Average (3/5)', 'Very Good (4/5)', 'Exceptional (5/5)'];
            document.getElementById('cora-rating-label').textContent = labels[rating];
            document.getElementById('cora-rating-label').className = rating >= 4 ? 'text-xs font-bold text-emerald-600 h-4' : 'text-xs font-bold text-amber-600 h-4';

            // Highlight stars
            var stars = document.querySelectorAll('#cora-star-container button svg');
            stars.forEach(function(svg, idx) {
                if (idx < rating) {
                    svg.style.fill = '#EAB308';
                    svg.style.stroke = '#CA8A04';
                } else {
                    svg.style.fill = '#F4F4F5';
                    svg.style.stroke = '#D4D4D8';
                }
            });

            // Reveal Step 2
            document.getElementById('cora-review-step-2').classList.remove('hidden');
        }

        function coraToggleAttr(btn) {
            btn.classList.toggle('selected');
            var attrText = btn.textContent.trim();
            var index = selectedAttrs.indexOf(attrText);
            if (index > -1) {
                selectedAttrs.splice(index, 1);
            } else {
                selectedAttrs.push(attrText);
            }
        }

        function coraSubmitPublicReview() {
            var text = document.getElementById('cora-public-feedback-text').value.trim();
            var btn = document.getElementById('cora-submit-public-btn');
            btn.disabled = true;
            btn.innerHTML = '<span>Saving...</span>';

            // Send via WP AJAX
            var ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
            var formData = new FormData();
            formData.append('action', 'cora_submit_public_review');
            formData.append('request_id', requestId);
            formData.append('rating', selectedRating);
            formData.append('review_text', text);
            formData.append('attributes', JSON.stringify(selectedAttrs));

            fetch(ajaxUrl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                document.getElementById('cora-review-step-1').classList.add('hidden');
                document.getElementById('cora-review-step-2').classList.add('hidden');

                if (selectedRating >= 4) {
                    document.getElementById('cora-review-success-high').classList.remove('hidden');
                    // Confetti explosion
                    if (typeof confetti === 'function') {
                        confetti({ particleCount: 80, spread: 70, origin: { y: 0.6 } });
                    }
                } else {
                    document.getElementById('cora-review-success-low').classList.remove('hidden');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<span>Submit Review</span>';
                alert('Submission failed. Please try again.');
            });
        }

        function coraCopyPublicSnippet() {
            var text = document.getElementById('cora-public-ai-snippet').textContent.trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
                alert('Snippet copied! Tap below to post on Google.');
            }
        }
    </script>
</body>
</html>
