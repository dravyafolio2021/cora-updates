<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get form blocks
global $wpdb;
$blocks_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_form_blocks WHERE form_id = %d", $form['id'] ), ARRAY_A );
$blocks = array();
$logic = array();
if ( $blocks_row ) {
    $blocks = json_decode( $blocks_row['blocks_json'], true ) ?: array();
    $logic = json_decode( $blocks_row['logic_json'], true ) ?: array();
}

$styling = json_decode( $form['styling'], true ) ?: array();
$settings = json_decode( $form['settings'], true ) ?: array();
$description = isset( $settings['description'] ) ? $settings['description'] : ( isset( $settings['subtitle'] ) ? $settings['subtitle'] : 'Fill out details below to submit request.' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $form['title'] ); ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #FAFAFA;
            color: #09090b;
        }
        <?php echo isset( $styling['custom_css'] ) ? esc_html( $styling['custom_css'] ) : ''; ?>
    </style>
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center p-0 sm:p-6 md:p-8">
    <div class="w-full sm:max-w-2xl bg-white dark:bg-zinc-900 border-0 sm:border border-zinc-200/90 dark:border-zinc-800/90 rounded-none sm:rounded-2xl p-5 sm:px-8 sm:py-6 shadow-none sm:shadow-[0_12px_40px_-12px_rgba(0,0,0,0.06)] relative flex flex-col min-h-screen sm:min-h-0">


        <!-- Top Platform Brand Banner & Exit Button -->
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-zinc-100 dark:border-zinc-800/80">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold text-xs flex items-center justify-center shadow-xs leading-none">
                    C
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 tracking-tight leading-none">Cora Forms</span>
                    <span class="text-[9.5px] font-medium text-zinc-400 dark:text-zinc-500">Official Communication</span>
                </div>
            </div>
            <button type="button" class="w-7 h-7 rounded-full bg-zinc-100/80 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-400 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-all border-0 cursor-pointer" onclick="if (window.opener) { window.close(); } else { window.history.back(); }">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Multi-Step Header Tracker -->
        <div id="multistep-header-tracker" class="mb-4 space-y-2 hidden">
            <div class="flex items-center justify-between">
                <span id="multistep-badge-text" class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold text-[10px] font-mono uppercase tracking-wider">
                    Step 1 of 1
                </span>
                <span id="multistep-step-title" class="text-xs font-bold text-zinc-800 dark:text-zinc-200"></span>
            </div>
            <div id="multistep-pills-bar" class="flex items-center gap-1.5 w-full"></div>
        </div>

        <!-- Form Header -->
        <div class="mb-4">
            <h1 class="text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight mb-1.5"><?php echo esc_html( $form['title'] ); ?></h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium leading-relaxed"><?php echo esc_html( $description ); ?></p>
        </div>

        <form id="public-cora-form" class="space-y-6" data-form-id="<?php echo esc_attr( $form['id'] ); ?>">
            <!-- Honeypot protection decoy field -->
            <div style="position: absolute; left: -9999px; top: -9999px; height: 0; width: 0; overflow: hidden;" aria-hidden="true">
                <input type="text" name="cora_hp_verify" id="cora-hp-verify" tabindex="-1" value="" autocomplete="off" />
            </div>

            <div id="form-steps-container" class="space-y-5">
                <!-- Inputs injected dynamically here -->
            </div>

            <!-- Navigation Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" id="btn-prev-step" class="hidden h-10 px-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-xs font-bold text-zinc-700 dark:text-zinc-200 flex items-center gap-1.5 transition-all shadow-3xs cursor-pointer">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Back
                </button>
                <div class="flex-1"></div>
                <button type="button" id="btn-next-step" class="h-10 px-6 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-900 dark:hover:bg-zinc-100 text-xs font-bold flex items-center gap-2 transition-all shadow-xs cursor-pointer border-none">
                    Next
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </form>

        <!-- Success / Thank You view -->
        <div id="form-success-container" class="hidden flex-col items-center justify-center text-center py-10 space-y-4">
            <div class="h-14 w-14 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/80 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-2xl font-bold shadow-xs">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div class="space-y-1.5">
                <h2 id="success-title-text" class="text-lg font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight">
                    <?php echo esc_html( isset($settings['thankyou_title']) && !empty($settings['thankyou_title']) ? $settings['thankyou_title'] : 'Response Submitted' ); ?>
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-md leading-relaxed mx-auto" id="success-msg-text">
                    <?php echo esc_html( isset($settings['success_message']) && !empty($settings['success_message']) ? $settings['success_message'] : 'Thank you for your response! We will be in touch shortly.' ); ?>
                </p>
            </div>
            <?php 
            $thankyou_cta_enable = isset($settings['thankyou_cta_enable']) ? $settings['thankyou_cta_enable'] : false;
            $thankyou_cta_text = isset($settings['thankyou_cta_text']) && !empty($settings['thankyou_cta_text']) ? $settings['thankyou_cta_text'] : 'Visit Website';
            $thankyou_cta_url = isset($settings['thankyou_cta_url']) ? $settings['thankyou_cta_url'] : '';
            if ($thankyou_cta_enable && !empty($thankyou_cta_url)) : 
            ?>
            <div class="pt-2">
                <a id="success-cta-btn" href="<?php echo esc_url($thankyou_cta_url); ?>" target="_blank" rel="noopener noreferrer" class="h-10 px-6 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-900 dark:hover:bg-zinc-100 text-xs font-bold inline-flex items-center justify-center gap-2 transition-all shadow-xs no-underline">
                    <span><?php echo esc_html($thankyou_cta_text); ?></span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer Watermark & Security Trust Badge -->
        <div class="mt-8 pt-5 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-[11px] text-zinc-400 dark:text-zinc-500 select-none">
            <div class="flex items-center gap-1.5 font-medium">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
                Powered by <strong class="text-zinc-700 dark:text-zinc-300 font-bold">Cora Forms</strong>
            </div>
            <div class="flex items-center gap-1">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-500"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>256-bit Encrypted</span>
            </div>
        </div>
    </div>

    <!-- Inject data block -->
    <script>
        const formBlocks = <?php echo json_encode( $blocks ); ?>;
        const formLogic = <?php echo json_encode( $logic ); ?>;
        const formSettings = <?php echo json_encode( $settings ); ?>;
        const redirectUrl = "<?php echo esc_url( isset($settings['redirect_url']) ? $settings['redirect_url'] : '' ); ?>";
        const coraRestNonce = "<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>";
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formEl = document.getElementById('public-cora-form');
            const stepsContainer = document.getElementById('form-steps-container');
            const btnPrev = document.getElementById('btn-prev-step');
            const btnNext = document.getElementById('btn-next-step');
            const successContainer = document.getElementById('form-success-container');
            
            let steps = [];
            let currentStepIdx = 0;
            let submittedAnswers = {};

            function recalculateDynamicPricing() {
                let servicesTotal = 0;
                document.querySelectorAll('.cora-service-check:checked').forEach(c => {
                    servicesTotal += parseFloat(c.dataset.price) || 0;
                });

                const paymentBlock = formBlocks.find(b => ['payment', 'stripe_payment', 'upi_id', 'upi_qr'].includes(b.type));
                if (paymentBlock) {
                    const basePrice = parseFloat(paymentBlock.price) || 100;
                    const finalTotal = basePrice + servicesTotal;

                    const totalEl = document.getElementById('checkout-total-amount');
                    if (totalEl) {
                        totalEl.textContent = finalTotal;
                    }
                    
                    const qrLinkEl = document.querySelector('a[href^="upi://pay"]');
                    if (qrLinkEl) {
                        const upiId = paymentBlock.upi_id_value || 'yourname@upi';
                        const newUpiLink = `upi://pay?pa=${encodeURIComponent(upiId)}&am=${finalTotal}&cu=INR`;
                        qrLinkEl.href = newUpiLink;
                    }
                    
                    const textPrice = document.querySelector('.cora-upi-price-text');
                    if (textPrice) {
                        textPrice.textContent = `₹${finalTotal}`;
                    }
                }
            }

            let stepTitles = [];
            // Split blocks list into visual steps / pages
            function partitionBlocks() {
                steps = [];
                stepTitles = [];
                let currentStep = [];
                let currentTitle = 'Step 1';
                formBlocks.forEach(block => {
                    if (block.type === 'page_break') {
                        if (currentStep.length > 0) {
                            steps.push(currentStep);
                            stepTitles.push(currentTitle);
                            currentStep = [];
                        }
                        currentTitle = block.label || `Step ${steps.length + 1}`;
                    } else {
                        currentStep.push(block);
                    }
                });
                if (currentStep.length > 0) {
                    steps.push(currentStep);
                    stepTitles.push(currentTitle);
                }
                if (steps.length === 0) {
                    steps.push([]);
                    stepTitles.push('Step 1');
                }
            }

            function initCustomFieldsLogic(container) {
                // Initialize Signature Pads
                container.querySelectorAll('.signature-canvas').forEach(canvas => {
                    const ctx = canvas.getContext('2d');
                    let drawing = false;
                    
                    canvas.width = canvas.offsetWidth || 300;
                    canvas.height = canvas.offsetHeight || 96;

                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.strokeStyle = '#09090b';

                    const getPos = (e) => {
                        const rect = canvas.getBoundingClientRect();
                        return {
                            x: (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left,
                            y: (e.clientY || (e.touches && e.touches[0].clientY)) - rect.top
                        };
                    };

                    const startDraw = (e) => {
                        drawing = true;
                        const pos = getPos(e);
                        ctx.beginPath();
                        ctx.moveTo(pos.x, pos.y);
                    };

                    const draw = (e) => {
                        if (!drawing) return;
                        e.preventDefault();
                        const pos = getPos(e);
                        ctx.lineTo(pos.x, pos.y);
                        ctx.stroke();
                    };

                    const stopDraw = () => {
                        if (!drawing) return;
                        drawing = false;
                        const hiddenInput = canvas.closest('.cora-signature-pad').querySelector('.signature-data-input');
                        if (hiddenInput) {
                            hiddenInput.value = canvas.toDataURL();
                            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    };

                    canvas.addEventListener('mousedown', startDraw);
                    canvas.addEventListener('mousemove', draw);
                    canvas.addEventListener('mouseup', stopDraw);
                    canvas.addEventListener('mouseleave', stopDraw);

                    canvas.addEventListener('touchstart', startDraw);
                    canvas.addEventListener('touchmove', draw, { passive: false });
                    canvas.addEventListener('touchend', stopDraw);

                    const clearBtn = canvas.closest('.cora-signature-pad').querySelector('.btn-clear-sig');
                    if (clearBtn) {
                        clearBtn.addEventListener('click', () => {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            const hiddenInput = canvas.closest('.cora-signature-pad').querySelector('.signature-data-input');
                            if (hiddenInput) {
                                hiddenInput.value = '';
                                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        });
                    }
                });

                // Initialize Ratings
                container.querySelectorAll('.cora-rating-container').forEach(ratingDiv => {
                    const hiddenInput = ratingDiv.querySelector('input[type="hidden"]');
                    const stars = ratingDiv.querySelectorAll('.star');

                    const updateStars = (val) => {
                        stars.forEach(s => {
                            const sVal = parseInt(s.dataset.val);
                            if (sVal <= val) {
                                s.classList.remove('text-zinc-300');
                                s.classList.add('text-amber-400');
                            } else {
                                s.classList.remove('text-amber-400');
                                s.classList.add('text-zinc-300');
                            }
                        });
                    };

                    stars.forEach(star => {
                        star.addEventListener('click', () => {
                            const val = parseInt(star.dataset.val);
                            if (hiddenInput) {
                                hiddenInput.value = val;
                                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            updateStars(val);
                        });
                        star.addEventListener('mouseenter', () => {
                            updateStars(parseInt(star.dataset.val));
                        });
                    });

                    ratingDiv.addEventListener('mouseleave', () => {
                        updateStars(parseInt(hiddenInput ? hiddenInput.value : 0));
                    });

                    if (hiddenInput && hiddenInput.value) {
                        updateStars(parseInt(hiddenInput.value));
                    }
                });

                // Initialize File Upload Dropzones
                container.querySelectorAll('.cora-file-dropzone').forEach(dropzone => {
                    const hiddenInp = dropzone.querySelector('.cora-file-hidden-input');
                    const textEl = dropzone.querySelector('.dropzone-text');

                    // Click triggers file open
                    dropzone.addEventListener('click', (e) => {
                        if (e.target !== hiddenInp) {
                            hiddenInp.click();
                        }
                    });

                    // Update UI when file selected
                    hiddenInp.addEventListener('change', () => {
                        if (hiddenInp.files && hiddenInp.files.length > 0) {
                            textEl.textContent = hiddenInp.files[0].name;
                            textEl.classList.remove('text-zinc-900');
                            textEl.classList.add('text-emerald-600', 'font-bold');
                            
                            // Save file name in submittedAnswers
                            const label = hiddenInp.dataset.label;
                            const fieldName = hiddenInp.dataset.fieldName;
                            submittedAnswers[label] = hiddenInp.files[0].name;
                            submittedAnswers[fieldName] = hiddenInp.files[0].name;
                            
                            evaluateLogic();
                            evaluateCalculations();
                            savePartialResponse();
                        } else {
                            textEl.textContent = 'Drag & drop an image or video';
                            textEl.classList.remove('text-emerald-600', 'font-bold');
                            textEl.classList.add('text-zinc-900');
                        }
                    });

                    // Drag and Drop behaviors
                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropzone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            dropzone.classList.remove('border-zinc-200');
                            dropzone.classList.add('border-zinc-400', 'bg-zinc-50/50');
                        }, false);
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        dropzone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            dropzone.classList.remove('border-zinc-400', 'bg-zinc-50/50');
                            dropzone.classList.add('border-zinc-200');
                        }, false);
                    });

                    dropzone.addEventListener('drop', (e) => {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        if (files && files.length > 0) {
                            hiddenInp.files = files;
                            // Trigger change event to trigger update UI & save
                            hiddenInp.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }, false);
                });

                // Initialize Booking Slots selection
                container.querySelectorAll('.cora-booking-slots').forEach(slotsDiv => {
                    const blockDiv = slotsDiv.closest('.form-block-item');
                    const hiddenVal = blockDiv.querySelector('.cora-booking-hidden-val');
                    const dateInp = blockDiv.querySelector('.cora-booking-date');
                    const btns = slotsDiv.querySelectorAll('.cora-slot-btn');

                    let selectedDate = dateInp ? dateInp.value : '';
                    let selectedTime = '';

                    const updateBookingValue = () => {
                        if (selectedDate && selectedTime) {
                            const combined = `${selectedDate} at ${selectedTime}`;
                            hiddenVal.value = combined;
                            hiddenVal.dispatchEvent(new Event('change', { bubbles: true }));
                            
                            const label = hiddenVal.dataset.label;
                            const fieldName = hiddenVal.dataset.fieldName;
                            submittedAnswers[label] = combined;
                            submittedAnswers[fieldName] = combined;

                            evaluateLogic();
                            evaluateCalculations();
                            savePartialResponse();
                        }
                    };

                    dateInp?.addEventListener('change', (e) => {
                        selectedDate = e.target.value;
                        updateBookingValue();
                    });

                    btns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            btns.forEach(b => b.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950'));
                            btn.classList.add('bg-zinc-950', 'text-white', 'border-zinc-950');
                            selectedTime = btn.dataset.time;
                            updateBookingValue();
                        });
                    });
                });

                // Initialize Address multi-fields
                container.querySelectorAll('.address-group').forEach(addrDiv => {
                    const blockDiv = addrDiv.closest('.form-block-item');
                    const hiddenVal = blockDiv.querySelector('.cora-address-hidden-val');
                    const street = addrDiv.querySelector('.addr-street');
                    const city = addrDiv.querySelector('.addr-city');
                    const state = addrDiv.querySelector('.addr-state');
                    const zip = addrDiv.querySelector('.addr-zip');

                    const updateAddressValue = () => {
                        const sVal = street.value.trim();
                        const cVal = city.value.trim();
                        const stVal = state.value.trim();
                        const zVal = zip.value.trim();

                        if (sVal || cVal || stVal || zVal) {
                            const formatted = `${sVal}, ${cVal}, ${stVal} ${zVal}`;
                            hiddenVal.value = formatted;
                            hiddenVal.dispatchEvent(new Event('change', { bubbles: true }));
                            const label = hiddenVal.dataset.label;
                            const fieldName = hiddenVal.dataset.fieldName;
                            submittedAnswers[label] = formatted;
                            submittedAnswers[fieldName] = formatted;

                            evaluateLogic();
                            evaluateCalculations();
                            savePartialResponse();
                        }
                    };

                    [street, city, state, zip].forEach(inp => {
                        inp?.addEventListener('input', updateAddressValue);
                    });
                });

                // Initialize Services/Pricing checklist
                container.querySelectorAll('.cora-service-check').forEach(chk => {
                    chk.addEventListener('change', () => {
                        const blockDiv = chk.closest('.form-block-item');
                        const hiddenVal = blockDiv.querySelector('.cora-services-hidden-val');
                        const label = hiddenVal.dataset.label;
                        const fieldName = hiddenVal.dataset.fieldName;

                        const checked = blockDiv.querySelectorAll('.cora-service-check:checked');
                        const list = [];
                        checked.forEach(c => list.push(c.value));
                        
                        const combined = list.join(', ');
                        hiddenVal.value = combined;
                        hiddenVal.dispatchEvent(new Event('change', { bubbles: true }));
                        submittedAnswers[label] = combined;
                        submittedAnswers[fieldName] = combined;

                        recalculateDynamicPricing();

                        evaluateLogic();
                        evaluateCalculations();
                        savePartialResponse();
                    });
                });

                // Restore custom field displays
                container.querySelectorAll('.cora-booking-slots').forEach(slotsDiv => {
                    const blockDiv = slotsDiv.closest('.form-block-item');
                    const hiddenVal = blockDiv.querySelector('.cora-booking-hidden-val');
                    const dateInp = blockDiv.querySelector('.cora-booking-date');
                    const label = hiddenVal.dataset.label;

                    if (submittedAnswers[label]) {
                        const parts = submittedAnswers[label].split(' at ');
                        if (parts[0] && dateInp) dateInp.value = parts[0];
                        if (parts[1]) {
                            const btn = slotsDiv.querySelector(`button[data-time="${parts[1]}"]`);
                            if (btn) btn.classList.add('bg-zinc-950', 'text-white', 'border-zinc-950');
                        }
                    }
                });

                container.querySelectorAll('.address-group').forEach(addrDiv => {
                    const blockDiv = addrDiv.closest('.form-block-item');
                    const hiddenVal = blockDiv.querySelector('.cora-address-hidden-val');
                    const label = hiddenVal.dataset.label;
                    const street = addrDiv.querySelector('.addr-street');
                    const city = addrDiv.querySelector('.addr-city');
                    const state = addrDiv.querySelector('.addr-state');
                    const zip = addrDiv.querySelector('.addr-zip');

                    if (submittedAnswers[label]) {
                        const val = submittedAnswers[label];
                        const parts = val.split(', ');
                        if (parts[0] && street) street.value = parts[0];
                        if (parts[1] && city) city.value = parts[1];
                        if (parts[2]) {
                            const zipParts = parts[2].split(' ');
                            if (zipParts[0] && state) state.value = zipParts[0];
                            if (zipParts[1] && zip) zip.value = zipParts[1];
                        }
                    }
                });

                container.querySelectorAll('.cora-service-check').forEach(chk => {
                    const blockDiv = chk.closest('.form-block-item');
                    const hiddenVal = blockDiv.querySelector('.cora-services-hidden-val');
                    const label = hiddenVal.dataset.label;

                    if (submittedAnswers[label]) {
                        const selectedServices = submittedAnswers[label].split(', ');
                        if (selectedServices.includes(chk.value)) {
                            chk.checked = true;
                        }
                    }
                });

                recalculateDynamicPricing();
            }

            // Create step layouts and render form
            function renderStep(idx) {
                stepsContainer.innerHTML = '';
                const blocks = steps[idx];
                
                blocks.forEach((block, bIdx) => {
                    const blockDiv = document.createElement('div');
                    blockDiv.className = 'form-block-item flex flex-col gap-1.5';
                    blockDiv.dataset.blockId = `block_${idx}_${bIdx}`;                    if (block.type === 'header') {
                        blockDiv.innerHTML = `<h2 class="text-base font-bold text-zinc-900 mt-4">${block.label}</h2>`;
                    } else if (block.type === 'paragraph') {
                        blockDiv.innerHTML = `<p class="text-xs text-zinc-500 leading-relaxed">${block.label}</p>`;
                    } else if (block.type === 'divider') {
                        blockDiv.innerHTML = `<div class="h-px bg-stone-200 my-4"></div>`;
                    } else if (block.type === 'stripe_payment') {
                        const cleanLabel = block.label || 'Stripe Checkout';
                        blockDiv.id = 'field-wrapper-stripe_payment';
                        blockDiv.innerHTML = `
                            <label class="block text-xs font-semibold text-zinc-700 mb-1.5">${cleanLabel}</label>
                            <div class="border border-zinc-200 rounded-2xl p-4 bg-zinc-50/20 flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-zinc-800">Amount to Pay:</span>
                                    <span class="text-sm font-bold text-zinc-950">${block.currency === 'USD' ? '$' : '₹'}${block.price || 0}</span>
                                </div>
                                <div class="text-[10px] text-zinc-400 leading-normal">Checkout is powered securely by Stripe. Clicking submit will redirect you to secure payment portal.</div>
                            </div>
                        `;
                    } else if (block.type === 'upi_id') {
                        const cleanLabel = block.label || 'UPI Payment';
                        blockDiv.id = 'field-wrapper-' + cleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                        blockDiv.innerHTML = `
                            <label class="block text-xs font-semibold text-zinc-700 mb-1.5">${cleanLabel}</label>
                            <div class="border border-zinc-200 rounded-2xl p-4 bg-zinc-50/20 flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-zinc-500 font-medium">Pay via UPI:</span>
                                    <span class="text-xs font-bold text-zinc-950 font-mono">${block.upi_id_value || 'yourname@upi'}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-zinc-450 font-semibold">Amount:</span>
                                    <span class="text-xs font-bold text-zinc-950">₹${block.price || 0}</span>
                                </div>
                                <div class="text-[10px] text-zinc-400 leading-normal">Open any UPI app, scan or type the UPI ID above, and pay the amount.</div>
                                <input type="text" name="upi_ref" placeholder="Enter UPI transaction reference ID" class="w-full h-11 px-4 rounded-xl border border-zinc-200 text-xs font-semibold focus:border-zinc-400 outline-none bg-white transition-all" data-label="${cleanLabel} - UPI Reference" data-field-name="upi_ref" />
                            </div>
                        `;
                    } else if (block.type === 'upi_qr') {
                        const cleanLabel = block.label || 'UPI QR Payment';
                        blockDiv.id = 'field-wrapper-' + cleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                        const upiLink = `upi://pay?pa=${encodeURIComponent(block.upi_id_value || 'yourname@upi')}&am=${block.price || 0}&cu=INR`;
                        blockDiv.innerHTML = `
                            <label class="block text-xs font-semibold text-zinc-700 mb-1.5">${cleanLabel}</label>
                            <div class="border border-zinc-200 rounded-2xl p-4 bg-zinc-50/20 flex flex-col items-center gap-3">
                                <div class="w-28 h-28 bg-white border border-zinc-200 rounded-xl flex items-center justify-center shadow-2xs">
                                    <div class="text-center">
                                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#09090b" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><path d="M14 14h1v1h-1zm3 0h1v1h-1zm0 3h1v1h-1zm-3 3h1v1h-1zm3 0h1v1h-1z"></path></svg>
                                        <div class="text-[8.5px] text-zinc-400 mt-1 uppercase font-bold tracking-wider">QR Code</div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs font-bold text-zinc-955">₹${block.price || 0}</div>
                                    <div class="text-[10px] text-zinc-550 font-mono">${block.upi_id_value || 'yourname@upi'}</div>
                                </div>
                                <a href="${upiLink}" target="_blank" class="w-full py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold text-center transition-all">Open UPI App</a>
                                <input type="text" name="upi_ref" placeholder="Enter UPI transaction ID after payment" class="w-full h-11 px-4 rounded-xl border border-zinc-200 text-xs font-semibold focus:border-zinc-400 outline-none bg-white transition-all" data-label="${cleanLabel} - UPI Ref" data-field-name="upi_ref" />
                            </div>
                        `;
                    } else if (block.type === 'formula') {
                        const cleanLabel = block.label || 'Calculated Value';
                        const fieldName = cleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                        blockDiv.id = 'field-wrapper-' + fieldName;
                        blockDiv.innerHTML = `
                            <div class="border border-zinc-200 rounded-2xl p-4 bg-zinc-50/20 flex items-center justify-between">
                                <span class="text-xs font-semibold text-zinc-800">${cleanLabel}</span>
                                <span class="cora-calculated-value text-sm font-bold text-zinc-950" 
                                      data-expression="${block.expression || ''}" 
                                      data-currency="${block.currency || 'NONE'}" 
                                      data-decimals="${block.decimals !== undefined ? block.decimals : 2}"
                                      data-field-name="${fieldName}"
                                      data-label="${cleanLabel}">0</span>
                            </div>
                        `;
                    } else if (block.type === 'columns') {
                        const colCount = block.columns_count || 2;
                        blockDiv.className = 'form-block-item w-full';
                        
                        let gridHtml = `<div class="grid gap-4 w-full" style="grid-template-columns: repeat(${colCount}, minmax(0, 1fr))">`;
                        
                        (block.column_fields || []).slice(0, colCount).forEach((colFields, colIdx) => {
                            gridHtml += `<div class="flex flex-col gap-4">`;
                            (colFields || []).forEach(subField => {
                                const subCleanLabel = subField.label || 'Input Field';
                                const subFieldName = subCleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                                
                                let subInputHtml = '';
                                if (subField.type === 'long_text' || subField.type === 'textarea') {
                                    subInputHtml = `<textarea name="${subFieldName}" data-label="${subCleanLabel}" data-field-name="${subFieldName}" rows="2" placeholder="Type answer..." class="w-full p-3 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 placeholder-zinc-400 focus:border-zinc-400 outline-none transition-all"></textarea>`;
                                } else if (subField.type === 'dropdown') {
                                    let subOpts = '<option value="">Choose...</option>';
                                    (subField.choices || []).forEach(cOpt => {
                                        let lbl = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                        subOpts += `<option value="${lbl}">${lbl}</option>`;
                                    });
                                    subInputHtml = `<div class="relative w-full"><select name="${subFieldName}" data-label="${subCleanLabel}" data-field-name="${subFieldName}" class="w-full h-11 pl-3 pr-8 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 focus:border-zinc-400 outline-none appearance-none cursor-pointer">${subOpts}</select></div>`;
                                } else if (subField.type === 'date') {
                                    subInputHtml = `<input type="date" name="${subFieldName}" data-label="${subCleanLabel}" data-field-name="${subFieldName}" class="w-full h-11 px-3 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 focus:border-zinc-400 outline-none" />`;
                                } else {
                                    const subInpType = subField.type === 'number' ? 'number' : (subField.type === 'email' ? 'email' : 'text');
                                    subInputHtml = `<input type="${subInpType}" name="${subFieldName}" data-label="${subCleanLabel}" data-field-name="${subFieldName}" placeholder="Type answer..." class="w-full h-11 px-3 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 placeholder-zinc-400 focus:border-zinc-400 outline-none transition-all" />`;
                                }
                                
                                gridHtml += `
                                    <div class="flex flex-col gap-1.5 w-full">
                                        <label class="block text-xs font-semibold text-zinc-700 mb-1.5">${subCleanLabel}</label>
                                        ${subInputHtml}
                                    </div>
                                `;
                            });
                            gridHtml += `</div>`;
                        });
                        
                        gridHtml += `</div>`;
                        blockDiv.innerHTML = gridHtml;
                    } else {
                        // Render standard inputs
                        const cleanLabel = block.label || 'Input Field';
                        const fieldName = cleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                        blockDiv.id = 'field-wrapper-' + fieldName;
                        let inputHtml = '';
 
                        if (block.type === 'long_text') {
                            inputHtml = `<textarea name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" rows="3" placeholder="Type answer..." class="w-full p-4 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 placeholder-zinc-400 focus:border-zinc-400 outline-none transition-all"></textarea>`;
                        } else if (block.type === 'dropdown') {
                            let optsHtml = '<option value="">Choose option...</option>';
                            const bChoices = block.choices || [];
                            bChoices.forEach(cOpt => {
                                let label = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                let val = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                optsHtml += `<option value="${val}">${label}</option>`;
                            });
                            inputHtml = `
                                <div class="relative w-full">
                                    <select name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" class="w-full h-11 pl-4 pr-10 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 focus:border-zinc-400 outline-none transition-all appearance-none cursor-pointer">
                                        ${optsHtml}
                                    </select>
                                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-zinc-400">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </div>
                                </div>
                            `;
                        } else if (block.type === 'checkbox') {
                            let checkboxesHtml = '';
                            const bChoices = block.choices || [];
                            bChoices.forEach((cOpt, cIdx) => {
                                let label = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                let val = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                checkboxesHtml += `
                                    <div class="flex items-center gap-3 py-2.5 px-3.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50/50 transition-all cursor-pointer relative" onclick="const cb = this.querySelector('input'); if (event.target !== cb) { cb.checked = !cb.checked; cb.dispatchEvent(new Event('change', { bubbles: true })); }">
                                        <input type="checkbox" name="${fieldName}[]" data-label="${cleanLabel}" data-field-name="${fieldName}" data-option-index="${cIdx}" value="${val}" class="h-4 w-4 rounded border-zinc-300 text-zinc-950 focus:ring-0 focus:ring-offset-0 focus:outline-none accent-zinc-950 cursor-pointer" />
                                        <span class="text-xs font-semibold text-zinc-800">${label}</span>
                                    </div>
                                `;
                            });
                            inputHtml = `<div class="flex flex-col gap-2">${checkboxesHtml}</div>`;
                        } else if (block.type === 'multiple_choice' || block.type === 'radio') {
                            let radioHtml = '';
                            const bChoices = block.choices || [];
                            bChoices.forEach((cOpt, cIdx) => {
                                let label = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                let val = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                radioHtml += `
                                    <div class="flex items-center gap-3 py-2.5 px-3.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50/50 transition-all cursor-pointer relative" onclick="const rb = this.querySelector('input'); rb.checked = true; rb.dispatchEvent(new Event('change', { bubbles: true }));">
                                        <input type="radio" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" value="${val}" class="h-4 w-4 rounded-full border-zinc-300 text-zinc-950 focus:ring-0 focus:ring-offset-0 focus:outline-none accent-zinc-950 cursor-pointer" />
                                        <span class="text-xs font-semibold text-zinc-800">${label}</span>
                                    </div>
                                `;
                            });
                            inputHtml = `<div class="flex flex-col gap-2">${radioHtml}</div>`;
                        } else if (block.type === 'matrix') {
                            const rows = block.rows || ['Service Quality', 'Speed of Service', 'Overall Value'];
                            const cols = block.columns || ['Poor', 'Average', 'Excellent'];
                            
                            let matrixTableHtml = `<div class="overflow-x-auto border border-zinc-200 rounded-xl bg-white"><table class="w-full text-left border-collapse text-xs">`;
                            matrixTableHtml += `<thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 font-bold uppercase tracking-wider"><tr><th class="p-3"></th>`;
                            cols.forEach(col => {
                                matrixTableHtml += `<th class="p-3 text-center">${col}</th>`;
                            });
                            matrixTableHtml += `</tr></thead><tbody>`;
                            
                            rows.forEach((row, rIdx) => {
                                const rowFieldName = `${fieldName}_row_${rIdx}`;
                                matrixTableHtml += `<tr class="border-b border-zinc-100 hover:bg-zinc-50/30"><td class="p-3 font-semibold text-zinc-700">${row}</td>`;
                                cols.forEach(col => {
                                    matrixTableHtml += `
                                        <td class="p-3 text-center">
                                            <input type="radio" name="${rowFieldName}" data-label="${cleanLabel} - ${row}" data-field-name="${rowFieldName}" value="${col}" class="h-4 w-4 accent-zinc-950 cursor-pointer" />
                                        </td>
                                    `;
                                });
                                matrixTableHtml += `</tr>`;
                            });
                            matrixTableHtml += `</tbody></table></div>`;
                            inputHtml = matrixTableHtml;
                        } else if (block.type === 'file') {
                            inputHtml = `
                                <div class="cora-file-dropzone border border-dashed border-zinc-200 rounded-2xl py-8 px-4 bg-white flex flex-col items-center justify-center gap-3 cursor-pointer transition-all text-center relative" data-field-name="${fieldName}">
                                    <div class="w-12 h-12 rounded-full bg-zinc-50 flex items-center justify-center text-zinc-700 border border-zinc-200/60 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
                                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><polyline points="9 15 12 12 15 15"></polyline></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-zinc-900 dropzone-text">Drag &amp; drop an image or video</p>
                                        <p class="text-[9.5px] text-zinc-450 mt-1">or click to browse (4 MB max)</p>
                                    </div>
                                    <input type="file" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" class="hidden cora-file-hidden-input" />
                                </div>
                            `;
                        } else if (block.type === 'date') {
                            inputHtml = `<input type="date" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" class="w-full h-11 px-4 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 focus:border-zinc-400 outline-none transition-all" />`;
                        } else if (block.type === 'slider') {
                            inputHtml = `
                                <div class="flex items-center gap-3 w-full bg-zinc-50/50 border border-zinc-150 p-3 rounded-xl">
                                    <input type="range" name="${fieldName}" min="0" max="100" value="50" data-label="${cleanLabel}" data-field-name="${fieldName}" class="flex-1 h-1.5 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-zinc-950" oninput="this.nextElementSibling.textContent = this.value" />
                                    <span class="text-xs font-mono font-bold text-zinc-700 w-8 text-right">50</span>
                                </div>
                            `;
                        } else if (block.type === 'signature') {
                            inputHtml = `
                                <div class="cora-signature-pad border border-zinc-200 rounded-2xl p-3 bg-zinc-50/10 flex flex-col gap-2.5">
                                    <canvas class="signature-canvas w-full h-24 bg-white border border-zinc-200 rounded-xl" style="touch-action: none;"></canvas>
                                    <div class="flex justify-between items-center px-1">
                                        <button type="button" class="btn-clear-sig px-2.5 h-7 rounded-lg text-[10px] font-bold text-zinc-500 hover:text-zinc-800 bg-white border border-zinc-200 transition-all hover:bg-zinc-50 cursor-pointer">Clear</button>
                                        <span class="text-[10px] font-bold text-zinc-400 tracking-wider">Sign here</span>
                                    </div>
                                    <input type="hidden" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" class="signature-data-input" />
                                </div>
                            `;
                        } else if (block.type === 'rating') {
                            inputHtml = `
                                <div class="flex items-center gap-1.5 text-2xl cursor-pointer cora-rating-container py-2.5 px-4 bg-zinc-50/10 border border-zinc-200 rounded-xl w-fit" data-field-name="${fieldName}">
                                    <input type="hidden" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" value="0" />
                                    <span class="star text-zinc-300 hover:text-amber-400 transition-colors duration-150" data-val="1">★</span>
                                    <span class="star text-zinc-300 hover:text-amber-400 transition-colors duration-150" data-val="2">★</span>
                                    <span class="star text-zinc-300 hover:text-amber-400 transition-colors duration-150" data-val="3">★</span>
                                    <span class="star text-zinc-300 hover:text-amber-400 transition-colors duration-150" data-val="4">★</span>
                                    <span class="star text-zinc-300 hover:text-amber-400 transition-colors duration-150" data-val="5">★</span>
                                </div>
                            `;
                        } else if (block.type === 'booking') {
                            inputHtml = `
                                <div class="border border-zinc-200 rounded-2xl p-4 bg-zinc-50/20 flex flex-col gap-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10.5px] font-semibold text-zinc-500 mb-1">Select Date</span>
                                        <input type="date" class="cora-booking-date w-full h-11 px-4 rounded-xl border border-zinc-200 text-xs font-semibold focus:border-zinc-400 outline-none bg-white transition-all" min="${new Date().toISOString().split('T')[0]}" />
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10.5px] font-semibold text-zinc-500 mb-1">Available Time Slots</span>
                                        <div class="grid grid-cols-3 gap-2 cora-booking-slots mt-1.5">
                                            <button type="button" class="cora-slot-btn py-2.5 px-3 border border-zinc-200 bg-white rounded-xl text-xs font-semibold text-center hover:bg-zinc-50 transition-all cursor-pointer" data-time="10:00 AM">10:00 AM</button>
                                            <button type="button" class="cora-slot-btn py-2.5 px-3 border border-zinc-200 bg-white rounded-xl text-xs font-semibold text-center hover:bg-zinc-50 transition-all cursor-pointer" data-time="11:30 AM">11:30 AM</button>
                                            <button type="button" class="cora-slot-btn py-2.5 px-3 border border-zinc-200 bg-white rounded-xl text-xs font-semibold text-center hover:bg-zinc-50 transition-all cursor-pointer" data-time="01:00 PM">01:00 PM</button>
                                            <button type="button" class="cora-slot-btn py-2.5 px-3 border border-zinc-200 bg-white rounded-xl text-xs font-semibold text-center hover:bg-zinc-50 transition-all cursor-pointer" data-time="02:30 PM">02:30 PM</button>
                                            <button type="button" class="cora-slot-btn py-2.5 px-3 border border-zinc-200 bg-white rounded-xl text-xs font-semibold text-center hover:bg-zinc-50 transition-all cursor-pointer" data-time="04:00 PM">04:00 PM</button>
                                            <button type="button" class="cora-slot-btn py-2.5 px-3 border border-zinc-200 bg-white rounded-xl text-xs font-semibold text-center hover:bg-zinc-50 transition-all cursor-pointer" data-time="05:30 PM">05:30 PM</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" class="cora-booking-hidden-val" ${block.required ? 'required' : ''} />
                                </div>
                            `;
                        } else if (block.type === 'address') {
                            inputHtml = `
                                <div class="border border-zinc-200 rounded-2xl p-4 bg-zinc-50/20 flex flex-col gap-3 address-group">
                                    <input type="text" class="addr-street w-full h-11 px-4 rounded-xl border border-zinc-200 text-xs font-semibold focus:border-zinc-400 outline-none bg-white transition-all" placeholder="Street Address" />
                                    <div class="grid grid-cols-3 gap-2">
                                        <input type="text" class="addr-city text-xs p-2.5 bg-white border border-zinc-200 rounded-lg focus:border-zinc-400 outline-none" placeholder="City" />
                                        <input type="text" class="addr-state text-xs p-2.5 bg-white border border-zinc-200 rounded-lg focus:border-zinc-400 outline-none" placeholder="State" />
                                        <input type="text" class="addr-zip text-xs p-2.5 bg-white border border-zinc-200 rounded-lg focus:border-zinc-400 outline-none" placeholder="ZIP Code" />
                                    </div>
                                    <input type="hidden" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" class="cora-address-hidden-val" ${block.required ? 'required' : ''} />
                                </div>
                            `;
                        } else if (block.type === 'services_checklist') {
                            const choices = block.choices || [];
                            let checklistHtml = `<div class="flex flex-col gap-2 w-full">`;
                            choices.forEach((c, cIdx) => {
                                checklistHtml += `
                                    <label class="flex items-center justify-between p-3.5 bg-white border border-zinc-200 rounded-xl text-xs font-semibold cursor-pointer hover:bg-zinc-50/50 transition-all select-none">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" class="cora-service-check h-4 w-4 accent-zinc-950 rounded cursor-pointer" data-price="${c.price || 0}" data-service="${c.label}" value="${c.label}" />
                                            <span class="text-zinc-800">${c.label}</span>
                                        </div>
                                        <span class="text-zinc-500 font-mono">₹${c.price || 0}</span>
                                    </label>
                                `;
                            });
                            checklistHtml += `
                                <input type="hidden" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" class="cora-services-hidden-val" ${block.required ? 'required' : ''} />
                                </div>
                            `;
                            inputHtml = checklistHtml;
                        } else {
                            // Text, number, email, phone
                            const inpType = block.type === 'number' ? 'number' : (block.type === 'email' ? 'email' : 'text');
                            inputHtml = `<input type="${inpType}" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" placeholder="Type answer..." class="w-full h-11 px-4 rounded-xl border border-zinc-200 bg-white text-xs font-semibold text-zinc-900 placeholder-zinc-400 focus:border-zinc-400 outline-none transition-all" />`;
                        }
 
                        blockDiv.innerHTML = `
                            <label class="block text-xs font-semibold text-zinc-700 mb-1.5">${cleanLabel}</label>
                            ${inputHtml}
                        `;
                    }
                    stepsContainer.appendChild(blockDiv);
                });

                // Set initial values
                stepsContainer.querySelectorAll('input, select, textarea').forEach(inp => {
                    const label = inp.dataset.label;
                    if (submittedAnswers[label] !== undefined) {
                        if (inp.type === 'checkbox') {
                            if (inp.name.endsWith('[]')) {
                                const vals = Array.isArray(submittedAnswers[label]) ? submittedAnswers[label] : [submittedAnswers[label]];
                                inp.checked = vals.includes(inp.value);
                            } else {
                                inp.checked = submittedAnswers[label] === 'true';
                            }
                        } else if (inp.type === 'radio') {
                            inp.checked = (inp.value === submittedAnswers[label]);
                        } else {
                            inp.value = submittedAnswers[label];
                        }
                    }

                    // Attach change listeners for real-time saving and drop-off analytics
                    ['input', 'change'].forEach(evtType => {
                        inp.addEventListener(evtType, function() {
                            // Clear validation error inline
                            const parentBlock = inp.closest('.form-block-item');
                            if (parentBlock) {
                                const errorDiv = parentBlock.querySelector('.cora-field-error');
                                if (errorDiv) errorDiv.remove();
                            }

                            const fieldName = inp.dataset.fieldName;
                            if (inp.type === 'checkbox') {
                                if (inp.name.endsWith('[]')) {
                                    const checkedBoxes = Array.from(stepsContainer.querySelectorAll(`input[name="${inp.name}"]:checked`));
                                    const values = checkedBoxes.map(cb => cb.value);
                                    submittedAnswers[label] = values;
                                    submittedAnswers[fieldName] = values;
                                } else {
                                    submittedAnswers[label] = inp.checked ? 'true' : 'false';
                                    submittedAnswers[fieldName] = inp.checked ? 1 : 0;
                                }
                            } else {
                                submittedAnswers[label] = inp.value;
                                submittedAnswers[fieldName] = inp.value;
                            }
                            evaluateLogic();
                            evaluateCalculations();
                            savePartialResponse();
                        });
                    });
                });

                evaluateLogic();
                evaluateCalculations();
                initCustomFieldsLogic(stepsContainer);

                // Update progress percentage
                const progressPct = steps.length > 1 ? ((idx + 1) / steps.length) * 100 : 100;
                const progressEl = document.getElementById('form-progress-indicator');
                if (progressEl) progressEl.style.width = `${progressPct}%`;

                // Update Multi-step tracker UI
                const trackerContainer = document.getElementById('multistep-header-tracker');
                if (steps.length > 1 && trackerContainer) {
                    trackerContainer.classList.remove('hidden');
                    
                    const badgeText = document.getElementById('multistep-badge-text');
                    if (badgeText) badgeText.textContent = `Step ${idx + 1} of ${steps.length}`;
                    
                    const stepTitleEl = document.getElementById('multistep-step-title');
                    if (stepTitleEl) stepTitleEl.textContent = stepTitles[idx] || `Step ${idx + 1}`;
                    
                    const pillsBar = document.getElementById('multistep-pills-bar');
                    if (pillsBar) {
                        pillsBar.innerHTML = '';
                        for (let s = 0; s < steps.length; s++) {
                            const pill = document.createElement('div');
                            pill.style.flex = '1';
                            if (s < idx) {
                                pill.className = 'h-1.5 rounded-full bg-zinc-950 dark:bg-white transition-all';
                            } else if (s === idx) {
                                pill.className = 'h-1.5 rounded-full bg-zinc-950 dark:bg-white ring-2 ring-zinc-950/20 dark:ring-white/20 transition-all';
                            } else {
                                pill.className = 'h-1.5 rounded-full bg-zinc-200 dark:bg-zinc-800 transition-all';
                            }
                            pillsBar.appendChild(pill);
                        }
                    }
                } else if (trackerContainer) {
                    trackerContainer.classList.add('hidden');
                }

                // Configure Nav Buttons
                if (idx === 0) {
                    btnPrev.classList.add('hidden');
                } else {
                    btnPrev.classList.remove('hidden');
                }

                if (idx === steps.length - 1) {
                    btnNext.textContent = (formSettings && formSettings.submit_button_text) || 'Submit';
                } else {
                    btnNext.textContent = 'Next';
                }
            }

            // Client-side validations for current step
            function validateCurrentStep() {
                let isValid = true;
                let firstInvalidEl = null;

                // Clear all existing errors
                stepsContainer.querySelectorAll('.cora-field-error').forEach(el => el.remove());

                const blocksOnStep = steps[currentStepIdx] || [];
                blocksOnStep.forEach((block, bIdx) => {
                    const blockDiv = stepsContainer.querySelector(`[data-block-id="block_${currentStepIdx}_${bIdx}"]`);
                    if (!blockDiv) return;

                    const cleanLabel = block.label || 'Input Field';
                    const fieldName = cleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');

                    let blockError = '';

                    // 1. Required field checks
                    if (block.required) {
                        if (block.type === 'checkbox' || block.type === 'multiple_choice' || block.type === 'radio') {
                            const checked = blockDiv.querySelectorAll('input:checked');
                            if (checked.length === 0) {
                                blockError = 'Please select at least one option.';
                            }
                        } else if (block.type === 'signature') {
                            const sigVal = blockDiv.querySelector('.signature-data-input')?.value;
                            if (!sigVal) {
                                blockError = 'Please sign before submitting.';
                            }
                        } else if (block.type === 'booking') {
                            const bookingVal = blockDiv.querySelector('.cora-booking-hidden-val')?.value;
                            if (!bookingVal) {
                                blockError = 'Please select a date and time slot.';
                            }
                        } else if (block.type === 'address') {
                            const street = blockDiv.querySelector('.addr-street')?.value.trim();
                            const city = blockDiv.querySelector('.addr-city')?.value.trim();
                            const state = blockDiv.querySelector('.addr-state')?.value.trim();
                            const zip = blockDiv.querySelector('.addr-zip')?.value.trim();
                            if (!street || !city || !state || !zip) {
                                blockError = 'Please fill out all address fields.';
                            }
                        } else if (block.type === 'services_checklist') {
                            const checked = blockDiv.querySelectorAll('.cora-service-check:checked');
                            if (checked.length === 0) {
                                blockError = 'Please select at least one service.';
                            }
                        } else if (block.type === 'dropdown') {
                            const selVal = blockDiv.querySelector('select')?.value;
                            if (!selVal) {
                                blockError = 'Please select an option.';
                            }
                        } else if (block.type === 'columns') {
                            (block.column_fields || []).forEach(colFields => {
                                (colFields || []).forEach(subField => {
                                    if (subField.required) {
                                        const subCleanLabel = subField.label || 'Input Field';
                                        const subFieldName = subCleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                                        const subEl = blockDiv.querySelector(`[name="${subFieldName}"]`);
                                        if (subEl && !subEl.value.trim()) {
                                            blockError = `Field "${subCleanLabel}" is required.`;
                                        }
                                    }
                                });
                            });
                        } else if (!['header', 'paragraph', 'divider', 'formula'].includes(block.type)) {
                            const val = blockDiv.querySelector('input, textarea')?.value.trim();
                            if (!val) {
                                blockError = 'This field is required.';
                            }
                        }
                    }

                    // 2. Email format validation
                    if (!blockError && block.type === 'email') {
                        const emailVal = blockDiv.querySelector('input')?.value.trim();
                        if (emailVal) {
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!emailRegex.test(emailVal)) {
                                blockError = 'Please enter a valid email address.';
                            }
                        }
                    }

                    // 3. Phone format validation
                    if (!blockError && block.type === 'phone') {
                        const phoneVal = blockDiv.querySelector('input')?.value.trim();
                        if (phoneVal) {
                            const phoneRegex = /^\+?[\d\s\-()]{7,}$/;
                            if (!phoneRegex.test(phoneVal)) {
                                blockError = 'Please enter a valid phone number.';
                            }
                        }
                    }

                    // Show inline error
                    if (blockError) {
                        isValid = false;
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'cora-field-error text-red-500 text-[10.5px] mt-1 font-semibold';
                        errorDiv.textContent = blockError;
                        blockDiv.appendChild(errorDiv);

                        if (!firstInvalidEl) {
                            firstInvalidEl = blockDiv;
                        }
                    }
                });

                if (!isValid && firstInvalidEl) {
                    firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const inp = firstInvalidEl.querySelector('input, select, textarea');
                    if (inp) inp.focus();
                }

                return isValid;
            }

            // Sync partial entry to server
            function savePartialResponse() {
                const formId = formEl.dataset.formId;
                const hpVal = document.getElementById('cora-hp-verify') ? document.getElementById('cora-hp-verify').value : '';
                
                fetch(`/wp-json/cora/v1/forms/${formId}/submit`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': coraRestNonce
                    },
                    body: JSON.stringify({
                        submitted_data: submittedAnswers,
                        is_partial: 1,
                        cora_hp_verify: hpVal
                    })
                }).catch(err => console.error("Error saving partial submission:", err));
            }

            // Navigation Actions
            btnPrev.addEventListener('click', function() {
                if (currentStepIdx > 0) {
                    currentStepIdx--;
                    renderStep(currentStepIdx);
                }
            });

            btnNext.addEventListener('click', function() {
                // Collect current values
                stepsContainer.querySelectorAll('input, select, textarea').forEach(inp => {
                    const label = inp.dataset.label;
                    if (!label) return;
                    const fieldName = inp.dataset.fieldName;
                    if (inp.type === 'checkbox') {
                        if (inp.name.endsWith('[]')) {
                            const checkedBoxes = Array.from(stepsContainer.querySelectorAll(`input[name="${inp.name}"]:checked`));
                            const values = checkedBoxes.map(cb => cb.value);
                            submittedAnswers[label] = values;
                            if (fieldName) submittedAnswers[fieldName] = values;
                        } else {
                            submittedAnswers[label] = inp.checked ? 'true' : 'false';
                            if (fieldName) submittedAnswers[fieldName] = inp.checked ? 1 : 0;
                        }
                    } else if (inp.type === 'radio') {
                        if (inp.checked) {
                            submittedAnswers[label] = inp.value;
                            if (fieldName) submittedAnswers[fieldName] = inp.value;
                        }
                    } else {
                        submittedAnswers[label] = inp.value;
                        if (fieldName) submittedAnswers[fieldName] = inp.value;
                    }
                });

                if (!validateCurrentStep()) {
                    return;
                }

                if (currentStepIdx < steps.length - 1) {
                    currentStepIdx++;
                    renderStep(currentStepIdx);
                } else {
                    // Final Submit
                    submitFinalResponse();
                }
            });

            function submitFinalResponse() {
                const formId = formEl.dataset.formId;
                btnNext.disabled = true;
                btnNext.textContent = 'Submitting...';

                const hpVal = document.getElementById('cora-hp-verify') ? document.getElementById('cora-hp-verify').value : '';
                const hasStripe = formBlocks.some(b => b.type === 'stripe_payment');
                
                function showErrorBanner(message) {
                    const existing = document.getElementById('cora-error-banner');
                    if (existing) existing.remove();
                    const banner = document.createElement('div');
                    banner.id = 'cora-error-banner';
                    banner.className = 'w-full p-4 mb-4 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl';
                    banner.textContent = message;
                    formEl.prepend(banner);
                    banner.scrollIntoView({ behavior: 'smooth' });
                }

                if (hasStripe) {
                    btnNext.textContent = 'Connecting checkout...';
                    fetch(`/wp-json/cora/v1/forms/${formId}/submit`, {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': coraRestNonce
                        },
                        body: JSON.stringify({
                            submitted_data: submittedAnswers,
                            is_partial: 0,
                            cora_hp_verify: hpVal
                        })
                    })
                    .then(() => {
                        return fetch(`/wp-json/cora/v1/forms/${formId}/pay`, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': coraRestNonce
                            }
                        });
                    })
                    .then(res => res.json())
                    .then(payData => {
                        if (payData.url) {
                            window.location.href = payData.url;
                        } else {
                            showErrorBanner("Payment configuration setup failed. Please contact the administrator.");
                            btnNext.disabled = false;
                            btnNext.textContent = (formSettings && formSettings.submit_button_text) || 'Submit';
                        }
                    })
                    .catch(err => {
                        console.error("Payment error:", err);
                        showErrorBanner("Checkout connection failed. Please check your network and try again.");
                        btnNext.disabled = false;
                        btnNext.textContent = (formSettings && formSettings.submit_button_text) || 'Submit';
                    });
                    return;
                }

                fetch(`/wp-json/cora/v1/forms/${formId}/submit`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': coraRestNonce
                    },
                    body: JSON.stringify({
                        submitted_data: submittedAnswers,
                        is_partial: 0,
                        cora_hp_verify: hpVal
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        formEl.classList.add('hidden');
                        successContainer.classList.remove('hidden');
                        successContainer.classList.add('flex');
                        
                        if (redirectUrl) {
                            setTimeout(() => {
                                window.location.href = redirectUrl;
                            }, 1500);
                        }
                    } else {
                        showErrorBanner("Submission failed. Please try again.");
                        btnNext.disabled = false;
                        btnNext.textContent = (formSettings && formSettings.submit_button_text) || 'Submit';
                    }
                })
                .catch(err => {
                    console.error("Submit error:", err);
                    showErrorBanner("Submission error. Please check your internet connection.");
                    btnNext.disabled = false;
                    btnNext.textContent = (formSettings && formSettings.submit_button_text) || 'Submit';
                });
            }

            // Dynamic Conditional Logic Evaluator
            function evaluateLogic() {
                if (!formLogic || formLogic.length === 0) return;

                formLogic.forEach(rule => {
                    const fieldVal = submittedAnswers[rule.field];
                    const wrapper = document.getElementById('field-wrapper-' + rule.target);
                    if (!wrapper) return;

                    let conditionMet = false;
                    if (rule.condition === 'equals' && String(fieldVal) === String(rule.value)) {
                        conditionMet = true;
                    } else if (rule.condition === 'not_equals' && String(fieldVal) !== String(rule.value)) {
                        conditionMet = true;
                    } else if (rule.condition === 'contains' && String(fieldVal).includes(rule.value)) {
                        conditionMet = true;
                    }

                    if (rule.action === 'show') {
                        if (conditionMet) {
                            wrapper.classList.remove('hidden');
                        } else {
                            wrapper.classList.add('hidden');
                        }
                    } else if (rule.action === 'hide') {
                        if (conditionMet) {
                            wrapper.classList.add('hidden');
                        } else {
                            wrapper.classList.remove('hidden');
                        }
                    }
                });
            }

            // Math Expression Calculated Fields Solver
            function evaluateCalculations() {
                function getFieldValue(varName) {
                    const block = formData.blocks.find(b => {
                        const clean = (b.label || '').toLowerCase().replace(/[^a-z0-9]/g, '_');
                        return clean === varName;
                    });
                    if (!block) return 0;
                    
                    const fieldName = (block.label || '').toLowerCase().replace(/[^a-z0-9]/g, '_');
                    
                    if (block.type === 'dropdown') {
                        const val = submittedAnswers[fieldName] || '';
                        const choice = (block.choices || []).find(c => {
                            let label = typeof c === 'object' ? c.label : c;
                            return label === val;
                        });
                        if (choice && typeof choice === 'object' && choice.score !== undefined) {
                            return parseFloat(choice.score) || 0;
                        }
                        return parseFloat(val) || 0;
                    } else if (block.type === 'checkbox') {
                        const vals = submittedAnswers[fieldName] || [];
                        const valArray = Array.isArray(vals) ? vals : [vals];
                        let sum = 0;
                        valArray.forEach(v => {
                            const choice = (block.choices || []).find(c => {
                                let label = typeof c === 'object' ? c.label : c;
                                return label === v;
                            });
                            if (choice && typeof choice === 'object' && choice.score !== undefined) {
                                sum += parseFloat(choice.score) || 0;
                            } else {
                                sum += parseFloat(v) || 0;
                            }
                        });
                        return sum;
                    } else {
                        return parseFloat(submittedAnswers[fieldName]) || 0;
                    }
                }

                stepsContainer.querySelectorAll('.cora-calculated-value').forEach(el => {
                    const expression = el.dataset.expression;
                    if (!expression) return;

                    let evalExpr = expression;
                    const placeholderMatches = expression.match(/\{([^}]+)\}/g) || [];
                    placeholderMatches.forEach(placeholder => {
                        const varName = placeholder.slice(1, -1).trim();
                        const val = getFieldValue(varName);
                        evalExpr = evalExpr.replace(new RegExp(placeholder.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g'), val);
                    });
                    
                    const wordMatches = evalExpr.match(/[a-zA-Z_][a-zA-Z0-9_]*/g) || [];
                    wordMatches.forEach(varName => {
                        if (varName === 'Math' || varName === 'true' || varName === 'false') return;
                        const val = getFieldValue(varName);
                        evalExpr = evalExpr.replace(new RegExp('\\b' + varName + '\\b', 'g'), val);
                    });

                    try {
                        const result = Function('"use strict"; return (' + evalExpr + ')')();
                        const numVal = isNaN(result) ? 0 : result;
                        const currency = el.dataset.currency || 'NONE';
                        const decimals = parseInt(el.dataset.decimals) !== undefined ? parseInt(el.dataset.decimals) : 2;
                        
                        let formatted = numVal.toFixed(decimals);
                        if (currency === 'INR') {
                            formatted = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(numVal);
                        } else if (currency === 'USD') {
                            formatted = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(numVal);
                        } else if (currency === 'CHF') {
                            formatted = new Intl.NumberFormat('de-CH', { style: 'currency', currency: 'CHF', minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(numVal);
                        }
                        
                        el.textContent = formatted;
                        const fieldName = el.dataset.fieldName;
                        submittedAnswers[fieldName] = numVal;
                    } catch (e) {
                        el.textContent = '0';
                    }
                });
            }

            // Check for checkout success/cancel or mock redirects in url parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('payment_success')) {
                formEl.classList.add('hidden');
                successContainer.classList.remove('hidden');
                successContainer.classList.add('flex');
                document.getElementById('success-msg-text').textContent = "Payment successful! Thank you for completing your checkout transaction.";
            } else if (urlParams.has('payment_cancel')) {
                const cancelBanner = document.createElement('div');
                cancelBanner.className = 'w-full p-4 mb-4 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl';
                cancelBanner.textContent = "Payment transaction was cancelled. You can try submitting again.";
                formEl.prepend(cancelBanner);
            } else if (urlParams.has('mock_checkout')) {
                const amt = urlParams.get('amount') || '0';
                const cur = urlParams.get('currency') || 'INR';
                const fid = urlParams.get('form_id') || '0';
                
                document.body.innerHTML = `
                    <div class="min-h-screen flex items-center justify-center p-4 bg-[#F9F6F0]" style="font-family: -apple-system, BlinkMacSystemFont, sans-serif;">
                        <div class="w-full max-w-md bg-white border border-zinc-200 rounded-[24px] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.015)] flex flex-col gap-6 text-zinc-900">
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                                <div class="flex items-center gap-2">
                                    <div class="text-zinc-650 shrink-0">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                    </div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">Secure Stripe Simulation</span>
                                </div>
                                <span class="text-xs text-zinc-400">Sandbox Mode</span>
                            </div>
                            
                            <div class="flex flex-col gap-1.5">
                                <span class="text-[10px] uppercase font-bold text-zinc-400">Payment Amount</span>
                                <span class="text-3xl font-bold">${cur === 'USD' ? '$' : '₹'}${amt}</span>
                            </div>

                            <div class="space-y-3">
                                <button onclick="window.location.href='/shared-form/\${fid}?payment_success=1'" class="w-full h-10 rounded-xl bg-zinc-950 text-white font-semibold text-xs hover:bg-zinc-900 transition-all cursor-pointer">
                                    Simulate Successful Payment (Authorize)
                                </button>
                                <button onclick="window.location.href='/shared-form/\${fid}?payment_cancel=1'" class="w-full h-10 rounded-xl border border-zinc-200 text-zinc-600 font-semibold text-xs hover:bg-zinc-50 transition-all cursor-pointer">
                                    Simulate Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }

            // Run Form Startup
            partitionBlocks();
            renderStep(0);
        });
    </script>
</body>
</html>
