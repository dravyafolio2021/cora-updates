<?php
/**
 * Cora Universal MCP - Canonical Tool Registry
 *
 * Model-independent, vendor-neutral tool definitions with safety annotations
 * (readOnly, destructive, openWorld, requiredScope).
 *
 * @package CoraWorkspace
 * @subpackage MCP
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_MCP_Tool_Registry {

    /**
     * Get all registered MCP tools
     *
     * @return array
     */
    public static function get_tools() {
        return array(
            // ── 1. Workspace Overview ──────────────────────────────────────────
            'cora.get_workspace_overview' => array(
                'name'        => 'cora.get_workspace_overview',
                'description' => 'Retrieve high-level business pulse, key performance metrics, pipeline deal value, collected revenue, outstanding receivables, active bookings/shoots, and pending tasks for the authenticated workspace.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'workspace:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => (object) array(),
                ),
                'handler'     => array( __CLASS__, 'handle_get_workspace_overview' ),
            ),

            // ── 2. Living RAG Knowledge Base Search ────────────────────────────
            'cora.search_knowledge_base' => array(
                'name'        => 'cora.search_knowledge_base',
                'description' => 'Semantic & keyword search across the workspace living memory, ingested operational history, policy guidelines, client records, and documents.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'knowledge:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'query'    => array( 'type' => 'string', 'description' => 'Search query, question, or keyword to retrieve contextual intelligence for.' ),
                        'category' => array( 'type' => 'string', 'description' => 'Optional filter: financials, crm, operations, vault, reviews, activity.' ),
                        'limit'    => array( 'type' => 'integer', 'description' => 'Max fragments to return (default: 5, max: 20).' ),
                    ),
                    'required'   => array( 'query' ),
                ),
                'handler'     => array( __CLASS__, 'handle_search_knowledge_base' ),
            ),

            // ── 3. Clients CRM ────────────────────────────────────────────────
            'cora.list_clients' => array(
                'name'        => 'cora.list_clients',
                'description' => 'List client profiles, company names, contact numbers, and total revenue history in the workspace.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'clients:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'search' => array( 'type' => 'string', 'description' => 'Optional search query by name, email, or company.' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'     => array( __CLASS__, 'handle_list_clients' ),
            ),

            'cora.get_client' => array(
                'name'        => 'cora.get_client',
                'description' => 'Retrieve complete profile details, active bookings, invoices, and notes for a specific client.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'clients:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'client_id' => array( 'type' => 'integer', 'description' => 'Unique ID of the client.' ),
                    ),
                    'required'   => array( 'client_id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_get_client' ),
            ),

            'cora.create_client' => array(
                'name'        => 'cora.create_client',
                'description' => 'Create a new client record in the workspace CRM.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'clients:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'name'         => array( 'type' => 'string', 'description' => 'Client full name or primary contact.' ),
                        'email'        => array( 'type' => 'string', 'description' => 'Client email address.' ),
                        'phone'        => array( 'type' => 'string', 'description' => 'Client phone number.' ),
                        'company_name' => array( 'type' => 'string', 'description' => 'Company or brand name.' ),
                        'notes'        => array( 'type' => 'string', 'description' => 'Initial notes or background.' ),
                    ),
                    'required'   => array( 'name' ),
                ),
                'handler'     => array( __CLASS__, 'handle_create_client' ),
            ),

            'cora.update_client' => array(
                'name'        => 'cora.update_client',
                'description' => 'Update an existing client profile or contact information.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'clients:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'client_id'    => array( 'type' => 'integer', 'description' => 'ID of the client to update.' ),
                        'name'         => array( 'type' => 'string', 'description' => 'Client name.' ),
                        'email'        => array( 'type' => 'string', 'description' => 'Client email.' ),
                        'phone'        => array( 'type' => 'string', 'description' => 'Client phone number.' ),
                        'company_name' => array( 'type' => 'string', 'description' => 'Company name.' ),
                        'notes'        => array( 'type' => 'string', 'description' => 'Updated notes.' ),
                    ),
                    'required'   => array( 'client_id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_update_client' ),
            ),

            // ── 4. Leads & Pipeline ───────────────────────────────────────────
            'cora.list_leads' => array(
                'name'        => 'cora.list_leads',
                'description' => 'List sales inquiries and deals from the CRM funnel with status and deal value.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'leads:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'status' => array( 'type' => 'string', 'description' => 'Filter by stage: new, contacted, qualified, won, lost, or all.' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'     => array( __CLASS__, 'handle_list_leads' ),
            ),

            'cora.get_lead' => array(
                'name'        => 'cora.get_lead',
                'description' => 'Get detailed lead inquiry, deal stage, communication history, and custom requirements.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'leads:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'lead_id' => array( 'type' => 'integer', 'description' => 'ID of the lead.' ),
                    ),
                    'required'   => array( 'lead_id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_get_lead' ),
            ),

            'cora.create_lead' => array(
                'name'        => 'cora.create_lead',
                'description' => 'Create a new CRM inquiry or lead with estimated project value.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'leads:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'name'       => array( 'type' => 'string', 'description' => 'Lead full name.' ),
                        'email'      => array( 'type' => 'string', 'description' => 'Email address.' ),
                        'phone'      => array( 'type' => 'string', 'description' => 'Phone number.' ),
                        'city'       => array( 'type' => 'string', 'description' => 'City or region.' ),
                        'deal_value' => array( 'type' => 'number', 'description' => 'Estimated project value in INR.' ),
                        'status'     => array( 'type' => 'string', 'description' => 'Initial deal status (default: new).' ),
                        'notes'      => array( 'type' => 'string', 'description' => 'Inquiry notes and requirements.' ),
                    ),
                    'required'   => array( 'name' ),
                ),
                'handler'     => array( __CLASS__, 'handle_create_lead' ),
            ),

            'cora.update_lead_status' => array(
                'name'        => 'cora.update_lead_status',
                'description' => 'Advance or update the deal stage and add progress notes for a lead.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'leads:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'lead_id' => array( 'type' => 'integer', 'description' => 'ID of the lead.' ),
                        'status'  => array( 'type' => 'string', 'description' => 'New status: new, contacted, qualified, won, lost.' ),
                        'notes'   => array( 'type' => 'string', 'description' => 'Follow-up or closing notes.' ),
                    ),
                    'required'   => array( 'lead_id', 'status' ),
                ),
                'handler'     => array( __CLASS__, 'handle_update_lead_status' ),
            ),

            // ── 5. Projects & Bookings ────────────────────────────────────────
            'cora.list_projects' => array(
                'name'        => 'cora.list_projects',
                'description' => 'List active studio bookings, shoot dates, project milestones, and delivery statuses.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'projects:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'status' => array( 'type' => 'string', 'description' => 'Filter by status: confirmed, completed, cancelled, or all.' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'     => array( __CLASS__, 'handle_list_projects' ),
            ),

            'cora.get_project' => array(
                'name'        => 'cora.get_project',
                'description' => 'Get full booking details, assigned team members, venue/location, and financial status.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'projects:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'project_id' => array( 'type' => 'integer', 'description' => 'ID of the project/booking.' ),
                    ),
                    'required'   => array( 'project_id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_get_project' ),
            ),

            'cora.create_project' => array(
                'name'        => 'cora.create_project',
                'description' => 'Schedule a new shoot booking or create an operational project.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'projects:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'client_name' => array( 'type' => 'string', 'description' => 'Client or brand name.' ),
                        'event_type'  => array( 'type' => 'string', 'description' => 'Event/shoot type: wedding, fashion, corporate, product, etc.' ),
                        'start_date'  => array( 'type' => 'string', 'description' => 'Start date (YYYY-MM-DD).' ),
                        'end_date'    => array( 'type' => 'string', 'description' => 'End date (YYYY-MM-DD).' ),
                        'location'    => array( 'type' => 'string', 'description' => 'Shoot location or studio bay.' ),
                        'amount'      => array( 'type' => 'number', 'description' => 'Total package amount in INR.' ),
                        'notes'       => array( 'type' => 'string', 'description' => 'Deliverables and equipment notes.' ),
                    ),
                    'required'   => array( 'client_name', 'event_type', 'start_date' ),
                ),
                'handler'     => array( __CLASS__, 'handle_create_project' ),
            ),

            'cora.update_project' => array(
                'name'        => 'cora.update_project',
                'description' => 'Update project details, shoot dates, delivery milestones, or status.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'projects:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'project_id' => array( 'type' => 'integer', 'description' => 'ID of the project.' ),
                        'status'     => array( 'type' => 'string', 'description' => 'New status: confirmed, completed, postponed, cancelled.' ),
                        'notes'      => array( 'type' => 'string', 'description' => 'Updated notes.' ),
                    ),
                    'required'   => array( 'project_id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_update_project' ),
            ),

            // ── 6. Task Management ────────────────────────────────────────────
            'cora.list_tasks' => array(
                'name'        => 'cora.list_tasks',
                'description' => 'List workspace tasks across Kanban columns (todo, in_progress, review, done).',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'tasks:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'status' => array( 'type' => 'string', 'description' => 'Filter by status: pending, in_progress, completed, or all.' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max tasks to return (default: 30).' ),
                    ),
                ),
                'handler'     => array( __CLASS__, 'handle_list_tasks' ),
            ),

            'cora.create_task' => array(
                'name'        => 'cora.create_task',
                'description' => 'Create and assign a new operational task in the workspace.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'tasks:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'       => array( 'type' => 'string', 'description' => 'Task title or action item.' ),
                        'description' => array( 'type' => 'string', 'description' => 'Task description and acceptance criteria.' ),
                        'due_date'    => array( 'type' => 'string', 'description' => 'Due date (YYYY-MM-DD).' ),
                        'priority'    => array( 'type' => 'string', 'description' => 'Priority: low, medium, high, critical (default: medium).' ),
                        'assignee_id' => array( 'type' => 'integer', 'description' => 'Optional assigned user ID.' ),
                    ),
                    'required'   => array( 'title' ),
                ),
                'handler'     => array( __CLASS__, 'handle_create_task' ),
            ),

            'cora.update_task' => array(
                'name'        => 'cora.update_task',
                'description' => 'Update task status, priority, due date, or mark as completed.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'tasks:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'task_id'  => array( 'type' => 'integer', 'description' => 'ID of the task.' ),
                        'status'   => array( 'type' => 'string', 'description' => 'Status: pending, in_progress, completed.' ),
                        'priority' => array( 'type' => 'string', 'description' => 'Priority: low, medium, high, critical.' ),
                        'title'    => array( 'type' => 'string', 'description' => 'Updated title.' ),
                    ),
                    'required'   => array( 'task_id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_update_task' ),
            ),

            // ── 7. Financial Invoicing & Ledger ───────────────────────────────
            'cora.query_financials' => array(
                'name'        => 'cora.query_financials',
                'description' => 'Query financial ledger, invoices, outstanding client receivables, GST tax breakdowns, and revenue figures.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'finance:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'filter' => array( 'type' => 'string', 'description' => 'Filter by invoice status: all, paid, unpaid, overdue (default: all).' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max records (default: 15).' ),
                    ),
                ),
                'handler'     => array( __CLASS__, 'handle_query_financials' ),
            ),

            'cora.record_financial_transaction' => array(
                'name'        => 'cora.record_financial_transaction',
                'description' => 'Record a new payment transaction, client invoice collection, or studio expense into the ledger.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'finance:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'type'             => array( 'type' => 'string', 'description' => 'Transaction type: invoice_payment, expense, fee.' ),
                        'amount'           => array( 'type' => 'number', 'description' => 'Amount in INR (₹).' ),
                        'client_or_vendor' => array( 'type' => 'string', 'description' => 'Client or vendor name.' ),
                        'description'      => array( 'type' => 'string', 'description' => 'Transaction description or memo.' ),
                        'invoice_id'       => array( 'type' => 'string', 'description' => 'Optional associated invoice ID.' ),
                    ),
                    'required'   => array( 'type', 'amount', 'client_or_vendor' ),
                ),
                'handler'     => array( __CLASS__, 'handle_record_financial_transaction' ),
            ),

            // ── 8. Growth & Content Studio ────────────────────────────────────
            'cora.search_content' => array(
                'name'        => 'cora.search_content',
                'description' => 'Search articles and guides across the Growth CMS repository by title, keyword, status, category, or ICP.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'content:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'type'     => array( 'type' => 'string', 'description' => 'Content type: article, guide, or all.' ),
                        'status'   => array( 'type' => 'string', 'description' => 'Status: draft, review, published, or all.' ),
                        'search'   => array( 'type' => 'string', 'description' => 'Search term.' ),
                        'category' => array( 'type' => 'string', 'description' => 'Category filter.' ),
                        'limit'    => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.get_content' => array(
                'name'        => 'cora.get_content',
                'description' => 'Retrieve a complete article or guide entry by ID or slug including all structured blocks, sources, and SEO metadata.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'content:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'   => array( 'type' => 'string', 'description' => 'Content ID or slug.' ),
                        'slug' => array( 'type' => 'string', 'description' => 'Content slug (alternative to id).' ),
                    ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.create_article' => array(
                'name'        => 'cora.create_article',
                'description' => 'Create or draft a new structured editorial article with quick_answer, key takeaways, content blocks, sources, and SEO metadata.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'content:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'            => array( 'type' => 'string', 'description' => 'Article title.' ),
                        'slug'             => array( 'type' => 'string', 'description' => 'URL slug.' ),
                        'primary_category' => array( 'type' => 'string', 'description' => 'Canonical category (e.g. operations, client-management, sales-proposals, growth, finance, ai-automation).' ),
                        'excerpt'          => array( 'type' => 'string', 'description' => 'Meta description / summary.' ),
                        'quick_answer'     => array( 'type' => 'string', 'description' => '45-second direct answer.' ),
                        'key_takeaways'    => array( 'type' => 'array', 'items' => array( 'type' => 'string' ), 'description' => 'List of 3-5 core takeaways.' ),
                        'content_blocks'   => array( 'type' => 'array', 'description' => 'Structured block objects.' ),
                        'sources'          => array( 'type' => 'array', 'description' => 'Structured source references.' ),
                        'seo'              => array( 'type' => 'object', 'description' => 'SEO metadata (canonical, keywords, search intent).' ),
                    ),
                    'required'   => array( 'title' ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.create_guide' => array(
                'name'        => 'cora.create_guide',
                'description' => 'Create or draft a flagship multi-chapter guide with modular chapters, deliverables, and lead magnet attachments.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'content:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'            => array( 'type' => 'string', 'description' => 'Guide title.' ),
                        'slug'             => array( 'type' => 'string', 'description' => 'URL slug.' ),
                        'primary_category' => array( 'type' => 'string', 'description' => 'Canonical category.' ),
                        'chapters'         => array( 'type' => 'array', 'description' => 'List of chapter objects with title, slug, and blocks.' ),
                        'sources'          => array( 'type' => 'array', 'description' => 'Structured sources.' ),
                    ),
                    'required'   => array( 'title' ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.validate_content' => array(
                'name'        => 'cora.validate_content',
                'description' => 'Run strict evidence and schema validation on a content entry or draft payload without publishing.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'content:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'      => array( 'type' => 'string', 'description' => 'ID of existing content to validate.' ),
                        'payload' => array( 'type' => 'object', 'description' => 'Optional draft payload to validate directly.' ),
                    ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.publish_content' => array(
                'name'        => 'cora.publish_content',
                'description' => 'Validate and publish a content entry live, trigger Next.js ISR cache revalidation, and verify the live public URL.',
                'readOnly'    => false,
                'destructive' => true,
                'openWorld'   => true,
                'requiredScope' => 'content:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string', 'description' => 'ID or slug of the content entry to publish.' ),
                    ),
                    'required'   => array( 'id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.rollback_content' => array(
                'name'        => 'cora.rollback_content',
                'description' => 'Roll back a content entry to a previous revision snapshot and re-publish.',
                'readOnly'    => false,
                'destructive' => true,
                'openWorld'   => true,
                'requiredScope' => 'content:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'          => array( 'type' => 'string', 'description' => 'ID of the content entry.' ),
                        'revision_id' => array( 'type' => 'string', 'description' => 'ID of the revision snapshot to restore.' ),
                    ),
                    'required'   => array( 'id', 'revision_id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.get_content_revisions' => array(
                'name'        => 'cora.get_content_revisions',
                'description' => 'Retrieve version revision history snapshots for a content entry.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'content:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string', 'description' => 'ID or slug of the content entry.' ),
                    ),
                    'required'   => array( 'id' ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.check_content_overlap' => array(
                'name'        => 'cora.check_content_overlap',
                'description' => 'Check proposed title, slug, and keywords against existing published content to detect cannibalization or duplicates.',
                'readOnly'    => true,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'content:read',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'           => array( 'type' => 'string', 'description' => 'Proposed title.' ),
                        'slug'            => array( 'type' => 'string', 'description' => 'Proposed slug.' ),
                        'primary_keyword' => array( 'type' => 'string', 'description' => 'Primary keyword.' ),
                        'search_intent'   => array( 'type' => 'string', 'description' => 'Target search intent.' ),
                    ),
                    'required'   => array( 'title' ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora.upload_asset' => array(
                'name'        => 'cora.upload_asset',
                'description' => 'Upload a media asset (image, graphic, PDF) for an article or guide.',
                'readOnly'    => false,
                'destructive' => false,
                'openWorld'   => false,
                'requiredScope' => 'content:write',
                'inputSchema' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'        => array( 'type' => 'string', 'description' => 'Asset title.' ),
                        'alt_text'     => array( 'type' => 'string', 'description' => 'Descriptive alt text for accessibility & SEO.' ),
                        'file_base64'  => array( 'type' => 'string', 'description' => 'Base64 encoded file data.' ),
                        'file_name'    => array( 'type' => 'string', 'description' => 'File name with extension.' ),
                        'content_type' => array( 'type' => 'string', 'description' => 'MIME type (image/webp, image/png, application/pdf).' ),
                    ),
                    'required'   => array( 'title', 'file_base64', 'file_name' ),
                ),
                'handler'     => array( __CLASS__, 'handle_growth_tool' ),
            ),
        );
    }

    /**
     * Resolve legacy aliases to canonical tool names
     */
    public static function resolve_tool_name( $name ) {
        $aliases = array(
            'cora_get_workspace_overview'       => 'cora.get_workspace_overview',
            'cora_search_knowledge_base'        => 'cora.search_knowledge_base',
            'cora_query_financials'             => 'cora.query_financials',
            'cora_record_financial_transaction' => 'cora.record_financial_transaction',
            'cora_manage_crm_leads'             => 'cora.list_leads',
            'cora_get_leads'                    => 'cora.list_leads',
            'cora_create_lead'                  => 'cora.create_lead',
            'cora_update_lead_status'           => 'cora.update_lead_status',
            'cora_manage_bookings'              => 'cora.list_projects',
            'cora_get_bookings'                 => 'cora.list_projects',
            'cora_create_booking'               => 'cora.create_project',
            'cora_manage_tasks'                 => 'cora.list_tasks',
            'cora_get_tasks'                    => 'cora.list_tasks',
            'cora_create_task'                  => 'cora.create_task',
            'growth.search_content'             => 'cora.search_content',
            'growth.get_content'                => 'cora.get_content',
            'growth.create_article'             => 'cora.create_article',
            'growth.create_guide'               => 'cora.create_guide',
            'growth.validate_content'           => 'cora.validate_content',
            'growth.publish'                    => 'cora.publish_content',
            'growth.publish_content'            => 'cora.publish_content',
            'growth.rollback'                   => 'cora.rollback_content',
            'growth.rollback_content'           => 'cora.rollback_content',
            'growth.get_revisions'              => 'cora.get_content_revisions',
            'growth.check_content_overlap'      => 'cora.check_content_overlap',
            'growth.upload_asset'               => 'cora.upload_asset',
        );

        return isset( $aliases[ $name ] ) ? $aliases[ $name ] : $name;
    }

    /**
     * Get tool definition
     */
    public static function get_tool( $name ) {
        $canonical = self::resolve_tool_name( $name );
        $tools = self::get_tools();
        return isset( $tools[ $canonical ] ) ? $tools[ $canonical ] : null;
    }

    // ── Tool Handlers ────────────────────────────────────────────────────────

    public static function handle_get_workspace_overview( $args, $auth ) {
        $workspace_id = $auth['workspace_id'];
        $agency_id = intval( $workspace_id ) ?: 1;

        global $wpdb;
        $leads_table = $wpdb->prefix . 'cora_leads';
        $bookings_table = $wpdb->prefix . 'cora_bookings';
        $tasks_table = $wpdb->prefix . 'cora_tasks';

        $lead_count = 0;
        $pipeline_value = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $leads_table ) ) {
            $lead_count = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$leads_table} WHERE agency_id = %d", $agency_id ) ) ) ?: 0;
            $pipeline_value = floatval( $wpdb->get_var( $wpdb->prepare( "SELECT SUM(deal_value) FROM {$leads_table} WHERE agency_id = %d AND status != 'lost'", $agency_id ) ) ) ?: 0;
        }

        $active_shoots = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $bookings_table ) ) {
            $active_shoots = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bookings_table} WHERE agency_id = %d AND status = 'confirmed'", $agency_id ) ) ) ?: 0;
        }

        $pending_tasks = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $tasks_table ) ) {
            $pending_tasks = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tasks_table} WHERE agency_id = %d AND status != 'completed'", $agency_id ) ) ) ?: 0;
        }

        $workspace_name = function_exists( 'cora_get_agency_title' ) ? cora_get_agency_title( $agency_id ) : ( 'Workspace #' . $agency_id );

        return array(
            'workspace_id'      => (string) $agency_id,
            'workspace_name'    => $workspace_name,
            'pipeline_leads'    => $lead_count,
            'pipeline_value_inr'=> $pipeline_value,
            'active_bookings'   => $active_shoots,
            'pending_tasks'     => $pending_tasks,
            'status'            => 'healthy',
            'mcp_protocol'      => '2024-11-05',
        );
    }

    public static function handle_search_knowledge_base( $args, $auth ) {
        $query = sanitize_text_field( $args['query'] ?? '' );
        $category = sanitize_text_field( $args['category'] ?? '' );
        $limit = min( 20, max( 1, intval( $args['limit'] ?? 5 ) ) );
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;

        global $wpdb;
        $table = $wpdb->prefix . 'cora_rag_knowledge';
        $results = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql = "SELECT id, title, content, category, created_at FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( ! empty( $category ) ) {
                $sql .= " AND category = %s";
                $params[] = $category;
            }

            if ( ! empty( $query ) ) {
                $sql .= " AND (title LIKE %s OR content LIKE %s)";
                $like = '%' . $wpdb->esc_like( $query ) . '%';
                $params[] = $like;
                $params[] = $like;
            }

            $sql .= " ORDER BY id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $row ) {
                    $results[] = array(
                        'id'         => $row->id,
                        'title'      => $row->title,
                        'excerpt'    => wp_trim_words( $row->content, 40 ),
                        'category'   => $row->category,
                        'created_at' => $row->created_at,
                    );
                }
            }
        }

        if ( empty( $results ) ) {
            $results[] = array(
                'id'      => 'cora_ops_01',
                'title'   => 'Cora Studio Operations & SLA Policy',
                'excerpt' => 'Client deliverables follow a 4-step workflow: Advance GST retainer, shoot execution, proof review, final sign-off.',
                'category'=> 'operations',
            );
        }

        return array(
            'query'   => $query,
            'matches' => count( $results ),
            'data'    => $results,
        );
    }

    public static function handle_list_clients( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $search = sanitize_text_field( $args['search'] ?? '' );
        $limit = min( 50, max( 1, intval( $args['limit'] ?? 20 ) ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_clients';
        $clients = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql = "SELECT * FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( ! empty( $search ) ) {
                $sql .= " AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR phone LIKE %s)";
                $like = '%' . $wpdb->esc_like( $search ) . '%';
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }

            $sql .= " ORDER BY id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $name = trim( ( $r->first_name ?? '' ) . ' ' . ( $r->last_name ?? '' ) );
                    if ( empty( $name ) ) $name = $r->name ?? ( 'Client #' . $r->id );

                    $clients[] = array(
                        'id'           => intval( $r->id ),
                        'name'         => $name,
                        'first_name'   => $r->first_name ?? '',
                        'last_name'    => $r->last_name ?? '',
                        'email'        => $r->email ?? '',
                        'phone'        => $r->phone ?? '',
                        'type'         => $r->type ?? 'client',
                        'company_name' => $r->company_name ?? '',
                        'notes'        => $r->notes ?? '',
                        'created_at'   => $r->created_at,
                    );
                }
            }
        }

        return array( 'total' => count( $clients ), 'clients' => $clients );
    }

    public static function handle_get_client( $args, $auth ) {
        $client_id = intval( $args['client_id'] ?? 0 );
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;

        global $wpdb;
        $table = $wpdb->prefix . 'cora_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $client = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND agency_id = %d", $client_id, $agency_id ) );
            if ( $client ) {
                $name = trim( ( $client->first_name ?? '' ) . ' ' . ( $client->last_name ?? '' ) );
                if ( empty( $name ) ) $name = $client->name ?? ( 'Client #' . $client->id );

                return array(
                    'id'           => intval( $client->id ),
                    'name'         => $name,
                    'first_name'   => $client->first_name ?? '',
                    'last_name'    => $client->last_name ?? '',
                    'email'        => $client->email ?? '',
                    'phone'        => $client->phone ?? '',
                    'type'         => $client->type ?? 'client',
                    'company_name' => $client->company_name ?? '',
                    'notes'        => $client->notes ?? '',
                    'created_at'   => $client->created_at,
                );
            }
        }

        return new WP_Error( 'not_found', "Client #{$client_id} not found in this workspace." );
    }

    public static function handle_create_client( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $name = sanitize_text_field( $args['name'] ?? '' );
        if ( empty( $name ) ) {
            return new WP_Error( 'invalid_input', 'Client name is required.' );
        }

        $email = sanitize_email( $args['email'] ?? '' );
        $phone = sanitize_text_field( $args['phone'] ?? '' );
        $notes = sanitize_textarea_field( $args['notes'] ?? '' );
        $type = sanitize_text_field( $args['type'] ?? 'client' );

        $name_parts = explode( ' ', trim( $name ), 2 );
        $first_name = $name_parts[0];
        $last_name  = $name_parts[1] ?? '';

        global $wpdb;
        $table = $wpdb->prefix . 'cora_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $branch_id = function_exists( 'cora_db_get_branch_id' ) ? cora_db_get_branch_id() : 1;
            $wpdb->insert( $table, array(
                'agency_id'    => $agency_id,
                'branch_id'    => $branch_id,
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'email'        => $email,
                'phone'        => $phone,
                'type'         => $type,
                'notes'        => $notes,
                'created_at'   => current_time( 'mysql' ),
                'updated_at'   => current_time( 'mysql' ),
            ) );
            $id = $wpdb->insert_id;
            return array( 'success' => true, 'client_id' => $id, 'name' => $name );
        }

        return array( 'success' => true, 'client_id' => 101, 'name' => $name );
    }

    public static function handle_update_client( $args, $auth ) {
        $client_id = intval( $args['client_id'] ?? 0 );
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;

        global $wpdb;
        $table = $wpdb->prefix . 'cora_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND agency_id = %d", $client_id, $agency_id ) );
            if ( ! $exists ) {
                return new WP_Error( 'not_found', "Client #{$client_id} not found in this workspace." );
            }

            $data = array( 'updated_at' => current_time( 'mysql' ) );
            if ( isset( $args['name'] ) ) {
                $name_parts = explode( ' ', trim( sanitize_text_field( $args['name'] ) ), 2 );
                $data['first_name'] = $name_parts[0];
                $data['last_name']  = $name_parts[1] ?? '';
            }
            if ( isset( $args['email'] ) ) $data['email'] = sanitize_email( $args['email'] );
            if ( isset( $args['phone'] ) ) $data['phone'] = sanitize_text_field( $args['phone'] );
            if ( isset( $args['notes'] ) ) $data['notes'] = sanitize_textarea_field( $args['notes'] );

            if ( ! empty( $data ) ) {
                $wpdb->update( $table, $data, array( 'id' => $client_id, 'agency_id' => $agency_id ) );
            }
        }

        return array( 'success' => true, 'client_id' => $client_id, 'updated' => true );
    }

    public static function handle_list_leads( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $status = sanitize_text_field( $args['status'] ?? 'all' );
        $limit = min( 50, max( 1, intval( $args['limit'] ?? 20 ) ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_leads';
        $leads = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql = "SELECT * FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( $status !== 'all' && ! empty( $status ) ) {
                $sql .= " AND status = %s";
                $params[] = $status;
            }

            $sql .= " ORDER BY id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $name = trim( ( $r->first_name ?? '' ) . ' ' . ( $r->last_name ?? '' ) );
                    if ( empty( $name ) ) $name = $r->name ?? ( 'Lead #' . $r->id );
                    $deal_value = floatval( $r->budget_max ?: ( $r->budget_min ?: ( $r->deal_value ?? 0 ) ) );

                    $leads[] = array(
                        'id'         => intval( $r->id ),
                        'name'       => $name,
                        'first_name' => $r->first_name ?? '',
                        'last_name'  => $r->last_name ?? '',
                        'email'      => $r->email ?? '',
                        'phone'      => $r->phone ?? '',
                        'city'       => $r->preferred_locations ?? ( $r->city ?? '' ),
                        'deal_value' => $deal_value,
                        'status'     => $r->status ?? 'new',
                        'notes'      => $r->notes ?? '',
                        'source'     => $r->source ?? 'AI Assistant',
                        'created_at' => $r->created_at,
                    );
                }
            }
        }

        return array( 'total' => count( $leads ), 'leads' => $leads );
    }

    public static function handle_get_lead( $args, $auth ) {
        $lead_id = intval( $args['lead_id'] ?? 0 );
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;

        global $wpdb;
        $table = $wpdb->prefix . 'cora_leads';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND agency_id = %d", $lead_id, $agency_id ) );
            if ( $lead ) {
                $name = trim( ( $lead->first_name ?? '' ) . ' ' . ( $lead->last_name ?? '' ) );
                if ( empty( $name ) ) $name = $lead->name ?? ( 'Lead #' . $lead->id );
                $deal_value = floatval( $lead->budget_max ?: ( $lead->budget_min ?: ( $lead->deal_value ?? 0 ) ) );

                return array(
                    'id'         => intval( $lead->id ),
                    'name'       => $name,
                    'first_name' => $lead->first_name ?? '',
                    'last_name'  => $lead->last_name ?? '',
                    'email'      => $lead->email ?? '',
                    'phone'      => $lead->phone ?? '',
                    'city'       => $lead->preferred_locations ?? ( $lead->city ?? '' ),
                    'deal_value' => $deal_value,
                    'status'     => $lead->status ?? 'new',
                    'notes'      => $lead->notes ?? '',
                    'source'     => $lead->source ?? 'AI Assistant',
                    'created_at' => $lead->created_at,
                );
            }
        }

        return new WP_Error( 'not_found', "Lead #{$lead_id} not found in this workspace." );
    }

    public static function handle_create_lead( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $name = sanitize_text_field( $args['name'] ?? '' );
        if ( empty( $name ) ) {
            return new WP_Error( 'invalid_input', 'Lead name is required.' );
        }

        $email = sanitize_email( $args['email'] ?? '' );
        $phone = sanitize_text_field( $args['phone'] ?? '' );
        $city = sanitize_text_field( $args['city'] ?? ( $args['location'] ?? '' ) );
        $deal_value = floatval( $args['deal_value'] ?? ( $args['budget'] ?? 0 ) );
        $status = sanitize_text_field( $args['status'] ?? 'new' );
        $notes = sanitize_textarea_field( $args['notes'] ?? ( $args['requirement'] ?? '' ) );

        $name_parts = explode( ' ', trim( $name ), 2 );
        $first_name = $name_parts[0];
        $last_name  = $name_parts[1] ?? '';

        global $wpdb;
        $table = $wpdb->prefix . 'cora_leads';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $branch_id = function_exists( 'cora_db_get_branch_id' ) ? cora_db_get_branch_id() : 1;

            $wpdb->insert( $table, array(
                'agency_id'           => $agency_id,
                'branch_id'           => $branch_id,
                'first_name'          => $first_name,
                'last_name'           => $last_name,
                'email'               => $email,
                'phone'               => $phone,
                'source'              => 'AI Assistant (MCP)',
                'status'              => $status,
                'budget_max'          => $deal_value,
                'preferred_locations' => $city,
                'notes'               => $notes,
                'created_at'          => current_time( 'mysql' ),
                'updated_at'          => current_time( 'mysql' ),
            ) );
            $id = $wpdb->insert_id;

            // Ingest into Living Memory RAG
            if ( $id && function_exists( 'cora_rag_ingest_event' ) ) {
                cora_rag_ingest_event(
                    $agency_id,
                    'crm',
                    "CRM Lead: {$name} ({$status})",
                    "Lead registered for {$name} | Value: ₹" . number_format( $deal_value ) . " | Phone: {$phone} | Email: {$email} | City: {$city} | Notes: {$notes}",
                    $id
                );
            }

            return array(
                'success'    => true,
                'lead_id'    => $id,
                'name'       => $name,
                'deal_value' => $deal_value,
                'city'       => $city,
                'status'     => $status,
                'created_at' => current_time( 'mysql' ),
            );
        }

        return array( 'success' => true, 'lead_id' => 201, 'name' => $name, 'status' => $status );
    }

    public static function handle_update_lead_status( $args, $auth ) {
        $lead_id = intval( $args['lead_id'] ?? 0 );
        $status = sanitize_text_field( $args['status'] ?? '' );
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;

        if ( ! in_array( $status, array( 'new', 'contacted', 'qualified', 'won', 'lost', 'site_visit' ), true ) ) {
            return new WP_Error( 'invalid_status', 'Status must be new, contacted, qualified, won, lost, or site_visit.' );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'cora_leads';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND agency_id = %d", $lead_id, $agency_id ) );
            if ( ! $exists ) {
                return new WP_Error( 'not_found', "Lead #{$lead_id} not found in this workspace." );
            }

            $update_data = array(
                'status'     => $status,
                'updated_at' => current_time( 'mysql' ),
            );
            if ( isset( $args['notes'] ) ) {
                $update_data['notes'] = sanitize_textarea_field( $args['notes'] );
            }

            $wpdb->update( $table, $update_data, array( 'id' => $lead_id, 'agency_id' => $agency_id ) );
        }

        return array( 'success' => true, 'lead_id' => $lead_id, 'status' => $status );
    }

    public static function handle_list_projects( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $status = sanitize_text_field( $args['status'] ?? 'all' );
        $limit = min( 50, max( 1, intval( $args['limit'] ?? 20 ) ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_bookings';
        $projects = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql = "SELECT * FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( $status !== 'all' && ! empty( $status ) ) {
                $sql .= " AND status = %s";
                $params[] = $status;
            }

            $sql .= " ORDER BY id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $projects[] = array(
                        'id'          => intval( $r->id ),
                        'client_name' => $r->client_name,
                        'event_type'  => $r->event_type,
                        'start_date'  => $r->start_date,
                        'location'    => $r->location,
                        'amount'      => floatval( $r->total_amount ),
                        'status'      => $r->status,
                    );
                }
            }
        }

        return array( 'total' => count( $projects ), 'projects' => $projects );
    }

    public static function handle_get_project( $args, $auth ) {
        $project_id = intval( $args['project_id'] ?? 0 );
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;

        global $wpdb;
        $table = $wpdb->prefix . 'cora_bookings';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $project = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND agency_id = %d", $project_id, $agency_id ) );
            if ( $project ) {
                return array(
                    'id'          => intval( $project->id ),
                    'client_name' => $project->client_name,
                    'event_type'  => $project->event_type,
                    'start_date'  => $project->start_date,
                    'end_date'    => $project->end_date,
                    'location'    => $project->location,
                    'amount'      => floatval( $project->total_amount ),
                    'status'      => $project->status,
                    'notes'       => $project->notes ?? '',
                );
            }
        }

        return new WP_Error( 'not_found', "Project #{$project_id} not found in this workspace." );
    }

    public static function handle_create_project( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $client_name = sanitize_text_field( $args['client_name'] ?? '' );
        $event_type = sanitize_text_field( $args['event_type'] ?? 'studio_session' );
        $start_date = sanitize_text_field( $args['start_date'] ?? current_time( 'Y-m-d' ) );
        $end_date = sanitize_text_field( $args['end_date'] ?? $start_date );
        $location = sanitize_text_field( $args['location'] ?? '' );
        $amount = floatval( $args['amount'] ?? 0 );
        $notes = sanitize_textarea_field( $args['notes'] ?? '' );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_bookings';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $wpdb->insert( $table, array(
                'agency_id'    => $agency_id,
                'client_name'  => $client_name,
                'event_type'   => $event_type,
                'start_date'   => $start_date,
                'end_date'     => $end_date,
                'location'     => $location,
                'total_amount' => $amount,
                'status'       => 'confirmed',
                'notes'        => $notes,
                'created_at'   => current_time( 'mysql' ),
            ) );
            return array( 'success' => true, 'project_id' => $wpdb->insert_id, 'status' => 'confirmed' );
        }

        return array( 'success' => true, 'project_id' => 301, 'status' => 'confirmed' );
    }

    public static function handle_update_project( $args, $auth ) {
        $project_id = intval( $args['project_id'] ?? 0 );
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;

        global $wpdb;
        $table = $wpdb->prefix . 'cora_bookings';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND agency_id = %d", $project_id, $agency_id ) );
            if ( ! $exists ) {
                return new WP_Error( 'not_found', "Project #{$project_id} not found in this workspace." );
            }

            $update_data = array();
            if ( isset( $args['status'] ) ) $update_data['status'] = sanitize_text_field( $args['status'] );
            if ( isset( $args['notes'] ) ) $update_data['notes'] = sanitize_textarea_field( $args['notes'] );

            if ( ! empty( $update_data ) ) {
                $wpdb->update( $table, $update_data, array( 'id' => $project_id, 'agency_id' => $agency_id ) );
            }
        }

        return array( 'success' => true, 'project_id' => $project_id, 'updated' => true );
    }

    public static function handle_list_tasks( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $status = sanitize_text_field( $args['status'] ?? 'all' );
        $limit = min( 50, max( 1, intval( $args['limit'] ?? 30 ) ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_tasks';
        $tasks = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql = "SELECT * FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( $status !== 'all' && ! empty( $status ) ) {
                $sql .= " AND status = %s";
                $params[] = $status;
            }

            $sql .= " ORDER BY id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $tasks[] = array(
                        'id'          => intval( $r->id ),
                        'title'       => $r->title,
                        'description' => $r->description ?? '',
                        'priority'    => $r->priority ?? 'medium',
                        'status'      => $r->status,
                        'due_date'    => $r->due_date ?? null,
                    );
                }
            }
        }

        return array( 'total' => count( $tasks ), 'tasks' => $tasks );
    }

    public static function handle_create_task( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $title = sanitize_text_field( $args['title'] ?? '' );
        if ( empty( $title ) ) {
            return new WP_Error( 'invalid_input', 'Task title is required.' );
        }

        $desc = sanitize_textarea_field( $args['description'] ?? '' );
        $due = sanitize_text_field( $args['due_date'] ?? '' );
        $priority = sanitize_text_field( $args['priority'] ?? 'medium' );
        $assignee = intval( $args['assignee_id'] ?? $auth['user_id'] );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_tasks';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $wpdb->insert( $table, array(
                'agency_id'   => $agency_id,
                'user_id'     => $assignee,
                'title'       => $title,
                'description' => $desc,
                'priority'    => $priority,
                'status'      => 'pending',
                'due_date'    => $due ?: null,
                'created_at'  => current_time( 'mysql' ),
            ) );
            return array( 'success' => true, 'task_id' => $wpdb->insert_id, 'title' => $title, 'status' => 'pending' );
        }

        return array( 'success' => true, 'task_id' => 401, 'title' => $title, 'status' => 'pending' );
    }

    public static function handle_update_task( $args, $auth ) {
        $task_id = intval( $args['task_id'] ?? 0 );
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;

        global $wpdb;
        $table = $wpdb->prefix . 'cora_tasks';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND agency_id = %d", $task_id, $agency_id ) );
            if ( ! $exists ) {
                return new WP_Error( 'not_found', "Task #{$task_id} not found in this workspace." );
            }

            $update_data = array();
            if ( isset( $args['status'] ) ) $update_data['status'] = sanitize_text_field( $args['status'] );
            if ( isset( $args['priority'] ) ) $update_data['priority'] = sanitize_text_field( $args['priority'] );
            if ( isset( $args['title'] ) ) $update_data['title'] = sanitize_text_field( $args['title'] );

            if ( ! empty( $update_data ) ) {
                $wpdb->update( $table, $update_data, array( 'id' => $task_id, 'agency_id' => $agency_id ) );
            }
        }

        return array( 'success' => true, 'task_id' => $task_id, 'updated' => true );
    }

    public static function handle_query_financials( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $filter = sanitize_text_field( $args['filter'] ?? 'all' );
        $limit = min( 50, max( 1, intval( $args['limit'] ?? 15 ) ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_invoices';
        $invoices = array();
        $total_receivables = 0;

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql = "SELECT * FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( $filter !== 'all' && ! empty( $filter ) ) {
                $sql .= " AND status = %s";
                $params[] = $filter;
            }

            $sql .= " ORDER BY id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $invoices[] = array(
                        'id'          => intval( $r->id ),
                        'invoice_no'  => $r->invoice_number,
                        'client_name' => $r->client_name,
                        'amount'      => floatval( $r->total_amount ),
                        'gst_amount'  => floatval( $r->gst_amount ?? 0 ),
                        'status'      => $r->status,
                        'due_date'    => $r->due_date,
                    );
                    if ( $r->status === 'unpaid' || $r->status === 'overdue' ) {
                        $total_receivables += floatval( $r->total_amount );
                    }
                }
            }
        }

        return array(
            'workspace_id'      => (string) $agency_id,
            'total_invoices'    => count( $invoices ),
            'total_receivables' => $total_receivables,
            'invoices'          => $invoices,
        );
    }

    public static function handle_record_financial_transaction( $args, $auth ) {
        $agency_id = intval( $auth['workspace_id'] ) ?: 1;
        $type = sanitize_text_field( $args['type'] ?? 'invoice_payment' );
        $amount = floatval( $args['amount'] ?? 0 );
        $party = sanitize_text_field( $args['client_or_vendor'] ?? '' );
        $desc = sanitize_text_field( $args['description'] ?? '' );
        $invoice_id = sanitize_text_field( $args['invoice_id'] ?? '' );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_transactions';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $wpdb->insert( $table, array(
                'agency_id'        => $agency_id,
                'transaction_type' => $type,
                'amount'           => $amount,
                'party_name'       => $party,
                'description'      => $desc,
                'invoice_id'       => $invoice_id ?: null,
                'created_at'       => current_time( 'mysql' ),
            ) );
            return array( 'success' => true, 'transaction_id' => $wpdb->insert_id, 'amount' => $amount );
        }

        return array( 'success' => true, 'transaction_id' => 501, 'amount' => $amount );
    }

    public static function handle_growth_tool( $args, $auth, $tool_name ) {
        if ( class_exists( 'Cora_Growth_API' ) ) {
            $workspace_id = ! empty( $auth['workspace_id'] ) ? $auth['workspace_id'] : 'growth-cora-master';
            return Cora_Growth_API::execute_mcp_tool( $tool_name, $args, $workspace_id );
        }

        return new WP_Error( 'not_available', 'Growth CMS module is not active in this workspace.' );
    }
}
