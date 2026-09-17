<?php
/**
 * Cora Professional Services & Agency Module
 * 
 * Provides end-to-end operating capabilities for Professional Services firms,
 * Advisory consultancies, Digital agencies, Law practices, and Accounting/CA firms.
 * Implements the 13 core domain entities and full P0/P1/P2 feature roadmap.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Professional_Services_Module implements Cora_Module_Interface {
    
    public function get_module_id() : string {
        return 'professional_services';
    }

    public function get_display_name() : string {
        return 'Professional Services & Agencies';
    }

    public function get_navigation_groups(string $active_role) : array {
        $enabled = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();
        $is_enabled = function( $slug ) use ( $enabled ) {
            return in_array( $slug, $enabled, true );
        };

        $groups = array();

        // 1. Workspace Core Group (P0 Foundation)
        $workspace_items = array(
            'dashboard' => array(
                'title' => 'Firm Overview',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>'
            )
        );

        if ( $is_enabled( 'financials' ) ) {
            $workspace_items['financials'] = array(
                'title' => 'Retainers & Billing (SAC 9983)',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
            );
        }

        if ( $is_enabled( 'vault' ) ) {
            $workspace_items['vault'] = array(
                'title' => 'SOW & Contracts Vault',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>'
            );
        }

        if ( $is_enabled( 'team-roles' ) ) {
            $workspace_items['team-roles'] = array(
                'title' => 'Partners & Staff Roles',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
            );
        }

        if ( $is_enabled( 'calendar' ) ) {
            $workspace_items['calendar'] = array(
                'title' => 'Engagement Calendar',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
            );
        }

        $groups[] = array( 'label' => 'Firm Workspace', 'items' => $workspace_items );

        // 2. Client Engagements & Delivery (P0 / P1)
        $ops_items = array();
        if ( $is_enabled( 'leads' ) ) {
            $ops_items['leads'] = array(
                'title' => 'Clients & Engagements',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>'
            );
        }

        if ( $is_enabled( 'tasks' ) ) {
            $ops_items['tasks'] = array(
                'title' => 'Milestones & Deliverables',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>'
            );
        }

        if ( $is_enabled( 'attendance' ) || $is_enabled( 'timesheets' ) ) {
            $ops_items['attendance'] = array(
                'title' => 'Timesheets & Billable Hours',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
            );
        }

        if ( $is_enabled( 'crew_scheduler' ) ) {
            $ops_items['crew_scheduler'] = array(
                'title' => 'Consultant Capacity Planner',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
            );
        }

        if ( ! empty( $ops_items ) ) {
            $groups[] = array( 'label' => 'Delivery & Engagements', 'items' => $ops_items );
        }

        // 3. Client Acquisition & Onboarding (P1 Scale)
        $sales_items = array();
        if ( $is_enabled( 'canvas' ) ) {
            $sales_items['canvas'] = array(
                'title' => 'Proposals & Landing Pages',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'
            );
        }

        if ( $is_enabled( 'forms' ) ) {
            $sales_items['forms'] = array(
                'title' => 'Discovery Briefs & KYC',
                'badge' => 'v1.0',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path></svg>'
            );
        }

        if ( $is_enabled( 'emails' ) ) {
            $sales_items['emails'] = array(
                'title' => 'Client Updates & Broadcasts',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'
            );
        }

        if ( $is_enabled( 'review_acquisition' ) ) {
            $sales_items['review_acquisition'] = array(
                'title' => 'Case Studies & Testimonials',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>'
            );
        }

        if ( ! empty( $sales_items ) ) {
            $groups[] = array( 'label' => 'Acquisition & Onboarding', 'items' => $sales_items );
        }

        // 4. AI Advisory & Firm Intelligence (P1 / P2)
        $ai_items = array();
        if ( $is_enabled( 'knowledge-base' ) ) {
            $ai_items['knowledge-base'] = array(
                'title' => 'Firm Knowledge Base & RAG',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>'
            );
        }

        if ( $is_enabled( 'blogs' ) ) {
            $ai_items['blogs'] = array(
                'title' => 'Thought Leadership & SEO',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
            );
        }

        if ( $is_enabled( 'mcp' ) ) {
            $ai_items['mcp'] = array(
                'title' => 'AI Copilots & MCP Tools',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="15" x2="23" y2="15"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="15" x2="4" y2="15"></line></svg>'
            );
        }

        if ( ! empty( $ai_items ) ) {
            $groups[] = array( 'label' => 'AI Advisory Suite', 'items' => $ai_items );
        }

        // 5. Settings & Governance
        $groups[] = array(
            'label' => 'Settings',
            'items' => array(
                'feature-hub' => array(
                    'title' => 'App Modules',
                    'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>'
                ),
                'settings-suite' => array(
                    'title' => 'Settings',
                    'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
                )
            )
        );

        return $groups;
    }

    public function get_industry_roles() : array {
        return array(
            'administrator'            => 'Managing Partner / Firm Director',
            'cora_manager'             => 'Practice Lead / Senior Partner',
            'cora_consultant'          => 'Senior Consultant / Advisory Lead',
            'cora_analyst'             => 'Analyst / Associate',
            'cora_billing_officer'     => 'Billing Specialist / Finance Lead',
            'cora_client_stakeholder'  => 'Client Executive / Stakeholder'
        );
    }

    public function get_crm_stages() : array {
        return array(
            'Inbound RFP / Intake' => array(
                'label' => 'Inbound RFP / Intake',
                'badge' => 'bg-blue-100 border border-blue-200/60',
                'desc'  => 'Initial inbound RFPs, prospective client inquiries & discovery intake'
            ),
            'Discovery & Conflict Check' => array(
                'label' => 'Discovery & Conflict Check',
                'badge' => 'bg-indigo-100 border border-indigo-200/60',
                'desc'  => 'Scoping call, conflict of interest check & technical feasibility audit'
            ),
            'Proposal & Engagement Letter' => array(
                'label' => 'Proposal & Engagement Letter',
                'badge' => 'bg-purple-100 border border-purple-200/60',
                'desc'  => 'Scope of Work (SOW), fee schedule, rate card & engagement letter drafting'
            ),
            'E-Sign & Retainer Deposit' => array(
                'label' => 'E-Sign & Retainer Deposit',
                'badge' => 'bg-amber-100 border border-amber-200/60',
                'desc'  => 'MSA/SOW under client e-signature and advance retainer deposit collection'
            ),
            'Active Advisory Engagement' => array(
                'label' => 'Active Advisory Engagement',
                'badge' => 'bg-emerald-100 border border-emerald-200/60',
                'desc'  => 'Active sprint execution, milestone delivery, time tracking & monthly billing'
            ),
            'Completed / Archival Review' => array(
                'label' => 'Completed / Archival Review',
                'badge' => 'bg-zinc-100 border border-zinc-200/60',
                'desc'  => 'Deliverable sign-off, final invoice settlement, review collection & audit archival'
            )
        );
    }

    public function setup_database_tables() {
        // Core tables are maintained centrally with tenant isolation
    }
}
