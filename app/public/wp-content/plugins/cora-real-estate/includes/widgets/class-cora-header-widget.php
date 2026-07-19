<?php
/**
 * Cora Header Elementor Widget
 *
 * Sticky top navigation bar for the Cora landing page.
 * Monochromatic palette — white/zinc/black only, per platform rules.
 * Registered under the "cora-sections" category.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Cora_Header_Widget extends Widget_Base {

    public function get_name() {
        return 'cora-header';
    }

    public function get_title() {
        return esc_html__( 'Cora Header', 'cora-real-estate' );
    }

    public function get_icon() {
        return 'eicon-nav-menu';
    }

    public function get_categories() {
        return [ 'cora-sections' ];
    }

    public function get_keywords() {
        return [ 'header', 'nav', 'navigation', 'menu', 'cora', 'landing' ];
    }

    protected function register_controls() {

        // ── Brand ──────────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_brand',
            [
                'label' => esc_html__( 'Brand', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'logo_text',
            [
                'label'   => esc_html__( 'Logo Wordmark', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Cora',
            ]
        );

        $this->add_control(
            'logo_url',
            [
                'label'       => esc_html__( 'Logo Link', 'cora-real-estate' ),
                'type'        => Controls_Manager::URL,
                'placeholder' => home_url( '/' ),
                'default'     => [ 'url' => home_url( '/' ) ],
            ]
        );

        $this->end_controls_section();

        // ── Navigation Links ───────────────────────────────────────────────────
        $this->start_controls_section(
            'section_nav',
            [
                'label' => esc_html__( 'Navigation Links', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $nav_repeater = new Repeater();

        $nav_repeater->add_control(
            'nav_label',
            [
                'label'       => esc_html__( 'Label', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Features',
                'placeholder' => 'Nav item label',
            ]
        );

        $nav_repeater->add_control(
            'nav_url',
            [
                'label'       => esc_html__( 'URL', 'cora-real-estate' ),
                'type'        => Controls_Manager::URL,
                'placeholder' => '#',
                'default'     => [ 'url' => '#' ],
            ]
        );

        $this->add_control(
            'nav_items',
            [
                'label'       => esc_html__( 'Links', 'cora-real-estate' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $nav_repeater->get_controls(),
                'default'     => [],   // Start empty — add links as sections are built
                'title_field' => '{{{ nav_label }}}',
            ]
        );

        $this->end_controls_section();

        // ── CTA Button ─────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_cta',
            [
                'label' => esc_html__( 'CTA Button', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'cta_text',
            [
                'label'   => esc_html__( 'Button Label', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Get Started for Free',
            ]
        );

        $this->add_control(
            'cta_url',
            [
                'label'       => esc_html__( 'Button URL', 'cora-real-estate' ),
                'type'        => Controls_Manager::URL,
                'placeholder' => '#',
                'default'     => [ 'url' => '#' ],
            ]
        );

        $this->add_control(
            'show_login_link',
            [
                'label'        => esc_html__( 'Show Log In Link', 'cora-real-estate' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'cora-real-estate' ),
                'label_off'    => esc_html__( 'Hide', 'cora-real-estate' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'login_text',
            [
                'label'     => esc_html__( 'Log In Label', 'cora-real-estate' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => 'Log in',
                'condition' => [ 'show_login_link' => 'yes' ],
            ]
        );

        $this->add_control(
            'login_url',
            [
                'label'     => esc_html__( 'Log In URL', 'cora-real-estate' ),
                'type'      => Controls_Manager::URL,
                'default'   => [ 'url' => '#' ],
                'condition' => [ 'show_login_link' => 'yes' ],
            ]
        );

        $this->end_controls_section();

        // ── Style ──────────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_style_header',
            [
                'label' => esc_html__( 'Header Style', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'sticky_header',
            [
                'label'        => esc_html__( 'Sticky on Scroll', 'cora-real-estate' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'cora-real-estate' ),
                'label_off'    => esc_html__( 'No', 'cora-real-estate' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $logo_text  = $settings['logo_text'] ?? 'Cora';
        $logo_url   = $settings['logo_url']['url'] ?? home_url( '/' );
        $nav_items  = $settings['nav_items'] ?? [];
        $cta_text   = $settings['cta_text'] ?? 'Get Started for Free';
        $cta_url    = $settings['cta_url']['url'] ?? '#';
        $show_login = ( $settings['show_login_link'] ?? '' ) === 'yes';
        $login_text = $settings['login_text'] ?? 'Log in';
        $login_url  = $settings['login_url']['url'] ?? '#';
        $sticky     = ( $settings['sticky_header'] ?? 'yes' ) === 'yes';
        $sticky_cls = $sticky ? ' cora-header-sticky' : '';
        ?>
        <header class="cora-header<?php echo esc_attr( $sticky_cls ); ?>" id="cora-site-header">
            <div class="cora-header-inner">

                <!-- Logo -->
                <a href="<?php echo esc_url( $logo_url ); ?>" class="cora-header-logo" aria-label="<?php echo esc_attr( $logo_text ); ?>">
                    <span class="cora-header-logo-mark" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="20" height="20" rx="5" fill="#09090b"/>
                            <path d="M6 10.5C6 8.015 7.985 6 10.5 6H12V8H10.5C9.119 8 8 9.119 8 10.5C8 11.881 9.119 13 10.5 13H12V15H10.5C7.985 15 6 12.985 6 10.5Z" fill="#ffffff"/>
                        </svg>
                    </span>
                    <span class="cora-header-logo-text"><?php echo esc_html( $logo_text ); ?></span>
                </a>

                <!-- Desktop Nav -->
                <?php if ( ! empty( $nav_items ) ) : ?>
                <nav class="cora-header-nav" aria-label="Main navigation">
                    <?php foreach ( $nav_items as $item ) : ?>
                        <a href="<?php echo esc_url( $item['nav_url']['url'] ?? '#' ); ?>" class="cora-header-nav-link">
                            <?php echo esc_html( $item['nav_label'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>

                <!-- Right Actions -->
                <div class="cora-header-actions">
                    <?php if ( $show_login ) : ?>
                    <a href="<?php echo esc_url( $login_url ); ?>" class="cora-header-login">
                        <?php echo esc_html( $login_text ); ?>
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="cora-header-cta">
                        <?php echo esc_html( $cta_text ); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>

                    <!-- Mobile hamburger -->
                    <button class="cora-header-burger" aria-label="Open menu" aria-expanded="false" aria-controls="cora-mobile-nav">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </div>

            <!-- Mobile nav drawer -->
            <div class="cora-mobile-nav" id="cora-mobile-nav" aria-hidden="true">
                <?php if ( ! empty( $nav_items ) ) : ?>
                    <?php foreach ( $nav_items as $item ) : ?>
                        <a href="<?php echo esc_url( $item['nav_url']['url'] ?? '#' ); ?>" class="cora-mobile-nav-link">
                            <?php echo esc_html( $item['nav_label'] ); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="cora-mobile-nav-actions">
                    <?php if ( $show_login ) : ?>
                    <a href="<?php echo esc_url( $login_url ); ?>" class="cora-header-login"><?php echo esc_html( $login_text ); ?></a>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="cora-header-cta"><?php echo esc_html( $cta_text ); ?></a>
                </div>
            </div>
        </header>

        <!-- Onboarding SignUp Modal Overlay -->
        <?php
        $google_enabled  = get_option( 'cora_onboarding_google_enabled', 1 ) && ! empty( get_option( 'cora_google_client_id', '' ) );
        $email_enabled   = get_option( 'cora_onboarding_email_enabled', 1 );
        $google_auth_url = home_url( '/workspace/auth/google' );
        ?>
        <div class="cora-modal-overlay" id="cora-signup-modal" aria-hidden="true">
            <div class="cora-modal-card">
                <!-- Close Button -->
                <button type="button" class="cora-modal-close" id="cora-modal-close-btn" aria-label="Close modal">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>

                <!-- Signup Form State -->
                <div id="cora-modal-form-state">
                    <div class="cora-modal-header">
                        <div class="cora-modal-tagline">Start building.</div>
                        <h2 class="cora-modal-title">Create free account</h2>
                    </div>

                    <div class="cora-modal-sso-container">
                        <?php if ( $google_enabled ) : ?>
                        <a href="<?php echo esc_url( $google_auth_url ); ?>" class="cora-modal-sso-btn cora-modal-google-btn">
                            <span class="cora-google-pill">LAST USED</span>
                            <svg width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right:2px;">
                                <path d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" fill="#FFC107"/>
                                <path d="M6.306,14.691l6.571,4.819C14.655,15.108,19.003,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" fill="#FF3D00"/>
                                <path d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" fill="#4CAF50"/>
                                <path d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" fill="#1976D2"/>
                            </svg>
                            Continue with Google
                        </a>
                        <?php endif; ?>
                    </div>

                    <?php if ( $email_enabled ) : ?>
                    <div class="cora-modal-divider">or</div>

                    <form class="cora-modal-form" id="cora-modal-signup-form" autocomplete="off">
                        <input type="email" class="cora-modal-input" id="cora-modal-email" required placeholder="Enter your email address..." autocomplete="email">
                        <button type="submit" class="cora-modal-btn-submit" id="cora-modal-submit-btn">Continue</button>
                    </form>
                    <?php endif; ?>

                    <div class="cora-modal-footer-tos">
                        By continuing, you agree to the <a href="<?php echo esc_url( home_url( '/privacy' ) ); ?>" target="_blank">Terms of Service</a> and <a href="<?php echo esc_url( home_url( '/privacy' ) ); ?>" target="_blank">Privacy Policy</a>.
                    </div>

                    <div class="cora-modal-footer-separator"></div>

                    <div class="cora-modal-sso-note">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" style="margin-right:2px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        SSO available on <a href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>">Business and Enterprise</a> plans
                    </div>
                </div>

                <!-- Success / Inbox State -->
                <div id="cora-modal-success-state" class="cora-modal-success">
                    <div class="cora-modal-success-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <h3 class="cora-modal-success-title">Check your inbox</h3>
                    <p class="cora-modal-success-text">We sent a verification link to <strong id="cora-modal-success-email"></strong>. Click the link to activate your workspace and sign in automatically.</p>
                    <p style="font-size:11.5px; color:#71717a; margin-bottom:4px;">Didn't receive it?</p>
                    <button type="button" class="cora-modal-btn-resend" id="cora-modal-resend-btn">Resend verification email</button>
                </div>
            </div>
        </div>

        <script>
        (function() {
            var header  = document.getElementById('cora-site-header');
            var burger  = header ? header.querySelector('.cora-header-burger') : null;
            var mobileNav = document.getElementById('cora-mobile-nav');

            // Sticky scroll shadow
            <?php if ( $sticky ) : ?>
            window.addEventListener('scroll', function() {
                if (!header) return;
                if (window.scrollY > 12) {
                    header.classList.add('cora-header-scrolled');
                } else {
                    header.classList.remove('cora-header-scrolled');
                }
            }, { passive: true });
            <?php endif; ?>

            // Mobile toggle
            if (burger && mobileNav) {
                burger.addEventListener('click', function() {
                    var open = mobileNav.classList.toggle('open');
                    burger.classList.toggle('open', open);
                    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
                    mobileNav.setAttribute('aria-hidden', open ? 'false' : 'true');
                });

                // Close on link click
                mobileNav.querySelectorAll('a').forEach(function(a) {
                    a.addEventListener('click', function() {
                        mobileNav.classList.remove('open');
                        burger.classList.remove('open');
                        burger.setAttribute('aria-expanded', 'false');
                        mobileNav.setAttribute('aria-hidden', 'true');
                    });
                });
            }

            // --- Modal Open/Close Logic ---
            var modal = document.getElementById('cora-signup-modal');
            var closeBtn = document.getElementById('cora-modal-close-btn');
            var registeredEmail = '';

            function openModal(e) {
                var isEnabled = <?php echo get_option( 'cora_onboarding_enabled', 1 ) ? 'true' : 'false'; ?>;
                if (!isEnabled) {
                    // If onboarding is disabled, let the default href link action happen (redirects to home/login)
                    return;
                }
                if (modal) {
                    if (e) e.preventDefault();
                    modal.classList.add('open');
                    modal.setAttribute('aria-hidden', 'false');
                }
            }

            function closeModal() {
                if (modal) {
                    modal.classList.remove('open');
                    modal.setAttribute('aria-hidden', 'true');
                    // Reset modal state
                    document.getElementById('cora-modal-form-state').style.display = 'block';
                    document.getElementById('cora-modal-success-state').style.display = 'none';
                    var emailInput = document.getElementById('cora-modal-email');
                    if (emailInput) emailInput.value = '';
                }
            }

            // Bind to CTA buttons in Header & Landing Hero sections
            document.addEventListener('click', function(e) {
                var target = e.target;
                // Match class or tag
                if (target.classList.contains('cora-header-cta') || 
                    target.classList.contains('cora-hero-cta-primary') || 
                    target.closest('.cora-header-cta') || 
                    target.closest('.cora-hero-cta-primary') ||
                    (target.tagName === 'A' && target.getAttribute('href') === '#cora-signup')) {
                    
                    var href = target.getAttribute('href') || (target.closest('a') ? target.closest('a').getAttribute('href') : '');
                    if (href === '#' || href === '#cora-signup' || href === '') {
                        openModal(e);
                    }
                }
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }

            // Close on overlay click
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) closeModal();
                });
            }

            // Handle Modal Form Submit
            var form = document.getElementById('cora-modal-signup-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var email = document.getElementById('cora-modal-email').value.trim();
                    if (!email) return;

                    var btn = document.getElementById('cora-modal-submit-btn');
                    btn.disabled = true;
                    btn.textContent = 'Sending link...';

                    var formData = new FormData();
                    formData.append('action', 'cora_modal_register');
                    formData.append('email', email);
                    formData.append('nonce', '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>');

                    fetch('<?php echo admin_url( "admin-ajax.php" ); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success) {
                            registeredEmail = email;
                            document.getElementById('cora-modal-form-state').style.display = 'none';
                            document.getElementById('cora-modal-success-state').style.display = 'block';
                            document.getElementById('cora-modal-success-email').textContent = email;
                        } else {
                            // If user helper is loaded, show toast, otherwise alert safely
                            if (window.coraShowToast) {
                                window.coraShowToast(res.data.message || 'Error occurred.');
                            } else {
                                alert(res.data.message || 'Error occurred.');
                            }
                            btn.disabled = false;
                            btn.textContent = 'Continue';
                        }
                    })
                    .catch(function() {
                        if (window.coraShowToast) {
                            window.coraShowToast('Network error. Please try again.');
                        } else {
                            alert('Network error. Please try again.');
                        }
                        btn.disabled = false;
                        btn.textContent = 'Continue';
                    });
                });
            }

            // Handle Resend
            var resendBtn = document.getElementById('cora-modal-resend-btn');
            if (resendBtn) {
                resendBtn.addEventListener('click', function() {
                    if (!registeredEmail) return;
                    if (window.coraShowToast) window.coraShowToast('Sending verification link...');
                    
                    var formData = new FormData();
                    formData.append('action', 'cora_ajax_resend_verification');
                    formData.append('email', registeredEmail);
                    formData.append('nonce', '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>');
                    
                    fetch('<?php echo admin_url( "admin-ajax.php" ); ?>', { method: 'POST', body: formData })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (window.coraShowToast) {
                            window.coraShowToast(res.data.message || 'Link sent!');
                        }
                    });
                });
            }
        })();
        </script>
        <?php
    }

    protected function content_template() {
        ?>
        <# var logoText = settings.logo_text || 'Cora';
           var ctaText  = settings.cta_text  || 'Get Started for Free';
           var loginText = settings.login_text || 'Log in';
           var showLogin = settings.show_login_link === 'yes';
        #>
        <header class="cora-header cora-header-sticky">
            <div class="cora-header-inner">
                <a href="#" class="cora-header-logo">
                    <span class="cora-header-logo-mark">
                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="20" height="20" rx="5" fill="#09090b"/>
                            <path d="M6 10.5C6 8.015 7.985 6 10.5 6H12V8H10.5C9.119 8 8 9.119 8 10.5C8 11.881 9.119 13 10.5 13H12V15H10.5C7.985 15 6 12.985 6 10.5Z" fill="#ffffff"/>
                        </svg>
                    </span>
                    <span class="cora-header-logo-text">{{{ logoText }}}</span>
                </a>
                <nav class="cora-header-nav">
                    <# _.each( settings.nav_items, function( item ) { #>
                        <a href="{{ item.nav_url.url }}" class="cora-header-nav-link">{{{ item.nav_label }}}</a>
                    <# }); #>
                </nav>
                <div class="cora-header-actions">
                    <# if ( showLogin ) { #>
                    <a href="#" class="cora-header-login">{{{ loginText }}}</a>
                    <# } #>
                    <a href="#" class="cora-header-cta">
                        {{{ ctaText }}}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                    <button class="cora-header-burger"><span></span><span></span><span></span></button>
                </div>
            </div>
        </header>
        <?php
    }
}
