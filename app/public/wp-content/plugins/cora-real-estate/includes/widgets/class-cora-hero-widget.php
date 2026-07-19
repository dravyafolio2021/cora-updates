<?php
/**
 * Cora Hero Elementor Widget
 *
 * A full-width, centered hero section for the Cora landing page.
 * Registered under the "cora-sections" category in the Elementor panel.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class Cora_Hero_Widget extends Widget_Base {

    public function get_name() {
        return 'cora-hero';
    }

    public function get_title() {
        return esc_html__( 'Cora Hero', 'cora-real-estate' );
    }

    public function get_icon() {
        return 'eicon-section';
    }

    public function get_categories() {
        return [ 'cora-sections' ];
    }

    public function get_keywords() {
        return [ 'hero', 'cora', 'landing', 'headline', 'banner' ];
    }

    protected function register_controls() {

        // ── Content Tab ────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_badge',
            [
                'label' => esc_html__( 'Badge', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'badge_text',
            [
                'label'       => esc_html__( 'Badge Text', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Now in Early Access',
                'placeholder' => 'e.g. New Feature',
            ]
        );

        $this->add_control(
            'badge_visible',
            [
                'label'        => esc_html__( 'Show Badge', 'cora-real-estate' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'cora-real-estate' ),
                'label_off'    => esc_html__( 'Hide', 'cora-real-estate' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->end_controls_section();

        // ── Headline ────────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_headline',
            [
                'label' => esc_html__( 'Headline', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'headline',
            [
                'label'       => esc_html__( 'Main Headline', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => 'Your Complete Real Estate Command Center',
                'rows'        => 3,
            ]
        );

        $this->add_control(
            'headline_highlight',
            [
                'label'       => esc_html__( 'Highlighted Word(s)', 'cora-real-estate' ),
                'description' => esc_html__( 'Wrap the word(s) to highlight in the headline.', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Real Estate',
            ]
        );

        $this->add_control(
            'subheadline',
            [
                'label'       => esc_html__( 'Sub Headline', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => 'Manage listings, leads, shoots, and your entire team — all from one beautifully unified platform built for Indian real estate professionals.',
                'rows'        => 4,
            ]
        );

        $this->end_controls_section();

        // ── CTAs ────────────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_ctas',
            [
                'label' => esc_html__( 'CTA Buttons', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'primary_cta_text',
            [
                'label'   => esc_html__( 'Primary CTA Label', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Start Free Trial',
            ]
        );

        $this->add_control(
            'primary_cta_url',
            [
                'label'         => esc_html__( 'Primary CTA Link', 'cora-real-estate' ),
                'type'          => Controls_Manager::URL,
                'placeholder'   => 'https://',
                'show_external' => true,
                'default'       => [
                    'url'         => '#',
                    'is_external' => false,
                    'nofollow'    => false,
                ],
            ]
        );

        $this->add_control(
            'secondary_cta_text',
            [
                'label'   => esc_html__( 'Secondary CTA Label', 'cora-real-estate' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Watch Demo',
            ]
        );

        $this->add_control(
            'secondary_cta_url',
            [
                'label'         => esc_html__( 'Secondary CTA Link', 'cora-real-estate' ),
                'type'          => Controls_Manager::URL,
                'placeholder'   => 'https://',
                'show_external' => true,
                'default'       => [
                    'url'         => '#',
                    'is_external' => false,
                    'nofollow'    => false,
                ],
            ]
        );

        $this->end_controls_section();

        // ── Feature Badges ──────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_feature_badges',
            [
                'label' => esc_html__( 'Feature Badges', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'badge_label',
            [
                'label'       => esc_html__( 'Label', 'cora-real-estate' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Feature',
                'placeholder' => 'Feature name',
            ]
        );

        $this->add_control(
            'feature_badges',
            [
                'label'       => esc_html__( 'Badges', 'cora-real-estate' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [ 'badge_label' => '✓ No credit card required' ],
                    [ 'badge_label' => '✓ 14-day free trial' ],
                    [ 'badge_label' => '✓ Cancel anytime' ],
                ],
                'title_field' => '{{{ badge_label }}}',
            ]
        );

        $this->end_controls_section();

        // ── Style Tab ───────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_style_headline',
            [
                'label' => esc_html__( 'Headline Style', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'headline_typography',
                'selector' => '{{WRAPPER}} .cora-hero-headline',
            ]
        );

        $this->add_control(
            'headline_color',
            [
                'label'     => esc_html__( 'Headline Color', 'cora-real-estate' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#09090b',
                'selectors' => [
                    '{{WRAPPER}} .cora-hero-headline' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'highlight_color',
            [
                'label'     => esc_html__( 'Highlight Color', 'cora-real-estate' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#18181b',
                'selectors' => [
                    '{{WRAPPER}} .cora-hero-headline-highlight' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_bg',
            [
                'label' => esc_html__( 'Background', 'cora-real-estate' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'cora-real-estate' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .cora-hero-section' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $headline   = $settings['headline'] ?? 'Your Complete Real Estate Command Center';
        $highlight  = $settings['headline_highlight'] ?? 'Real Estate';
        $subhead    = $settings['subheadline'] ?? '';
        $badge_text = $settings['badge_text'] ?? '';
        $show_badge = $settings['badge_visible'] === 'yes';

        $primary_text = $settings['primary_cta_text'] ?? 'Start Free Trial';
        $primary_url  = $settings['primary_cta_url']['url'] ?? '#';

        $secondary_text = $settings['secondary_cta_text'] ?? 'Watch Demo';
        $secondary_url  = $settings['secondary_cta_url']['url'] ?? '#';

        $feature_badges = $settings['feature_badges'] ?? [];

        // Highlight the keyword in the headline
        if ( $highlight ) {
            $headline = str_replace(
                esc_html( $highlight ),
                '<span class="cora-hero-headline-highlight">' . esc_html( $highlight ) . '</span>',
                esc_html( $headline )
            );
        } else {
            $headline = esc_html( $headline );
        }

        ?>
        <section class="cora-hero-section">
            <div class="cora-hero-inner">
                <?php if ( $show_badge && $badge_text ) : ?>
                <div class="cora-hero-badge">
                    <span class="cora-hero-badge-dot"></span>
                    <?php echo esc_html( $badge_text ); ?>
                </div>
                <?php endif; ?>

                <h1 class="cora-hero-headline"><?php echo $headline; ?></h1>

                <?php if ( $subhead ) : ?>
                <p class="cora-hero-sub"><?php echo esc_html( $subhead ); ?></p>
                <?php endif; ?>

                <div class="cora-hero-ctas">
                    <a href="<?php echo esc_url( $primary_url ); ?>" class="cora-hero-cta-primary">
                        <?php echo esc_html( $primary_text ); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="<?php echo esc_url( $secondary_url ); ?>" class="cora-hero-cta-secondary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                        <?php echo esc_html( $secondary_text ); ?>
                    </a>
                </div>

                <?php if ( ! empty( $feature_badges ) ) : ?>
                <div class="cora-hero-feature-badges">
                    <?php foreach ( $feature_badges as $badge ) : ?>
                        <span class="cora-hero-feature-badge"><?php echo esc_html( $badge['badge_label'] ); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }

    protected function content_template() {
        ?>
        <#
        var headline   = settings.headline || 'Your Complete Real Estate Command Center';
        var highlight  = settings.headline_highlight || '';
        var subhead    = settings.subheadline || '';
        var badgeText  = settings.badge_text || '';
        var showBadge  = settings.badge_visible === 'yes';
        var primaryText = settings.primary_cta_text || 'Start Free Trial';
        var secondaryText = settings.secondary_cta_text || 'Watch Demo';

        if ( highlight ) {
            headline = headline.replace( new RegExp( highlight, 'gi' ), '<span class="cora-hero-headline-highlight">' + highlight + '</span>' );
        }
        #>
        <section class="cora-hero-section">
            <div class="cora-hero-inner">
                <# if ( showBadge && badgeText ) { #>
                <div class="cora-hero-badge">
                    <span class="cora-hero-badge-dot"></span>
                    {{{ badgeText }}}
                </div>
                <# } #>

                <h1 class="cora-hero-headline">{{{ headline }}}</h1>

                <# if ( subhead ) { #>
                <p class="cora-hero-sub">{{{ subhead }}}</p>
                <# } #>

                <div class="cora-hero-ctas">
                    <a href="#" class="cora-hero-cta-primary">
                        {{{ primaryText }}}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="#" class="cora-hero-cta-secondary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                        {{{ secondaryText }}}
                    </a>
                </div>

                <# if ( settings.feature_badges && settings.feature_badges.length ) { #>
                <div class="cora-hero-feature-badges">
                    <# _.each( settings.feature_badges, function( item ) { #>
                        <span class="cora-hero-feature-badge">{{{ item.badge_label }}}</span>
                    <# } ); #>
                </div>
                <# } #>
            </div>
        </section>
        <?php
    }
}
