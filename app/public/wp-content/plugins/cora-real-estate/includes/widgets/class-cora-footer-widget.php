<?php
/**
 * Cora Footer Elementor Widget
 *
 * Full-width site footer for the Cora landing page.
 * Monochromatic palette — white/zinc/black only, per platform rules.
 * Registered under the "cora-sections" category.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Cora_Footer_Widget extends Widget_Base {

    public function get_name() {
        return 'cora-footer';
    }

    public function get_title() {
        return esc_html__( 'Cora Footer', 'cora-real-estate' );
    }

    public function get_icon() {
        return 'eicon-footer';
    }

    public function get_categories() {
        return [ 'cora-sections' ];
    }

    public function get_keywords() {
        return [ 'footer', 'cora', 'landing', 'links', 'bottom' ];
    }

    protected function register_controls() {

        // ── Brand ──────────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_footer_brand',
            [
                'label' => esc_html__( 'Brand & Tagline', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'footer_logo_text',
            [
                'label'   => esc_html__( 'Logo Wordmark', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Cora',
            ]
        );

        $this->add_control(
            'footer_tagline',
            [
                'label'   => esc_html__( 'Tagline', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'The complete real estate command center for modern Indian agencies.',
                'rows'    => 3,
            ]
        );

        $this->add_control(
            'footer_copyright',
            [
                'label'   => esc_html__( 'Copyright Text', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '© 2025 Cora. All rights reserved.',
            ]
        );

        $this->end_controls_section();

        // ── Link Columns ───────────────────────────────────────────────────────
        $column_defaults = [
            [
                'col_heading' => 'Company',
                'links'       => [
                    [ 'link_label' => 'About',    'link_url' => '#about'   ],
                    [ 'link_label' => 'Contact',  'link_url' => '#contact' ],
                ],
            ],
            [
                'col_heading' => 'Legal',
                'links'       => [
                    [ 'link_label' => 'Privacy Policy', 'link_url' => '#privacy' ],
                    [ 'link_label' => 'Terms of Use',   'link_url' => '#terms'   ],
                ],
            ],
            [
                'col_heading' => '',
                'links'       => [],
            ],
            [
                'col_heading' => '',
                'links'       => [],
            ],
        ];

        for ( $i = 1; $i <= 4; $i++ ) {
            $defaults = $column_defaults[ $i - 1 ];

            $this->start_controls_section(
                "section_footer_col_{$i}",
                [
                    'label' => esc_html__( "Link Column {$i}", 'cora-real-estate' ),
                    'tab'   => Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                "col_{$i}_heading",
                [
                    'label'   => esc_html__( 'Column Heading', 'cora-real-estate' ),
                    'type'    => Controls_Manager::TEXT,
                    'default' => $defaults['col_heading'],
                ]
            );

            $link_repeater = new Repeater();
            $link_repeater->add_control(
                'link_label',
                [
                    'label'       => esc_html__( 'Label', 'cora-real-estate' ),
                    'type'        => Controls_Manager::TEXT,
                    'default'     => 'Link',
                    'placeholder' => 'Link label',
                ]
            );
            $link_repeater->add_control(
                'link_url',
                [
                    'label'   => esc_html__( 'URL', 'cora-real-estate' ),
                    'type'    => Controls_Manager::TEXT,
                    'default' => '#',
                ]
            );

            $default_links = array_map( function( $l ) {
                return [ 'link_label' => $l['link_label'], 'link_url' => $l['link_url'] ];
            }, $defaults['links'] );

            $this->add_control(
                "col_{$i}_links",
                [
                    'label'       => esc_html__( 'Links', 'cora-real-estate' ),
                    'type'        => Controls_Manager::REPEATER,
                    'fields'      => $link_repeater->get_controls(),
                    'default'     => $default_links,
                    'title_field' => '{{{ link_label }}}',
                ]
            );

            $this->end_controls_section();
        }

        // ── Social Links ────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_footer_social',
            [
                'label' => esc_html__( 'Social Links', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $socials = [
            'twitter'  => 'X / Twitter',
            'linkedin' => 'LinkedIn',
            'instagram'=> 'Instagram',
        ];

        foreach ( $socials as $key => $label ) {
            $this->add_control(
                "social_{$key}",
                [
                    'label'       => esc_html__( $label, 'cora-real-estate' ),
                    'type'        => Controls_Manager::URL,
                    'placeholder' => 'https://',
                    'default'     => [ 'url' => '' ],
                ]
            );
        }

        $this->end_controls_section();

        // ── CTA Banner (optional) ───────────────────────────────────────────────
        $this->start_controls_section(
            'section_footer_cta_banner',
            [
                'label' => esc_html__( 'Pre-footer CTA Banner', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_cta_banner',
            [
                'label'        => esc_html__( 'Show CTA Banner', 'cora-real-estate' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'cora-real-estate' ),
                'label_off'    => esc_html__( 'Hide', 'cora-real-estate' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'cta_banner_heading',
            [
                'label'     => esc_html__( 'Banner Heading', 'cora-real-estate' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => 'Ready to transform your real estate business?',
                'condition' => [ 'show_cta_banner' => 'yes' ],
            ]
        );

        $this->add_control(
            'cta_banner_sub',
            [
                'label'     => esc_html__( 'Banner Subtext', 'cora-real-estate' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => 'Join 500+ agencies already on Cora. No credit card required.',
                'condition' => [ 'show_cta_banner' => 'yes' ],
            ]
        );

        $this->add_control(
            'cta_banner_btn_text',
            [
                'label'     => esc_html__( 'Button Label', 'cora-real-estate' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => 'Start Free Trial',
                'condition' => [ 'show_cta_banner' => 'yes' ],
            ]
        );

        $this->add_control(
            'cta_banner_btn_url',
            [
                'label'     => esc_html__( 'Button URL', 'cora-real-estate' ),
                'type'      => Controls_Manager::URL,
                'default'   => [ 'url' => '#' ],
                'condition' => [ 'show_cta_banner' => 'yes' ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $logo_text = $settings['footer_logo_text'] ?? 'Cora';
        $tagline   = $settings['footer_tagline']   ?? '';
        $copyright = $settings['footer_copyright'] ?? '© 2025 Cora. All rights reserved.';

        $show_banner    = ( $settings['show_cta_banner'] ?? 'yes' ) === 'yes';
        $banner_heading = $settings['cta_banner_heading'] ?? '';
        $banner_sub     = $settings['cta_banner_sub'] ?? '';
        $banner_btn     = $settings['cta_banner_btn_text'] ?? 'Start Free Trial';
        $banner_btn_url = $settings['cta_banner_btn_url']['url'] ?? '#';

        $social_twitter  = $settings['social_twitter']['url']   ?? '';
        $social_linkedin = $settings['social_linkedin']['url']  ?? '';
        $social_instagram= $settings['social_instagram']['url'] ?? '';

        $columns = [];
        for ( $i = 1; $i <= 4; $i++ ) {
            $columns[] = [
                'heading' => $settings["col_{$i}_heading"] ?? '',
                'links'   => $settings["col_{$i}_links"]   ?? [],
            ];
        }
        ?>

        <?php if ( $show_banner && $banner_heading ) : ?>
        <!-- Pre-footer CTA Banner -->
        <section class="cora-footer-cta-banner">
            <div class="cora-footer-cta-inner">
                <div class="cora-footer-cta-text">
                    <h2 class="cora-footer-cta-heading"><?php echo esc_html( $banner_heading ); ?></h2>
                    <?php if ( $banner_sub ) : ?>
                    <p class="cora-footer-cta-sub"><?php echo esc_html( $banner_sub ); ?></p>
                    <?php endif; ?>
                </div>
                <a href="<?php echo esc_url( $banner_btn_url ); ?>" class="cora-hero-cta-primary">
                    <?php echo esc_html( $banner_btn ); ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
        </section>
        <?php endif; ?>

        <!-- Main Footer -->
        <footer class="cora-footer">
            <div class="cora-footer-inner">

                <!-- Brand column -->
                <div class="cora-footer-brand">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cora-header-logo cora-footer-logo-link" aria-label="<?php echo esc_attr( $logo_text ); ?>">
                        <span class="cora-header-logo-mark">
                            <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="20" height="20" rx="5" fill="#09090b"/>
                                <path d="M6 10.5C6 8.015 7.985 6 10.5 6H12V8H10.5C9.119 8 8 9.119 8 10.5C8 11.881 9.119 13 10.5 13H12V15H10.5C7.985 15 6 12.985 6 10.5Z" fill="#ffffff"/>
                            </svg>
                        </span>
                        <span class="cora-header-logo-text"><?php echo esc_html( $logo_text ); ?></span>
                    </a>
                    <?php if ( $tagline ) : ?>
                    <p class="cora-footer-tagline"><?php echo esc_html( $tagline ); ?></p>
                    <?php endif; ?>

                    <!-- Social icons -->
                    <div class="cora-footer-social">
                        <?php if ( $social_twitter ) : ?>
                        <a href="<?php echo esc_url( $social_twitter ); ?>" class="cora-footer-social-icon" aria-label="X / Twitter" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if ( $social_linkedin ) : ?>
                        <a href="<?php echo esc_url( $social_linkedin ); ?>" class="cora-footer-social-icon" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if ( $social_instagram ) : ?>
                        <a href="<?php echo esc_url( $social_instagram ); ?>" class="cora-footer-social-icon" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Link columns -->
                <div class="cora-footer-cols">
                    <?php foreach ( $columns as $col ) : ?>
                    <div class="cora-footer-col">
                        <?php if ( $col['heading'] ) : ?>
                        <h3 class="cora-footer-col-heading"><?php echo esc_html( $col['heading'] ); ?></h3>
                        <?php endif; ?>
                        <?php foreach ( $col['links'] as $link ) : ?>
                        <a href="<?php echo esc_url( $link['link_url'] ?? '#' ); ?>" class="cora-footer-link">
                            <?php echo esc_html( $link['link_label'] ?? '' ); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Bottom bar -->
            <div class="cora-footer-bottom">
                <div class="cora-footer-bottom-inner">
                    <span class="cora-footer-copy"><?php echo esc_html( $copyright ); ?></span>
                    <div class="cora-footer-bottom-links">
                        <?php
                        $bottom_links = [
                            [ 'label' => 'Privacy',  'url' => '#privacy' ],
                            [ 'label' => 'Terms',    'url' => '#terms'   ],
                            [ 'label' => 'Cookies',  'url' => '#cookies' ],
                        ];
                        foreach ( $bottom_links as $bl ) : ?>
                        <a href="<?php echo esc_url( $bl['url'] ); ?>" class="cora-footer-bottom-link">
                            <?php echo esc_html( $bl['label'] ); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </footer>
        <?php
    }

    protected function content_template() {
        ?>
        <#
        var logoText    = settings.footer_logo_text || 'Cora';
        var tagline     = settings.footer_tagline || '';
        var copyright   = settings.footer_copyright || '© 2025 Cora. All rights reserved.';
        var showBanner  = settings.show_cta_banner === 'yes';
        var bannerHead  = settings.cta_banner_heading || '';
        var bannerSub   = settings.cta_banner_sub || '';
        var bannerBtn   = settings.cta_banner_btn_text || 'Start Free Trial';
        var cols = [
            { heading: settings.col_1_heading, links: settings.col_1_links },
            { heading: settings.col_2_heading, links: settings.col_2_links },
            { heading: settings.col_3_heading, links: settings.col_3_links },
            { heading: settings.col_4_heading, links: settings.col_4_links },
        ];
        #>
        <# if ( showBanner && bannerHead ) { #>
        <section class="cora-footer-cta-banner">
            <div class="cora-footer-cta-inner">
                <div class="cora-footer-cta-text">
                    <h2 class="cora-footer-cta-heading">{{{ bannerHead }}}</h2>
                    <# if ( bannerSub ) { #><p class="cora-footer-cta-sub">{{{ bannerSub }}}</p><# } #>
                </div>
                <a href="#" class="cora-hero-cta-primary">{{{ bannerBtn }}}<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
            </div>
        </section>
        <# } #>
        <footer class="cora-footer">
            <div class="cora-footer-inner">
                <div class="cora-footer-brand">
                    <a href="#" class="cora-header-logo cora-footer-logo-link">
                        <span class="cora-header-logo-mark"><svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="20" height="20" rx="5" fill="#09090b"/><path d="M6 10.5C6 8.015 7.985 6 10.5 6H12V8H10.5C9.119 8 8 9.119 8 10.5C8 11.881 9.119 13 10.5 13H12V15H10.5C7.985 15 6 12.985 6 10.5Z" fill="#ffffff"/></svg></span>
                        <span class="cora-header-logo-text">{{{ logoText }}}</span>
                    </a>
                    <# if ( tagline ) { #><p class="cora-footer-tagline">{{{ tagline }}}</p><# } #>
                </div>
                <div class="cora-footer-cols">
                    <# _.each( cols, function( col ) { #>
                    <div class="cora-footer-col">
                        <# if ( col.heading ) { #><h3 class="cora-footer-col-heading">{{{ col.heading }}}</h3><# } #>
                        <# _.each( col.links, function( link ) { #>
                        <a href="{{ link.link_url }}" class="cora-footer-link">{{{ link.link_label }}}</a>
                        <# }); #>
                    </div>
                    <# }); #>
                </div>
            </div>
            <div class="cora-footer-bottom">
                <div class="cora-footer-bottom-inner">
                    <span class="cora-footer-copy">{{{ copyright }}}</span>
                    <div class="cora-footer-bottom-links">
                        <a href="#" class="cora-footer-bottom-link">Privacy</a>
                        <a href="#" class="cora-footer-bottom-link">Terms</a>
                        <a href="#" class="cora-footer-bottom-link">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
        <?php
    }
}
