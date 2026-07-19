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
