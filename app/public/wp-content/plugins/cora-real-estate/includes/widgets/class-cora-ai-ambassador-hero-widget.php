<?php
/**
 * Cora AI Ambassador Hero Elementor Widget
 *
 * A premium, modern hero section highlighting AI Brand Ambassador solutions.
 * Features a split layout, custom badges, floating metrics cards, and a sleek design.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class Cora_AI_Ambassador_Hero_Widget extends Widget_Base {

    public function get_name() {
        return 'cora-ai-ambassador-hero';
    }

    public function get_title() {
        return esc_html__( 'Cora AI Ambassador Hero', 'cora-real-estate' );
    }

    public function get_icon() {
        return 'eicon-image-hotspot';
    }

    public function get_categories() {
        return [ 'cora-sections' ];
    }

    public function get_keywords() {
        return [ 'hero', 'ai', 'ambassador', 'avatar', 'creator', 'brand' ];
    }

    protected function register_controls() {

        // ── Content Tab ────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'General Content', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'tag_text',
            [
                'label'       => esc_html__( 'Top Tag Text', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Done-For-You AI Brand Ambassador',
            ]
        );

        $this->add_control(
            'headline_main',
            [
                'label'       => esc_html__( 'Headline (Sans-serif)', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => "Own your brand's AI",
            ]
        );

        $this->add_control(
            'headline_serif',
            [
                'label'       => esc_html__( 'Headline Highlight (Serif)', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => "Ambassador.",
            ]
        );

        $this->add_control(
            'description',
            [
                'label'       => esc_html__( 'Description', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => 'Scale your revenue and brand equity with a dedicated virtual creator. Trained exclusively on your brand guidelines, active 24/7, with 100% IP ownership.',
                'rows'        => 4,
            ]
        );

        $this->end_controls_section();

        // ── Feature Checklist ───────────────────────────────────────────────────
        $this->start_controls_section(
            'section_features',
            [
                'label' => esc_html__( 'Feature Pills', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'feature_label',
            [
                'label'       => esc_html__( 'Feature Label', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Feature',
            ]
        );

        $this->add_control(
            'features_list',
            [
                'label'       => esc_html__( 'Features', 'cora-real-estate' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [ 'feature_label' => '100% IP Ownership' ],
                    [ 'feature_label' => 'Unlimited Content' ],
                    [ 'feature_label' => '24/7 Brand Presence' ],
                    [ 'feature_label' => 'Multi-Language Scaling' ],
                    [ 'feature_label' => 'Personalized Demos' ],
                    [ 'feature_label' => 'Zero Production Overhead' ],
                ],
                'title_field' => '{{{ feature_label }}}',
            ]
        );

        $this->end_controls_section();

        // ── Call To Action ──────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_cta',
            [
                'label' => esc_html__( 'CTA & Trust badges', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'cta_label',
            [
                'label'   => esc_html__( 'CTA Button Label', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Build Your AI Ambassador',
            ]
        );

        $this->add_control(
            'cta_url',
            [
                'label'         => esc_html__( 'CTA Link', 'cora-real-estate' ),
                'type'          => Controls_Manager::URL,
                'placeholder'   => 'https://',
                'default'       => [
                    'url'         => '#',
                    'is_external' => false,
                ],
            ]
        );

        $this->add_control(
            'cta_subtext',
            [
                'label'   => esc_html__( 'CTA Subtext', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '*No upfront payment. Custom draft concept delivered in 24 hours.',
            ]
        );

        $this->add_control(
            'shopify_text',
            [
                'label'   => esc_html__( 'Shopify Badge Text', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'SHOPIFY APP STORE',
            ]
        );

        $this->add_control(
            'rating_text',
            [
                'label'   => esc_html__( 'Rating Badge Text', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '5.0 Rating',
            ]
        );

        $this->end_controls_section();

        // ── Avatar Image & Metric Cards ──────────────────────────────────────────
        $this->start_controls_section(
            'section_image',
            [
                'label' => esc_html__( 'Ambassador Image & Cards', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'avatar_image',
            [
                'label'   => esc_html__( 'Choose Avatar Image', 'cora-real-estate' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => plugins_url( 'assets/images/ai-ambassador-hero.jpg', dirname( dirname( __FILE__ ) ) ),
                ],
            ]
        );

        // Metric Card 1
        $this->add_control(
            'card1_title',
            [
                'label'   => esc_html__( 'Card 1 Title (Top)', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'AVG. PERFORMANCE',
            ]
        );
        $this->add_control(
            'card1_value',
            [
                'label'   => esc_html__( 'Card 1 Value', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '3.4x ROAS Boost',
            ]
        );

        // Metric Card 2
        $this->add_control(
            'card2_title',
            [
                'label'   => esc_html__( 'Card 2 Title (Middle)', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'VEO 3 ENGINE',
            ]
        );
        $this->add_control(
            'card2_value',
            [
                'label'   => esc_html__( 'Card 2 Value', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '98.4% Lip-Sync',
            ]
        );

        // Metric Card 3
        $this->add_control(
            'card3_title',
            [
                'label'   => esc_html__( 'Card 3 Title (Bottom)', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'TURNAROUND',
            ]
        );
        $this->add_control(
            'card3_value',
            [
                'label'   => esc_html__( 'Card 3 Value', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '24h Delivery',
            ]
        );

        $this->end_controls_section();

        // ── Style Tab ───────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_style_colors',
            [
                'label' => esc_html__( 'Colors & Background', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'cora-real-estate' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F9F6F0',
                'selectors' => [
                    '{{WRAPPER}} .cora-ambassador-hero' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label'     => esc_html__( 'Primary Text Color', 'cora-real-estate' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#09090b',
                'selectors' => [
                    '{{WRAPPER}} .cora-ambassador-title-main' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'accent_color',
            [
                'label'     => esc_html__( 'Accent Color (Purple)', 'cora-real-estate' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#7c3aed',
                'selectors' => [
                    '{{WRAPPER}} .cora-ambassador-btn' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .cora-ambassador-badge-dot' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .cora-ambassador-feature-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $tag_text       = $settings['tag_text'] ?? '';
        $headline_main  = $settings['headline_main'] ?? '';
        $headline_serif = $settings['headline_serif'] ?? '';
        $description    = $settings['description'] ?? '';
        $features_list  = $settings['features_list'] ?? [];
        
        $cta_label      = $settings['cta_label'] ?? '';
        $cta_url        = $settings['cta_url']['url'] ?? '#';
        $cta_subtext    = $settings['cta_subtext'] ?? '';
        $shopify_text   = $settings['shopify_text'] ?? '';
        $rating_text    = $settings['rating_text'] ?? '';

        $avatar_url     = $settings['avatar_image']['url'] ?? '';

        $card1_title    = $settings['card1_title'] ?? '';
        $card1_value    = $settings['card1_value'] ?? '';
        $card2_title    = $settings['card2_title'] ?? '';
        $card2_value    = $settings['card2_value'] ?? '';
        $card3_title    = $settings['card3_title'] ?? '';
        $card3_value    = $settings['card3_value'] ?? '';

        ?>
        <style>
            .cora-ambassador-hero {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                background-color: #F9F6F0;
                padding: 80px 4% 0px 4%;
                display: flex;
                flex-direction: row;
                align-items: stretch;
                justify-content: space-between;
                position: relative;
                overflow: hidden;
                box-sizing: border-box;
                gap: 40px;
            }
            .cora-ambassador-content {
                flex: 1;
                max-width: 54%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding-bottom: 80px;
                z-index: 10;
            }
            .cora-ambassador-badge {
                align-self: flex-start;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 16px;
                background: rgba(0, 0, 0, 0.03);
                border: 1px solid rgba(0, 0, 0, 0.06);
                border-radius: 9999px;
                font-size: 12px;
                font-weight: 600;
                color: #4b5563;
                margin-bottom: 24px;
            }
            .cora-ambassador-badge-dot {
                width: 6px;
                height: 6px;
                background-color: #7c3aed;
                border-radius: 50%;
            }
            .cora-ambassador-title {
                font-size: clamp(40px, 4.5vw, 64px);
                line-height: 1.1;
                font-weight: 850;
                color: #09090b;
                margin: 0 0 24px 0;
                letter-spacing: -0.03em;
            }
            .cora-ambassador-title-main {
                display: block;
            }
            .cora-ambassador-title-serif {
                font-family: "Georgia", Garamond, serif;
                font-style: italic;
                font-weight: 400;
                color: #09090b;
            }
            .cora-ambassador-desc {
                font-size: 16px;
                line-height: 1.6;
                color: #4b5563;
                margin: 0 0 32px 0;
                max-width: 90%;
            }
            .cora-ambassador-features {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 40px;
            }
            .cora-ambassador-feature-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 14px;
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.06);
                border-radius: 9999px;
                font-size: 12px;
                font-weight: 600;
                color: #09090b;
            }
            .cora-ambassador-feature-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 14px;
                height: 14px;
                border: 1px solid #7c3aed;
                border-radius: 50%;
                color: #7c3aed;
                flex-shrink: 0;
            }
            .cora-ambassador-feature-icon svg {
                width: 8px;
                height: 8px;
                fill: none;
                stroke: currentColor;
                stroke-width: 3.5;
            }
            .cora-ambassador-cta-wrapper {
                margin-bottom: 24px;
            }
            .cora-ambassador-btn {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 16px 28px;
                background: #7c3aed;
                color: #ffffff;
                font-size: 14px;
                font-weight: 700;
                text-decoration: none;
                border-radius: 12px;
                box-shadow: 0 4px 14px rgba(124, 58, 237, 0.2);
                transition: all 0.2s ease;
            }
            .cora-ambassador-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 6px 20px rgba(124, 58, 237, 0.3);
            }
            .cora-ambassador-btn svg {
                width: 14px;
                height: 14px;
                fill: none;
                stroke: currentColor;
                stroke-width: 2.2;
            }
            .cora-ambassador-subtext {
                font-size: 11px;
                color: #6b7280;
                margin-top: 8px;
            }
            .cora-ambassador-trust {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .cora-ambassador-trust-badge {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 8px 14px;
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.05);
                border-radius: 8px;
                font-size: 10px;
                font-weight: 800;
                letter-spacing: 0.05em;
                color: #09090b;
            }
            .cora-ambassador-trust-badge svg.shopify-icon {
                width: 12px;
                height: 12px;
                fill: #95bf47;
            }
            .cora-ambassador-stars {
                color: #fbbf24;
                font-size: 10px;
                display: flex;
                align-items: center;
                gap: 1px;
            }
            
            /* Right Column */
            .cora-ambassador-visual {
                flex: 1;
                max-width: 44%;
                position: relative;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                min-height: 520px;
            }
            .cora-ambassador-img-container {
                width: 100%;
                height: 100%;
                position: relative;
                display: flex;
                align-items: flex-end;
            }
            .cora-ambassador-img {
                width: 100%;
                height: auto;
                max-height: 560px;
                object-fit: contain;
                display: block;
                mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 85%, rgba(0,0,0,0) 100%);
                -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 85%, rgba(0,0,0,0) 100%);
            }
            
            /* Floating Metrics Cards */
            .cora-floating-card {
                position: absolute;
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.06);
                border-radius: 14px;
                padding: 10px 16px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 20;
                transform-origin: center;
                pointer-events: none;
            }
            .cora-floating-card-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                background: rgba(124, 58, 237, 0.06);
                border-radius: 8px;
                color: #7c3aed;
                flex-shrink: 0;
            }
            .cora-floating-card-icon svg {
                width: 14px;
                height: 14px;
                fill: none;
                stroke: currentColor;
                stroke-width: 2.2;
            }
            .cora-floating-card-content {
                display: flex;
                flex-direction: column;
            }
            .cora-floating-card-label {
                font-size: 8px;
                font-weight: 700;
                letter-spacing: 0.05em;
                color: #6b7280;
                margin-bottom: 2px;
                text-transform: uppercase;
            }
            .cora-floating-card-value {
                font-size: 12px;
                font-weight: 850;
                color: #09090b;
                white-space: nowrap;
            }
            
            /* Specific Metric Cards Positions & Rotations */
            .cora-card-1 {
                top: 22%;
                right: -4%;
                transform: rotate(3deg);
            }
            .cora-card-2 {
                bottom: 42%;
                left: -8%;
                transform: rotate(-3deg);
            }
            .cora-card-3 {
                bottom: 18%;
                right: -2%;
                transform: rotate(2deg);
            }
            
            /* Responsive */
            @media (max-width: 1024px) {
                .cora-ambassador-hero {
                    flex-direction: column;
                    padding-top: 60px;
                }
                .cora-ambassador-content {
                    max-width: 100%;
                    padding-bottom: 40px;
                }
                .cora-ambassador-visual {
                    max-width: 80%;
                    margin: 0 auto;
                    min-height: 480px;
                }
            }
            @media (max-width: 640px) {
                .cora-ambassador-hero {
                    padding: 40px 20px 0 20px;
                }
                .cora-floating-card {
                    padding: 8px 12px;
                }
                .cora-card-1 {
                    right: 0%;
                }
                .cora-card-2 {
                    left: 0%;
                }
                .cora-card-3 {
                    right: 0%;
                }
            }
        </style>

        <section class="cora-ambassador-hero">
            <!-- Left Side Content -->
            <div class="cora-ambassador-content">
                <?php if ( $tag_text ) : ?>
                    <div class="cora-ambassador-badge">
                        <span class="cora-ambassador-badge-dot"></span>
                        <?php echo esc_html( $tag_text ); ?>
                    </div>
                <?php endif; ?>

                <h1 class="cora-ambassador-title">
                    <span class="cora-ambassador-title-main"><?php echo esc_html( $headline_main ); ?></span>
                    <span class="cora-ambassador-title-serif"><?php echo esc_html( $headline_serif ); ?></span>
                </h1>

                <?php if ( $description ) : ?>
                    <p class="cora-ambassador-desc"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $features_list ) ) : ?>
                    <div class="cora-ambassador-features">
                        <?php foreach ( $features_list as $item ) : ?>
                            <?php if ( ! empty( $item['feature_label'] ) ) : ?>
                                <span class="cora-ambassador-feature-pill">
                                    <span class="cora-ambassador-feature-icon">
                                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </span>
                                    <?php echo esc_html( $item['feature_label'] ); ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="cora-ambassador-cta-wrapper">
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="cora-ambassador-btn">
                        <?php echo esc_html( $cta_label ); ?>
                        <svg viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83M9 12l2-2 4 4"/></svg>
                    </a>
                    <?php if ( $cta_subtext ) : ?>
                        <div class="cora-ambassador-subtext"><?php echo esc_html( $cta_subtext ); ?></div>
                    <?php endif; ?>
                </div>

                <div class="cora-ambassador-trust">
                    <?php if ( $shopify_text ) : ?>
                        <span class="cora-ambassador-trust-badge">
                            <svg class="shopify-icon" viewBox="0 0 24 24">
                                <path d="M19.78 5.61a.88.88 0 00-.75-.46l-4.14-.15a.3.3 0 01-.26-.18l-1.92-3.79a.9.9 0 00-1.61 0L9.18 4.82a.3.3 0 01-.26.18l-4.14.15a.88.88 0 00-.75.46.88.88 0 000 .89l2.76 4.95a.3.3 0 010 .32L4 16.71a.88.88 0 000 .89.88.88 0 00.75.46l4.14-.15a.3.3 0 01.26.18l1.92 3.79a.9.9 0 001.61 0l1.92-3.79a.3.3 0 01.26-.18l4.14.15a.88.88 0 00.75-.46.88.88 0 000-.89l-2.76-4.95a.3.3 0 010-.32l2.76-4.95a.88.88 0 000-.89z"/>
                            </svg>
                            <?php echo esc_html( $shopify_text ); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ( $rating_text ) : ?>
                        <span class="cora-ambassador-trust-badge">
                            <?php echo esc_html( $rating_text ); ?>
                            <span class="cora-ambassador-stars">★ ★ ★ ★ ★</span>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Side Visual (Avatar & Metric Badges) -->
            <div class="cora-ambassador-visual">
                <div class="cora-ambassador-img-container">
                    <?php if ( $avatar_url ) : ?>
                        <img class="cora-ambassador-img" src="<?php echo esc_url( $avatar_url ); ?>" alt="AI Brand Ambassador" />
                    <?php endif; ?>

                    <!-- Card 1 -->
                    <?php if ( $card1_title || $card1_value ) : ?>
                        <div class="cora-floating-card cora-card-1">
                            <span class="cora-floating-card-icon">
                                <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                            </span>
                            <div class="cora-floating-card-content">
                                <span class="cora-floating-card-label"><?php echo esc_html( $card1_title ); ?></span>
                                <span class="cora-floating-card-value"><?php echo esc_html( $card1_value ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Card 2 -->
                    <?php if ( $card2_title || $card2_value ) : ?>
                        <div class="cora-floating-card cora-card-2">
                            <span class="cora-floating-card-icon">
                                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </span>
                            <div class="cora-floating-card-content">
                                <span class="cora-floating-card-label"><?php echo esc_html( $card2_title ); ?></span>
                                <span class="cora-floating-card-value"><?php echo esc_html( $card2_value ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Card 3 -->
                    <?php if ( $card3_title || $card3_value ) : ?>
                        <div class="cora-floating-card cora-card-3">
                            <span class="cora-floating-card-icon">
                                <svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            </span>
                            <div class="cora-floating-card-content">
                                <span class="cora-floating-card-label"><?php echo esc_html( $card3_title ); ?></span>
                                <span class="cora-floating-card-value"><?php echo esc_html( $card3_value ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
    }

    protected function content_template() {
        ?>
        <#
        var tag_text       = settings.tag_text || '';
        var headline_main  = settings.headline_main || '';
        var headline_serif = settings.headline_serif || '';
        var description    = settings.description || '';
        var features_list  = settings.features_list || [];
        
        var cta_label      = settings.cta_label || '';
        var cta_url        = settings.cta_url.url || '#';
        var cta_subtext    = settings.cta_subtext || '';
        var shopify_text   = settings.shopify_text || '';
        var rating_text    = settings.rating_text || '';

        var avatar_url     = settings.avatar_image.url || '';

        var card1_title    = settings.card1_title || '';
        var card1_value    = settings.card1_value || '';
        var card2_title    = settings.card2_title || '';
        var card2_value    = settings.card2_value || '';
        var card3_title    = settings.card3_title || '';
        var card3_value    = settings.card3_value || '';
        #>
        
        <style>
            .cora-ambassador-hero {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                background-color: #F9F6F0;
                padding: 80px 4% 0px 4%;
                display: flex;
                flex-direction: row;
                align-items: stretch;
                justify-content: space-between;
                position: relative;
                overflow: hidden;
                box-sizing: border-box;
                gap: 40px;
            }
            .cora-ambassador-content {
                flex: 1;
                max-width: 54%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding-bottom: 80px;
                z-index: 10;
            }
            .cora-ambassador-badge {
                align-self: flex-start;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 16px;
                background: rgba(0, 0, 0, 0.03);
                border: 1px solid rgba(0, 0, 0, 0.06);
                border-radius: 9999px;
                font-size: 12px;
                font-weight: 600;
                color: #4b5563;
                margin-bottom: 24px;
            }
            .cora-ambassador-badge-dot {
                width: 6px;
                height: 6px;
                background-color: #7c3aed;
                border-radius: 50%;
            }
            .cora-ambassador-title {
                font-size: clamp(40px, 4.5vw, 64px);
                line-height: 1.1;
                font-weight: 850;
                color: #09090b;
                margin: 0 0 24px 0;
                letter-spacing: -0.03em;
            }
            .cora-ambassador-title-main {
                display: block;
            }
            .cora-ambassador-title-serif {
                font-family: "Georgia", Garamond, serif;
                font-style: italic;
                font-weight: 400;
                color: #09090b;
            }
            .cora-ambassador-desc {
                font-size: 16px;
                line-height: 1.6;
                color: #4b5563;
                margin: 0 0 32px 0;
                max-width: 90%;
            }
            .cora-ambassador-features {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 40px;
            }
            .cora-ambassador-feature-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 14px;
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.06);
                border-radius: 9999px;
                font-size: 12px;
                font-weight: 600;
                color: #09090b;
            }
            .cora-ambassador-feature-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 14px;
                height: 14px;
                border: 1px solid #7c3aed;
                border-radius: 50%;
                color: #7c3aed;
                flex-shrink: 0;
            }
            .cora-ambassador-feature-icon svg {
                width: 8px;
                height: 8px;
                fill: none;
                stroke: currentColor;
                stroke-width: 3.5;
            }
            .cora-ambassador-cta-wrapper {
                margin-bottom: 24px;
            }
            .cora-ambassador-btn {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 16px 28px;
                background: #7c3aed;
                color: #ffffff;
                font-size: 14px;
                font-weight: 700;
                text-decoration: none;
                border-radius: 12px;
                box-shadow: 0 4px 14px rgba(124, 58, 237, 0.2);
                transition: all 0.2s ease;
            }
            .cora-ambassador-btn svg {
                width: 14px;
                height: 14px;
                fill: none;
                stroke: currentColor;
                stroke-width: 2.2;
            }
            .cora-ambassador-subtext {
                font-size: 11px;
                color: #6b7280;
                margin-top: 8px;
            }
            .cora-ambassador-trust {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .cora-ambassador-trust-badge {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 8px 14px;
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.05);
                border-radius: 8px;
                font-size: 10px;
                font-weight: 800;
                letter-spacing: 0.05em;
                color: #09090b;
            }
            .cora-ambassador-trust-badge svg.shopify-icon {
                width: 12px;
                height: 12px;
                fill: #95bf47;
            }
            .cora-ambassador-stars {
                color: #fbbf24;
                font-size: 10px;
                display: flex;
                align-items: center;
                gap: 1px;
            }
            .cora-ambassador-visual {
                flex: 1;
                max-width: 44%;
                position: relative;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                min-height: 520px;
            }
            .cora-ambassador-img-container {
                width: 100%;
                height: 100%;
                position: relative;
                display: flex;
                align-items: flex-end;
            }
            .cora-ambassador-img {
                width: 100%;
                height: auto;
                max-height: 560px;
                object-fit: contain;
                display: block;
                mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 85%, rgba(0,0,0,0) 100%);
                -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 85%, rgba(0,0,0,0) 100%);
            }
            .cora-floating-card {
                position: absolute;
                background: #ffffff;
                border: 1px solid rgba(0, 0, 0, 0.06);
                border-radius: 14px;
                padding: 10px 16px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 20;
                transform-origin: center;
            }
            .cora-floating-card-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                background: rgba(124, 58, 237, 0.06);
                border-radius: 8px;
                color: #7c3aed;
                flex-shrink: 0;
            }
            .cora-floating-card-icon svg {
                width: 14px;
                height: 14px;
                fill: none;
                stroke: currentColor;
                stroke-width: 2.2;
            }
            .cora-floating-card-content {
                display: flex;
                flex-direction: column;
            }
            .cora-floating-card-label {
                font-size: 8px;
                font-weight: 700;
                letter-spacing: 0.05em;
                color: #6b7280;
                margin-bottom: 2px;
                text-transform: uppercase;
            }
            .cora-floating-card-value {
                font-size: 12px;
                font-weight: 850;
                color: #09090b;
                white-space: nowrap;
            }
            .cora-card-1 {
                top: 22%;
                right: -4%;
                transform: rotate(3deg);
            }
            .cora-card-2 {
                bottom: 42%;
                left: -8%;
                transform: rotate(-3deg);
            }
            .cora-card-3 {
                bottom: 18%;
                right: -2%;
                transform: rotate(2deg);
            }
            @media (max-width: 1024px) {
                .cora-ambassador-hero {
                    flex-direction: column;
                    padding-top: 60px;
                }
                .cora-ambassador-content {
                    max-width: 100%;
                    padding-bottom: 40px;
                }
                .cora-ambassador-visual {
                    max-width: 80%;
                    margin: 0 auto;
                    min-height: 480px;
                }
            }
        </style>

        <section class="cora-ambassador-hero">
            <div class="cora-ambassador-content">
                <# if ( tag_text ) { #>
                    <div class="cora-ambassador-badge">
                        <span class="cora-ambassador-badge-dot"></span>
                        {{{ tag_text }}}
                    </div>
                <# } #>

                <h1 class="cora-ambassador-title">
                    <span class="cora-ambassador-title-main">{{{ headline_main }}}</span>
                    <span class="cora-ambassador-title-serif">{{{ headline_serif }}}</span>
                </h1>

                <# if ( description ) { #>
                    <p class="cora-ambassador-desc">{{{ description }}}</p>
                <# } #>

                <# if ( features_list && features_list.length ) { #>
                    <div class="cora-ambassador-features">
                        <# _.each( features_list, function( item ) { #>
                            <# if ( item.feature_label ) { #>
                                <span class="cora-ambassador-feature-pill">
                                    <span class="cora-ambassador-feature-icon">
                                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </span>
                                    {{{ item.feature_label }}}
                                </span>
                            <# } #>
                        <# } ); #>
                    </div>
                <# } #>

                <div class="cora-ambassador-cta-wrapper">
                    <a href="{{{ cta_url }}}" class="cora-ambassador-btn">
                        {{{ cta_label }}}
                        <svg viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83M9 12l2-2 4 4"/></svg>
                    </a>
                    <# if ( cta_subtext ) { #>
                        <div class="cora-ambassador-subtext">{{{ cta_subtext }}}</div>
                    <# } #>
                </div>

                <div class="cora-ambassador-trust">
                    <# if ( shopify_text ) { #>
                        <span class="cora-ambassador-trust-badge">
                            <svg class="shopify-icon" viewBox="0 0 24 24">
                                <path d="M19.78 5.61a.88.88 0 00-.75-.46l-4.14-.15a.3.3 0 01-.26-.18l-1.92-3.79a.9.9 0 00-1.61 0L9.18 4.82a.3.3 0 01-.26.18l-4.14.15a.88.88 0 00-.75.46.88.88 0 000 .89l2.76 4.95a.3.3 0 010 .32L4 16.71a.88.88 0 000 .89.88.88 0 00.75.46l4.14-.15a.3.3 0 01.26.18l1.92 3.79a.9.9 0 001.61 0l1.92-3.79a.3.3 0 01.26-.18l4.14.15a.88.88 0 00.75-.46.88.88 0 000-.89l-2.76-4.95a.3.3 0 010-.32l2.76-4.95a.88.88 0 000-.89z"/>
                            </svg>
                            {{{ shopify_text }}}
                        </span>
                    <# } #>
                    <# if ( rating_text ) { #>
                        <span class="cora-ambassador-trust-badge">
                            {{{ rating_text }}}
                            <span class="cora-ambassador-stars">★ ★ ★ ★ ★</span>
                        </span>
                    <# } #>
                </div>
            </div>

            <div class="cora-ambassador-visual">
                <div class="cora-ambassador-img-container">
                    <# if ( avatar_url ) { #>
                        <img class="cora-ambassador-img" src="{{{ avatar_url }}}" alt="AI Brand Ambassador" />
                    <# } #>

                    <# if ( card1_title || card1_value ) { #>
                        <div class="cora-floating-card cora-card-1">
                            <span class="cora-floating-card-icon">
                                <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                            </span>
                            <div class="cora-floating-card-content">
                                <span class="cora-floating-card-label">{{{ card1_title }}}</span>
                                <span class="cora-floating-card-value">{{{ card1_value }}}</span>
                            </div>
                        </div>
                    <# } #>

                    <# if ( card2_title || card2_value ) { #>
                        <div class="cora-floating-card cora-card-2">
                            <span class="cora-floating-card-icon">
                                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </span>
                            <div class="cora-floating-card-content">
                                <span class="cora-floating-card-label">{{{ card2_title }}}</span>
                                <span class="cora-floating-card-value">{{{ card2_value }}}</span>
                            </div>
                        </div>
                    <# } #>

                    <# if ( card3_title || card3_value ) { #>
                        <div class="cora-floating-card cora-card-3">
                            <span class="cora-floating-card-icon">
                                <svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            </span>
                            <div class="cora-floating-card-content">
                                <span class="cora-floating-card-label">{{{ card3_title }}}</span>
                                <span class="cora-floating-card-value">{{{ card3_value }}}</span>
                            </div>
                        </div>
                    <# } #>
                </div>
            </div>
        </section>
        <?php
    }
}
