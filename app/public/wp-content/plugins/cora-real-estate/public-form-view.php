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
            background-color: #faf9f6;
            color: #1c1917;
        }
        .form-card {
            background-color: #ffffff;
            border: 1px solid #e7e5e4;
        }
        <?php echo isset( $styling['custom_css'] ) ? esc_html( $styling['custom_css'] ) : ''; ?>
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl form-card rounded-2xl p-8 md:p-12 shadow-sm relative">
        <!-- Progress bar indicator -->
        <div class="absolute top-0 left-0 w-full h-1 bg-zinc-100 rounded-t-2xl overflow-hidden">
            <div id="form-progress-indicator" class="h-full bg-zinc-900 transition-all duration-300" style="width: 0%"></div>
        </div>

        <form id="public-cora-form" class="space-y-6" data-form-id="<?php echo esc_attr( $form['id'] ); ?>">
            <!-- Honeypot protection decoy field -->
            <div style="position: absolute; left: -9999px; top: -9999px; height: 0; width: 0; overflow: hidden;" aria-hidden="true">
                <input type="text" name="cora_hp_verify" id="cora-hp-verify" tabindex="-1" value="" autocomplete="off" />
            </div>

            <div id="form-steps-container">
                <!-- Inputs injected dynamically here -->
            </div>

            <!-- Navigation Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-stone-100">
                <button type="button" id="btn-prev-step" class="hidden px-4 h-9 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-xs font-semibold transition-all">
                    Previous
                </button>
                <div class="flex-1"></div>
                <button type="button" id="btn-next-step" class="px-5 h-9 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold transition-all">
                    Next
                </button>
            </div>
        </form>

        <!-- Success view -->
        <div id="form-success-container" class="hidden flex-col items-center justify-center text-center py-12 space-y-4">
            <div class="h-12 w-12 rounded-full bg-stone-100 flex items-center justify-center text-zinc-900 text-xl font-bold">✓</div>
            <h2 class="text-lg font-bold text-zinc-900">Response Submitted</h2>
            <p class="text-xs text-zinc-500 max-w-sm" id="success-msg-text">
                <?php echo esc_html( isset($settings['success_message']) ? $settings['success_message'] : 'Thank you for your response!' ); ?>
            </p>
        </div>
    </div>

    <!-- Inject data block -->
    <script>
        const formBlocks = <?php echo json_encode( $blocks ); ?>;
        const formLogic = <?php echo json_encode( $logic ); ?>;
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

            // Split blocks list into visual steps / pages
            function partitionBlocks() {
                let currentStep = [];
                formBlocks.forEach(block => {
                    if (block.type === 'page_break') {
                        if (currentStep.length > 0) {
                            steps.push(currentStep);
                            currentStep = [];
                        }
                    } else {
                        currentStep.push(block);
                    }
                });
                if (currentStep.length > 0) {
                    steps.push(currentStep);
                }
                if (steps.length === 0) {
                    steps.push([]);
                }
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
                            <label class="text-xs font-semibold text-zinc-800">${cleanLabel}</label>
                            <div class="border border-zinc-200 rounded-xl p-4 bg-zinc-50/50 flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-zinc-500 font-medium">Amount to Pay:</span>
                                    <span class="text-sm font-bold text-zinc-900">${block.currency === 'USD' ? '$' : '₹'}${block.price || 0}</span>
                                </div>
                                <div class="text-[10px] text-zinc-400">Checkout is powered securely by Stripe. Clicking submit will redirect you to secure payment portal.</div>
                            </div>
                        `;
                    } else if (block.type === 'formula') {
                        const cleanLabel = block.label || 'Calculated Value';
                        const fieldName = cleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                        blockDiv.id = 'field-wrapper-' + fieldName;
                        blockDiv.innerHTML = `
                            <div class="border border-zinc-200 rounded-xl p-4 bg-zinc-50/50 flex items-center justify-between">
                                <span class="text-xs font-semibold text-zinc-800">${cleanLabel}</span>
                                <span class="cora-calculated-value text-sm font-bold text-zinc-950" 
                                      data-expression="${block.expression || ''}" 
                                      data-currency="${block.currency || 'NONE'}" 
                                      data-decimals="${block.decimals !== undefined ? block.decimals : 2}"
                                      data-field-name="${fieldName}"
                                      data-label="${cleanLabel}">0</span>
                            </div>
                        `;
                    } else {
                        // Render standard inputs
                        const cleanLabel = block.label || 'Input Field';
                        const fieldName = cleanLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                        blockDiv.id = 'field-wrapper-' + fieldName;
                        let inputHtml = '';

                        if (block.type === 'long_text') {
                            inputHtml = `<textarea name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" rows="3" placeholder="Type answer..." class="w-full p-3 rounded-lg border border-zinc-200 text-xs focus:ring-1 focus:ring-zinc-400 outline-none transition-all"></textarea>`;
                        } else if (block.type === 'dropdown') {
                            let optsHtml = '<option value="">Choose option...</option>';
                            const bChoices = block.choices || [];
                            bChoices.forEach(cOpt => {
                                let label = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                let val = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                optsHtml += `<option value="${val}">${label}</option>`;
                            });
                            inputHtml = `
                                <select name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" class="w-full h-10 px-3 rounded-lg border border-zinc-200 text-xs focus:ring-1 focus:ring-zinc-400 outline-none bg-white transition-all">
                                    ${optsHtml}
                                </select>
                            `;
                        } else if (block.type === 'checkbox') {
                            let checkboxesHtml = '';
                            const bChoices = block.choices || [];
                            bChoices.forEach((cOpt, cIdx) => {
                                let label = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                let val = typeof cOpt === 'object' ? cOpt.label : cOpt;
                                checkboxesHtml += `
                                    <div class="flex items-center gap-2 mt-1">
                                        <input type="checkbox" name="${fieldName}[]" data-label="${cleanLabel}" data-field-name="${fieldName}" data-option-index="${cIdx}" value="${val}" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-400" />
                                        <span class="text-xs text-stone-600">${label}</span>
                                    </div>
                                `;
                            });
                            inputHtml = `<div class="flex flex-col gap-1.5">${checkboxesHtml}</div>`;
                        } else {
                            // Text, number, email, phone
                            const inpType = block.type === 'number' ? 'number' : (block.type === 'email' ? 'email' : 'text');
                            inputHtml = `<input type="${inpType}" name="${fieldName}" data-label="${cleanLabel}" data-field-name="${fieldName}" placeholder="Type answer..." class="w-full h-10 px-3 rounded-lg border border-zinc-200 text-xs focus:ring-1 focus:ring-zinc-400 outline-none transition-all" />`;
                        }

                        blockDiv.innerHTML = `
                            <label class="text-xs font-semibold text-zinc-800">${cleanLabel}</label>
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
                        } else {
                            inp.value = submittedAnswers[label];
                        }
                    }

                    // Attach change listeners for real-time saving and drop-off analytics
                    ['input', 'change'].forEach(evtType => {
                        inp.addEventListener(evtType, function() {
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

                // Update progress percentage
                const progressPct = steps.length > 1 ? (idx / (steps.length - 1)) * 100 : 100;
                document.getElementById('form-progress-indicator').style.width = `${progressPct}%`;

                // Configure Nav Buttons
                if (idx === 0) {
                    btnPrev.classList.add('hidden');
                } else {
                    btnPrev.classList.remove('hidden');
                }

                if (idx === steps.length - 1) {
                    btnNext.textContent = 'Submit';
                } else {
                    btnNext.textContent = 'Next';
                }
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
                    if (inp.type === 'checkbox') {
                        submittedAnswers[label] = inp.checked ? 'true' : 'false';
                    } else {
                        submittedAnswers[label] = inp.value;
                    }
                });

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
                            btnNext.textContent = 'Submit';
                        }
                    })
                    .catch(err => {
                        console.error("Payment error:", err);
                        showErrorBanner("Checkout connection failed. Please check your network and try again.");
                        btnNext.disabled = false;
                        btnNext.textContent = 'Submit';
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
                        alert("Submission failed. Please try again.");
                        btnNext.disabled = false;
                        btnNext.textContent = 'Submit';
                    }
                })
                .catch(err => {
                    console.error("Submit error:", err);
                    alert("Submission error.");
                    btnNext.disabled = false;
                    btnNext.textContent = 'Submit';
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
                    <div class="min-h-screen flex items-center justify-center p-4 bg-[#faf9f6]" style="font-family: -apple-system, BlinkMacSystemFont, sans-serif;">
                        <div class="w-full max-w-md bg-white border border-stone-200 rounded-2xl p-8 shadow-sm flex flex-col gap-6 text-stone-900">
                            <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">💳</span>
                                    <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Secure Stripe Simulation</span>
                                </div>
                                <span class="text-xs text-stone-400">Sandbox Mode</span>
                            </div>
                            
                            <div class="flex flex-col gap-1.5">
                                <span class="text-[10px] uppercase font-bold text-stone-400">Payment Amount</span>
                                <span class="text-3xl font-bold">${cur === 'USD' ? '$' : '₹'}${amt}</span>
                            </div>

                            <div class="space-y-3">
                                <button onclick="window.location.href='/shared-form/\${fid}?payment_success=1'" class="w-full h-10 rounded-lg bg-stone-950 text-white font-semibold text-xs hover:bg-stone-800 transition-all">
                                    Simulate Successful Payment (Authorize)
                                </button>
                                <button onclick="window.location.href='/shared-form/\${fid}?payment_cancel=1'" class="w-full h-10 rounded-lg border border-stone-200 text-stone-600 font-semibold text-xs hover:bg-stone-50 transition-all">
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
