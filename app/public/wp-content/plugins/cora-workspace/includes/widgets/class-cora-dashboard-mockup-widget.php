<?php
/**
 * Cora Dashboard Mockup Elementor Widget
 *
 * A responsive, animated dashboard preview for the Cora landing page.
 * Registered under the "cora-sections" category in the Elementor panel.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class Cora_Dashboard_Mockup_Widget extends Widget_Base {

    public function get_name() {
        return 'cora-dashboard-mockup';
    }

    public function get_title() {
        return esc_html__( 'Cora Dashboard Mockup', 'cora-workspace' );
    }

    public function get_icon() {
        return 'eicon-device-desktop';
    }

    public function get_categories() {
        return [ 'cora-sections' ];
    }

    public function get_keywords() {
        return [ 'dashboard', 'mockup', 'preview', 'screenshot', 'cora', 'landing' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'Content', 'cora-workspace' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'section_label',
            [
                'label'   => esc_html__( 'Section Label', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'PLATFORM PREVIEW',
            ]
        );

        $this->add_control(
            'headline',
            [
                'label'   => esc_html__( 'Headline', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'Everything you need, in one place',
                'rows'    => 2,
            ]
        );

        $this->add_control(
            'description',
            [
                'label'   => esc_html__( 'Description', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'A powerful, intuitive workspace for modern real estate teams. Manage listings, leads, shoots, finances, and your team — all from one unified platform.',
                'rows'    => 3,
            ]
        );

        $this->end_controls_section();

        // ── Stats ───────────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_stats',
            [
                'label' => esc_html__( 'Platform Stats', 'cora-workspace' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'stat_1_value',
            [
                'label'   => esc_html__( 'Stat 1 Value', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '2,400+',
            ]
        );
        $this->add_control(
            'stat_1_label',
            [
                'label'   => esc_html__( 'Stat 1 Label', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Properties Managed',
            ]
        );

        $this->add_control(
            'stat_2_value',
            [
                'label'   => esc_html__( 'Stat 2 Value', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '98%',
            ]
        );
        $this->add_control(
            'stat_2_label',
            [
                'label'   => esc_html__( 'Stat 2 Label', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Client Satisfaction',
            ]
        );

        $this->add_control(
            'stat_3_value',
            [
                'label'   => esc_html__( 'Stat 3 Value', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXT,
                'default' => '3x',
            ]
        );
        $this->add_control(
            'stat_3_label',
            [
                'label'   => esc_html__( 'Stat 3 Label', 'cora-workspace' ),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Faster Closings',
            ]
        );

        $this->end_controls_section();

        // ── Style ───────────────────────────────────────────────────────────────
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Style', 'cora-workspace' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'cora-workspace' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fafafa',
                'selectors' => [
                    '{{WRAPPER}} .cora-mockup-section' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'headline_color',
            [
                'label'     => esc_html__( 'Headline Color', 'cora-workspace' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#09090b',
                'selectors' => [
                    '{{WRAPPER}} .cora-mockup-headline' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $section_label = $settings['section_label'] ?? 'PLATFORM PREVIEW';
        $headline      = $settings['headline']      ?? 'Everything you need, in one place';
        $description   = $settings['description']   ?? '';

        $stat_1_value = $settings['stat_1_value'] ?? '2,400+';
        $stat_1_label = $settings['stat_1_label'] ?? 'Properties Managed';
        $stat_2_value = $settings['stat_2_value'] ?? '98%';
        $stat_2_label = $settings['stat_2_label'] ?? 'Client Satisfaction';
        $stat_3_value = $settings['stat_3_value'] ?? '3x';
        $stat_3_label = $settings['stat_3_label'] ?? 'Faster Closings';

        ?>
        <section class="cora-mockup-section">
            <div class="cora-mockup-inner">

                <!-- Section label and header text -->
                <div class="cora-mockup-header">
                    <span class="cora-mockup-label"><?php echo esc_html( $section_label ); ?></span>
                    <h2 class="cora-mockup-headline"><?php echo esc_html( $headline ); ?></h2>
                    <?php if ( $description ) : ?>
                    <p class="cora-mockup-desc"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Stats row -->
                <div class="cora-mockup-stats">
                    <div class="cora-mockup-stat">
                        <span class="cora-mockup-stat-value"><?php echo esc_html( $stat_1_value ); ?></span>
                        <span class="cora-mockup-stat-label"><?php echo esc_html( $stat_1_label ); ?></span>
                    </div>
                    <div class="cora-mockup-stat-divider"></div>
                    <div class="cora-mockup-stat">
                        <span class="cora-mockup-stat-value"><?php echo esc_html( $stat_2_value ); ?></span>
                        <span class="cora-mockup-stat-label"><?php echo esc_html( $stat_2_label ); ?></span>
                    </div>
                    <div class="cora-mockup-stat-divider"></div>
                    <div class="cora-mockup-stat">
                        <span class="cora-mockup-stat-value"><?php echo esc_html( $stat_3_value ); ?></span>
                        <span class="cora-mockup-stat-label"><?php echo esc_html( $stat_3_label ); ?></span>
                    </div>
                </div>

                <!-- Dashboard Mockup Frame -->
                <div class="cora-mockup-frame-wrap">
                    <div class="cora-mockup-browser-bar">
                        <div class="cora-mockup-browser-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <div class="cora-mockup-browser-url">cora.studio / workspace</div>
                    </div>
                    <div class="cora-mockup-frame">
                        <!-- Sidebar -->
                        <div class="cora-mockup-sidebar">
                            <div class="cora-mockup-sidebar-logo">
                                <div class="cora-mockup-logo-mark"></div>
                                <span>Cora</span>
                            </div>
                            <?php
                            $nav_items = [
                                [ 'label' => 'Dashboard',   'active' => true  ],
                                [ 'label' => 'Listings',    'active' => false ],
                                [ 'label' => 'Leads',       'active' => false ],
                                [ 'label' => 'Shoots',      'active' => false ],
                                [ 'label' => 'Finances',    'active' => false ],
                                [ 'label' => 'Team',        'active' => false ],
                            ];
                            foreach ( $nav_items as $item ) :
                            ?>
                            <div class="cora-mockup-nav-item <?php echo $item['active'] ? 'active' : ''; ?>">
                                <div class="cora-mockup-nav-dot"></div>
                                <?php echo esc_html( $item['label'] ); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Main Content -->
                        <div class="cora-mockup-main">
                            <!-- Top row cards -->
                            <div class="cora-mockup-cards-row">
                                <?php
                                $cards = [
                                    [ 'title' => 'Total Listings',   'value' => '247',    'change' => '+12%' ],
                                    [ 'title' => 'Active Leads',     'value' => '1,834',  'change' => '+8%'  ],
                                    [ 'title' => 'Revenue (Oct)',    'value' => '₹42.3L', 'change' => '+23%' ],
                                    [ 'title' => 'Shoots This Month','value' => '38',     'change' => '+5%'  ],
                                ];
                                foreach ( $cards as $card ) :
                                ?>
                                <div class="cora-mockup-card">
                                    <span class="cora-mockup-card-title"><?php echo esc_html( $card['title'] ); ?></span>
                                    <span class="cora-mockup-card-value"><?php echo esc_html( $card['value'] ); ?></span>
                                    <span class="cora-mockup-card-change"><?php echo esc_html( $card['change'] ); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Chart placeholder -->
                            <div class="cora-mockup-chart-wrap">
                                <div class="cora-mockup-chart-header">
                                    <span class="cora-mockup-chart-title">Revenue Overview</span>
                                    <span class="cora-mockup-chart-period">Last 6 months</span>
                                </div>
                                <div class="cora-mockup-chart-bars">
                                    <?php
                                    $bar_heights = [ 45, 60, 40, 70, 55, 80 ];
                                    $months      = [ 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct' ];
                                    foreach ( $bar_heights as $i => $h ) :
                                    ?>
                                    <div class="cora-mockup-bar-col">
                                        <div class="cora-mockup-bar" style="height:<?php echo esc_attr( $h ); ?>px;"></div>
                                        <span><?php echo esc_html( $months[ $i ] ); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Recent activities -->
                            <div class="cora-mockup-activity">
                                <div class="cora-mockup-activity-title">Recent Activity</div>
                                <?php
                                $activities = [
                                    [ 'label' => 'New lead: Arjun Sharma', 'time' => '2m ago',  'type' => 'lead' ],
                                    [ 'label' => 'Shoot booked: 3BHK Andheri', 'time' => '18m ago', 'type' => 'shoot' ],
                                    [ 'label' => 'Listing closed: Bandra West', 'time' => '1h ago',  'type' => 'listing' ],
                                ];
                                foreach ( $activities as $act ) :
                                ?>
                                <div class="cora-mockup-activity-item">
                                    <div class="cora-mockup-activity-dot type-<?php echo esc_attr( $act['type'] ); ?>"></div>
                                    <span class="cora-mockup-activity-label"><?php echo esc_html( $act['label'] ); ?></span>
                                    <span class="cora-mockup-activity-time"><?php echo esc_html( $act['time'] ); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <?php
    }

    protected function content_template() {
        ?>
        <#
        var sectionLabel = settings.section_label || 'PLATFORM PREVIEW';
        var headline     = settings.headline || 'Everything you need, in one place';
        var description  = settings.description || '';
        #>
        <section class="cora-mockup-section">
            <div class="cora-mockup-inner">
                <div class="cora-mockup-header">
                    <span class="cora-mockup-label">{{{ sectionLabel }}}</span>
                    <h2 class="cora-mockup-headline">{{{ headline }}}</h2>
                    <# if ( description ) { #>
                    <p class="cora-mockup-desc">{{{ description }}}</p>
                    <# } #>
                </div>
                <div class="cora-mockup-stats">
                    <div class="cora-mockup-stat">
                        <span class="cora-mockup-stat-value">{{{ settings.stat_1_value }}}</span>
                        <span class="cora-mockup-stat-label">{{{ settings.stat_1_label }}}</span>
                    </div>
                    <div class="cora-mockup-stat-divider"></div>
                    <div class="cora-mockup-stat">
                        <span class="cora-mockup-stat-value">{{{ settings.stat_2_value }}}</span>
                        <span class="cora-mockup-stat-label">{{{ settings.stat_2_label }}}</span>
                    </div>
                    <div class="cora-mockup-stat-divider"></div>
                    <div class="cora-mockup-stat">
                        <span class="cora-mockup-stat-value">{{{ settings.stat_3_value }}}</span>
                        <span class="cora-mockup-stat-label">{{{ settings.stat_3_label }}}</span>
                    </div>
                </div>
                <div class="cora-mockup-frame-wrap">
                    <div class="cora-mockup-browser-bar">
                        <div class="cora-mockup-browser-dots"><span></span><span></span><span></span></div>
                        <div class="cora-mockup-browser-url">cora.studio / workspace</div>
                    </div>
                    <div class="cora-mockup-frame">
                        <div class="cora-mockup-sidebar">
                            <div class="cora-mockup-sidebar-logo">
                                <div class="cora-mockup-logo-mark"></div>
                                <span>Cora</span>
                            </div>
                            <div class="cora-mockup-nav-item active"><div class="cora-mockup-nav-dot"></div>Dashboard</div>
                            <div class="cora-mockup-nav-item"><div class="cora-mockup-nav-dot"></div>Listings</div>
                            <div class="cora-mockup-nav-item"><div class="cora-mockup-nav-dot"></div>Leads</div>
                            <div class="cora-mockup-nav-item"><div class="cora-mockup-nav-dot"></div>Shoots</div>
                            <div class="cora-mockup-nav-item"><div class="cora-mockup-nav-dot"></div>Finances</div>
                            <div class="cora-mockup-nav-item"><div class="cora-mockup-nav-dot"></div>Team</div>
                        </div>
                        <div class="cora-mockup-main">
                            <div class="cora-mockup-cards-row">
                                <div class="cora-mockup-card"><span class="cora-mockup-card-title">Total Listings</span><span class="cora-mockup-card-value">247</span><span class="cora-mockup-card-change">+12%</span></div>
                                <div class="cora-mockup-card"><span class="cora-mockup-card-title">Active Leads</span><span class="cora-mockup-card-value">1,834</span><span class="cora-mockup-card-change">+8%</span></div>
                                <div class="cora-mockup-card"><span class="cora-mockup-card-title">Revenue (Oct)</span><span class="cora-mockup-card-value">₹42.3L</span><span class="cora-mockup-card-change">+23%</span></div>
                                <div class="cora-mockup-card"><span class="cora-mockup-card-title">Shoots</span><span class="cora-mockup-card-value">38</span><span class="cora-mockup-card-change">+5%</span></div>
                            </div>
                            <div class="cora-mockup-chart-wrap">
                                <div class="cora-mockup-chart-header">
                                    <span class="cora-mockup-chart-title">Revenue Overview</span>
                                    <span class="cora-mockup-chart-period">Last 6 months</span>
                                </div>
                                <div class="cora-mockup-chart-bars">
                                    <div class="cora-mockup-bar-col"><div class="cora-mockup-bar" style="height:45px;"></div><span>May</span></div>
                                    <div class="cora-mockup-bar-col"><div class="cora-mockup-bar" style="height:60px;"></div><span>Jun</span></div>
                                    <div class="cora-mockup-bar-col"><div class="cora-mockup-bar" style="height:40px;"></div><span>Jul</span></div>
                                    <div class="cora-mockup-bar-col"><div class="cora-mockup-bar" style="height:70px;"></div><span>Aug</span></div>
                                    <div class="cora-mockup-bar-col"><div class="cora-mockup-bar" style="height:55px;"></div><span>Sep</span></div>
                                    <div class="cora-mockup-bar-col"><div class="cora-mockup-bar" style="height:80px;"></div><span>Oct</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
