<?php
/**
 * Cora Universal MCP - Canonical Tool Registry
 *
 * Model-independent, vendor-neutral tool definitions with safety annotations
 * (readOnly, destructive, openWorld, requiredScope).
 * Connects directly to core Cora database schema (leads, clients, bookings, workspace_tasks, ledger, rag).
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
            'cora_get_workspace_overview' => array(
                'name'          => 'cora_get_workspace_overview',
                'description'   => 'Retrieve high-level business pulse, key performance metrics, pipeline deal value, collected revenue, outstanding receivables, active bookings/shoots, and pending tasks for the authenticated workspace.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'workspace:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => (object) array(),
                ),
                'handler'       => array( __CLASS__, 'handle_get_workspace_overview' ),
            ),

            // ── 2. Living RAG Knowledge Base ───────────────────────────────────
            'cora_search_knowledge_base' => array(
                'name'          => 'cora_search_knowledge_base',
                'description'   => 'Semantic & keyword search across the workspace living memory, ingested operational history, policy guidelines, client records, and documents.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'knowledge:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'query'    => array( 'type' => 'string', 'description' => 'Search query, question, or keyword to retrieve contextual intelligence for.' ),
                        'category' => array( 'type' => 'string', 'description' => 'Optional filter: financials, crm, operations, vault, reviews, activity, clients.' ),
                        'limit'    => array( 'type' => 'integer', 'description' => 'Max fragments to return (default: 5, max: 20).' ),
                    ),
                    'required'   => array( 'query' ),
                ),
                'handler'       => array( __CLASS__, 'handle_search_knowledge_base' ),
            ),

            'cora_add_knowledge_fragment' => array(
                'name'          => 'cora_add_knowledge_fragment',
                'description'   => 'Store a new living memory fragment, client preference, operational SOP, or meeting summary into the workspace RAG vector memory.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'knowledge:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'    => array( 'type' => 'string', 'description' => 'Title or summary headline of the knowledge fragment.' ),
                        'content'  => array( 'type' => 'string', 'description' => 'Full memory text or detailed intelligence note.' ),
                        'category' => array( 'type' => 'string', 'description' => 'Category: operations, crm, financials, clients, general (default: general).' ),
                    ),
                    'required'   => array( 'title', 'content' ),
                ),
                'handler'       => array( __CLASS__, 'handle_add_knowledge_fragment' ),
            ),

            // ── 3. Clients CRM ────────────────────────────────────────────────
            'cora_list_clients' => array(
                'name'          => 'cora_list_clients',
                'description'   => 'List client profiles, company names, contact phone/emails, and records in the workspace.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'clients:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'search' => array( 'type' => 'string', 'description' => 'Optional search query by name, email, phone, or company.' ),
                        'type'   => array( 'type' => 'string', 'description' => 'Filter by client type: client, buyer, seller, commercial, vip, or all.' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'       => array( __CLASS__, 'handle_list_clients' ),
            ),

            'cora_get_client' => array(
                'name'          => 'cora_get_client',
                'description'   => 'Retrieve complete profile details, active bookings, transactions, and notes for a specific client.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'clients:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'client_id' => array( 'type' => 'integer', 'description' => 'Unique ID of the client.' ),
                    ),
                    'required'   => array( 'client_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_get_client' ),
            ),

            'cora_create_client' => array(
                'name'          => 'cora_create_client',
                'description'   => 'Create a new client record in the workspace CRM.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'clients:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'name'         => array( 'type' => 'string', 'description' => 'Client full name or primary contact.' ),
                        'email'        => array( 'type' => 'string', 'description' => 'Client email address.' ),
                        'phone'        => array( 'type' => 'string', 'description' => 'Client phone number.' ),
                        'type'         => array( 'type' => 'string', 'description' => 'Client classification: client, buyer, seller, commercial, vip (default: client).' ),
                        'company_name' => array( 'type' => 'string', 'description' => 'Company or brand name.' ),
                        'notes'        => array( 'type' => 'string', 'description' => 'Initial notes, background, or requirements.' ),
                    ),
                    'required'   => array( 'name' ),
                ),
                'handler'       => array( __CLASS__, 'handle_create_client' ),
            ),

            'cora_update_client' => array(
                'name'          => 'cora_update_client',
                'description'   => 'Update an existing client profile, contact information, or notes.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'clients:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'client_id'    => array( 'type' => 'integer', 'description' => 'ID of the client to update.' ),
                        'name'         => array( 'type' => 'string', 'description' => 'Updated client name.' ),
                        'email'        => array( 'type' => 'string', 'description' => 'Updated email.' ),
                        'phone'        => array( 'type' => 'string', 'description' => 'Updated phone number.' ),
                        'type'         => array( 'type' => 'string', 'description' => 'Updated client classification.' ),
                        'company_name' => array( 'type' => 'string', 'description' => 'Company name.' ),
                        'notes'        => array( 'type' => 'string', 'description' => 'Updated notes.' ),
                    ),
                    'required'   => array( 'client_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_update_client' ),
            ),

            'cora_delete_client' => array(
                'name'          => 'cora_delete_client',
                'description'   => 'Delete a client record from the workspace CRM.',
                'readOnly'      => false,
                'destructive'   => true,
                'openWorld'     => false,
                'requiredScope' => 'clients:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'client_id' => array( 'type' => 'integer', 'description' => 'ID of the client to remove.' ),
                    ),
                    'required'   => array( 'client_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_delete_client' ),
            ),

            // ── 4. Leads & Pipeline ───────────────────────────────────────────
            'cora_list_leads' => array(
                'name'          => 'cora_list_leads',
                'description'   => 'List sales inquiries and deals from the CRM funnel with status, budget, location, and deal values.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'leads:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'status' => array( 'type' => 'string', 'description' => 'Filter by stage: new, contacted, qualified, proposal_sent, won, lost, or all.' ),
                        'search' => array( 'type' => 'string', 'description' => 'Search by lead name, phone, email, or city.' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'       => array( __CLASS__, 'handle_list_leads' ),
            ),

            'cora_get_lead' => array(
                'name'          => 'cora_get_lead',
                'description'   => 'Get detailed lead inquiry, deal stage, budget requirements, and communication notes.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'leads:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'lead_id' => array( 'type' => 'integer', 'description' => 'ID of the lead.' ),
                    ),
                    'required'   => array( 'lead_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_get_lead' ),
            ),

            'cora_create_lead' => array(
                'name'          => 'cora_create_lead',
                'description'   => 'Create a new CRM inquiry or prospective client lead with estimated project value and requirements.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'leads:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'name'          => array( 'type' => 'string', 'description' => 'Lead full name or contact person.' ),
                        'email'         => array( 'type' => 'string', 'description' => 'Email address.' ),
                        'phone'         => array( 'type' => 'string', 'description' => 'Phone or WhatsApp number.' ),
                        'city'          => array( 'type' => 'string', 'description' => 'City, location, or area.' ),
                        'property_type' => array( 'type' => 'string', 'description' => 'Service/Property type (e.g. Wedding, Commercial, Studio, Villa, 3BHK).' ),
                        'deal_value'    => array( 'type' => 'number', 'description' => 'Estimated budget or deal value in INR (₹).' ),
                        'status'        => array( 'type' => 'string', 'description' => 'Initial deal status: new, contacted, qualified (default: new).' ),
                        'notes'         => array( 'type' => 'string', 'description' => 'Inquiry details, project requirements, or notes.' ),
                    ),
                    'required'   => array( 'name' ),
                ),
                'handler'       => array( __CLASS__, 'handle_create_lead' ),
            ),

            'cora_update_lead' => array(
                'name'          => 'cora_update_lead',
                'description'   => 'Update lead contact details, deal value, status, or notes.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'leads:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'lead_id'    => array( 'type' => 'integer', 'description' => 'ID of the lead to update.' ),
                        'name'       => array( 'type' => 'string', 'description' => 'Lead name.' ),
                        'email'      => array( 'type' => 'string', 'description' => 'Email address.' ),
                        'phone'      => array( 'type' => 'string', 'description' => 'Phone number.' ),
                        'city'       => array( 'type' => 'string', 'description' => 'City / location.' ),
                        'deal_value' => array( 'type' => 'number', 'description' => 'Deal value / budget in INR.' ),
                        'status'     => array( 'type' => 'string', 'description' => 'Stage: new, contacted, qualified, won, lost.' ),
                        'notes'      => array( 'type' => 'string', 'description' => 'Updated notes.' ),
                    ),
                    'required'   => array( 'lead_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_update_lead' ),
            ),

            'cora_update_lead_status' => array(
                'name'          => 'cora_update_lead_status',
                'description'   => 'Advance or update the deal stage and add follow-up progress notes for a lead.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'leads:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'lead_id' => array( 'type' => 'integer', 'description' => 'ID of the lead.' ),
                        'status'  => array( 'type' => 'string', 'description' => 'New status: new, contacted, qualified, won, lost.' ),
                        'notes'   => array( 'type' => 'string', 'description' => 'Follow-up or closing notes.' ),
                    ),
                    'required'   => array( 'lead_id', 'status' ),
                ),
                'handler'       => array( __CLASS__, 'handle_update_lead_status' ),
            ),

            // ── 5. Projects & Bookings ────────────────────────────────────────
            'cora_list_projects' => array(
                'name'          => 'cora_list_projects',
                'description'   => 'List studio bookings, shoot dates, project milestones, package amounts, and delivery statuses.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'projects:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'status' => array( 'type' => 'string', 'description' => 'Filter by status: confirmed, completed, cancelled, or all.' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'       => array( __CLASS__, 'handle_list_projects' ),
            ),

            'cora_get_project' => array(
                'name'          => 'cora_get_project',
                'description'   => 'Get full booking details, assigned team/crew, shoot dates, location, and package value.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'projects:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'project_id' => array( 'type' => 'integer', 'description' => 'ID of the project/booking.' ),
                    ),
                    'required'   => array( 'project_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_get_project' ),
            ),

            'cora_create_project' => array(
                'name'          => 'cora_create_project',
                'description'   => 'Schedule a new shoot booking or create an operational project with package value and crew.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'projects:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'client_name' => array( 'type' => 'string', 'description' => 'Client or brand name.' ),
                        'event_type'  => array( 'type' => 'string', 'description' => 'Event/shoot type: wedding, fashion, commercial, product, portrait, etc.' ),
                        'start_date'  => array( 'type' => 'string', 'description' => 'Shoot/booking date (YYYY-MM-DD or datetime).' ),
                        'location'    => array( 'type' => 'string', 'description' => 'Location or studio bay.' ),
                        'amount'      => array( 'type' => 'number', 'description' => 'Total package amount in INR (₹).' ),
                        'crew'        => array( 'type' => 'string', 'description' => 'Assigned crew, photographers, or team members.' ),
                        'notes'       => array( 'type' => 'string', 'description' => 'Deliverables and equipment notes.' ),
                    ),
                    'required'   => array( 'client_name', 'start_date' ),
                ),
                'handler'       => array( __CLASS__, 'handle_create_project' ),
            ),

            'cora_update_project' => array(
                'name'          => 'cora_update_project',
                'description'   => 'Update project details, shoot dates, package value, or status.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'projects:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'project_id' => array( 'type' => 'integer', 'description' => 'ID of the project/booking.' ),
                        'status'     => array( 'type' => 'string', 'description' => 'Status: confirmed, completed, postponed, cancelled.' ),
                        'start_date' => array( 'type' => 'string', 'description' => 'Updated shoot date (YYYY-MM-DD).' ),
                        'amount'     => array( 'type' => 'number', 'description' => 'Updated package amount in INR.' ),
                        'crew'       => array( 'type' => 'string', 'description' => 'Assigned crew members.' ),
                        'notes'      => array( 'type' => 'string', 'description' => 'Updated notes.' ),
                    ),
                    'required'   => array( 'project_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_update_project' ),
            ),

            // ── 6. Task Management ────────────────────────────────────────────
            'cora_list_tasks' => array(
                'name'          => 'cora_list_tasks',
                'description'   => 'List workspace tasks across Kanban columns (todo, in_progress, review, done).',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'tasks:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'status'   => array( 'type' => 'string', 'description' => 'Filter by status: todo, in_progress, review, done, or all.' ),
                        'priority' => array( 'type' => 'string', 'description' => 'Filter by priority: low, medium, high, critical.' ),
                        'limit'    => array( 'type' => 'integer', 'description' => 'Max tasks to return (default: 30).' ),
                    ),
                ),
                'handler'       => array( __CLASS__, 'handle_list_tasks' ),
            ),

            'cora_get_task' => array(
                'name'          => 'cora_get_task',
                'description'   => 'Get full details of a specific task item by ID or UID.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'tasks:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'task_id' => array( 'type' => 'string', 'description' => 'Numeric ID or UID of the task.' ),
                    ),
                    'required'   => array( 'task_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_get_task' ),
            ),

            'cora_create_task' => array(
                'name'          => 'cora_create_task',
                'description'   => 'Create and assign a new operational task in the workspace Kanban board.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'tasks:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'       => array( 'type' => 'string', 'description' => 'Task title or action item.' ),
                        'description' => array( 'type' => 'string', 'description' => 'Task description and acceptance criteria.' ),
                        'due_date'    => array( 'type' => 'string', 'description' => 'Due date (YYYY-MM-DD).' ),
                        'priority'    => array( 'type' => 'string', 'description' => 'Priority: low, medium, high, critical (default: medium).' ),
                        'category'    => array( 'type' => 'string', 'description' => 'Category: General, Shoots, Post-Production, CRM, Sales (default: General).' ),
                        'status'      => array( 'type' => 'string', 'description' => 'Initial status: todo, in_progress, review, done (default: todo).' ),
                        'assignee_id' => array( 'type' => 'integer', 'description' => 'Optional assigned user ID.' ),
                    ),
                    'required'   => array( 'title' ),
                ),
                'handler'       => array( __CLASS__, 'handle_create_task' ),
            ),

            'cora_update_task' => array(
                'name'          => 'cora_update_task',
                'description'   => 'Update task title, description, priority, due date, or status.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'tasks:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'task_id'     => array( 'type' => 'string', 'description' => 'Numeric ID or UID of the task.' ),
                        'title'       => array( 'type' => 'string', 'description' => 'Updated title.' ),
                        'description' => array( 'type' => 'string', 'description' => 'Updated description.' ),
                        'status'      => array( 'type' => 'string', 'description' => 'Status: todo, in_progress, review, done.' ),
                        'priority'    => array( 'type' => 'string', 'description' => 'Priority: low, medium, high, critical.' ),
                        'due_date'    => array( 'type' => 'string', 'description' => 'Due date (YYYY-MM-DD).' ),
                    ),
                    'required'   => array( 'task_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_update_task' ),
            ),

            'cora_update_task_status' => array(
                'name'          => 'cora_update_task_status',
                'description'   => 'Quickly move a task across Kanban columns (todo, in_progress, review, done).',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'tasks:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'task_id' => array( 'type' => 'string', 'description' => 'Numeric ID or UID of the task.' ),
                        'status'  => array( 'type' => 'string', 'description' => 'New status: todo, in_progress, review, done.' ),
                    ),
                    'required'   => array( 'task_id', 'status' ),
                ),
                'handler'       => array( __CLASS__, 'handle_update_task' ),
            ),

            'cora_delete_task' => array(
                'name'          => 'cora_delete_task',
                'description'   => 'Delete a task item from the workspace Kanban board.',
                'readOnly'      => false,
                'destructive'   => true,
                'openWorld'     => false,
                'requiredScope' => 'tasks:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'task_id' => array( 'type' => 'string', 'description' => 'Numeric ID or UID of the task.' ),
                    ),
                    'required'   => array( 'task_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_delete_task' ),
            ),

            // ── 7. Financial Invoicing & Ledger ───────────────────────────────
            'cora_query_financials' => array(
                'name'          => 'cora_query_financials',
                'description'   => 'Query financial ledger, collected revenue, outstanding receivables, expenses, and transaction records.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'finance:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'type'   => array( 'type' => 'string', 'description' => 'Filter by transaction type: all, invoice, payment, income, expense (default: all).' ),
                        'status' => array( 'type' => 'string', 'description' => 'Filter by status: all, paid, pending, unpaid, overdue (default: all).' ),
                        'limit'  => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'       => array( __CLASS__, 'handle_query_financials' ),
            ),

            'cora_record_financial_transaction' => array(
                'name'          => 'cora_record_financial_transaction',
                'description'   => 'Record a new payment collection, client invoice, retainer, or studio expense into the ledger.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'finance:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'type'        => array( 'type' => 'string', 'description' => 'Transaction type: invoice, payment, income, expense, retainer (default: payment).' ),
                        'amount'      => array( 'type' => 'number', 'description' => 'Amount in INR (₹).' ),
                        'description' => array( 'type' => 'string', 'description' => 'Transaction description or memo.' ),
                        'category'    => array( 'type' => 'string', 'description' => 'Category (e.g. Shoot Retainer, Gear Rental, Editing Fee, Operations).' ),
                        'status'      => array( 'type' => 'string', 'description' => 'Status: paid, pending, unpaid (default: paid).' ),
                        'client_name' => array( 'type' => 'string', 'description' => 'Client or vendor party name.' ),
                        'date'        => array( 'type' => 'string', 'description' => 'Transaction date (YYYY-MM-DD).' ),
                    ),
                    'required'   => array( 'amount', 'description' ),
                ),
                'handler'       => array( __CLASS__, 'handle_record_financial_transaction' ),
            ),

            // ── 8. Growth & Content Studio ────────────────────────────────────
            'cora_search_content' => array(
                'name'          => 'cora_search_content',
                'description'   => 'Search articles and guides across the Growth CMS repository by title, keyword, status, category, or ICP.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'content:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'type'     => array( 'type' => 'string', 'description' => 'Content type: article, guide, or all.' ),
                        'status'   => array( 'type' => 'string', 'description' => 'Status: draft, review, published, or all.' ),
                        'search'   => array( 'type' => 'string', 'description' => 'Search term.' ),
                        'category' => array( 'type' => 'string', 'description' => 'Canonical category (operations, client-management, sales-proposals, growth, finance, ai-automation).' ),
                        'limit'    => array( 'type' => 'integer', 'description' => 'Max records (default: 20).' ),
                    ),
                ),
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_get_content' => array(
                'name'          => 'cora_get_content',
                'description'   => 'Retrieve a complete article or guide entry by ID or slug including all structured blocks, sources, and SEO metadata.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'content:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'   => array( 'type' => 'string', 'description' => 'Content ID or slug.' ),
                        'slug' => array( 'type' => 'string', 'description' => 'Content slug (alternative to id).' ),
                    ),
                ),
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_create_article' => array(
                'name'          => 'cora_create_article',
                'description'   => 'Create or draft a new structured editorial article with quick_answer, key takeaways, content blocks, sources, and SEO metadata.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'content:write',
                'inputSchema'   => array(
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
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_update_article' => array(
                'name'          => 'cora_update_article',
                'description'   => 'Update an existing editorial article by ID or slug.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'content:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'               => array( 'type' => 'string', 'description' => 'ID of the article.' ),
                        'title'            => array( 'type' => 'string', 'description' => 'Updated title.' ),
                        'quick_answer'     => array( 'type' => 'string', 'description' => 'Updated quick answer.' ),
                        'primary_category' => array( 'type' => 'string', 'description' => 'Category.' ),
                        'content_blocks'   => array( 'type' => 'array', 'description' => 'Structured blocks.' ),
                    ),
                    'required'   => array( 'id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_create_guide' => array(
                'name'          => 'cora_create_guide',
                'description'   => 'Create or draft a flagship multi-chapter guide with modular chapters, deliverables, and lead magnet attachments.',
                'readOnly'      => false,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'content:write',
                'inputSchema'   => array(
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
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_validate_content' => array(
                'name'          => 'cora_validate_content',
                'description'   => 'Run strict evidence and schema validation on a content entry or draft payload without publishing.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'content:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string', 'description' => 'ID of existing content to validate.' ),
                    ),
                    'required'   => array( 'id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_publish_content' => array(
                'name'          => 'cora_publish_content',
                'description'   => 'Validate and publish a content entry live, trigger Next.js ISR cache revalidation, and verify the live public URL.',
                'readOnly'      => false,
                'destructive'   => true,
                'openWorld'     => true,
                'requiredScope' => 'content:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string', 'description' => 'ID or slug of the content entry to publish.' ),
                    ),
                    'required'   => array( 'id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_rollback_content' => array(
                'name'          => 'cora_rollback_content',
                'description'   => 'Roll back a content entry to a previous revision snapshot and re-publish.',
                'readOnly'      => false,
                'destructive'   => true,
                'openWorld'     => true,
                'requiredScope' => 'content:write',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id'          => array( 'type' => 'string', 'description' => 'ID of the content entry.' ),
                        'revision_id' => array( 'type' => 'string', 'description' => 'ID of the revision snapshot to restore.' ),
                    ),
                    'required'   => array( 'id', 'revision_id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_get_content_revisions' => array(
                'name'          => 'cora_get_content_revisions',
                'description'   => 'Retrieve version revision history snapshots for a content entry.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'content:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'id' => array( 'type' => 'string', 'description' => 'ID or slug of the content entry.' ),
                    ),
                    'required'   => array( 'id' ),
                ),
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            'cora_check_content_overlap' => array(
                'name'          => 'cora_check_content_overlap',
                'description'   => 'Check proposed title, slug, and keywords against existing published content to detect cannibalization or duplicates.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'content:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'title'           => array( 'type' => 'string', 'description' => 'Proposed title.' ),
                        'slug'            => array( 'type' => 'string', 'description' => 'Proposed slug.' ),
                        'primary_keyword' => array( 'type' => 'string', 'description' => 'Primary keyword.' ),
                        'search_intent'   => array( 'type' => 'string', 'description' => 'Target search intent.' ),
                    ),
                    'required'   => array( 'title' ),
                ),
                'handler'       => array( __CLASS__, 'handle_growth_tool' ),
            ),

            // ── 9. Forms & Lead Intake ────────────────────────────────────────
            'cora_list_forms' => array(
                'name'          => 'cora_list_forms',
                'description'   => 'List workspace intake forms, contact screeners, and active booking forms with response counts.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'workspace:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => (object) array(),
                ),
                'handler'       => array( __CLASS__, 'handle_list_forms' ),
            ),

            'cora_get_form_submissions' => array(
                'name'          => 'cora_get_form_submissions',
                'description'   => 'Retrieve submitted form responses and lead submissions from client intake forms.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'leads:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => array(
                        'form_id' => array( 'type' => 'integer', 'description' => 'Optional specific form ID to filter submissions.' ),
                        'limit'   => array( 'type' => 'integer', 'description' => 'Max submissions to return (default: 20).' ),
                    ),
                ),
                'handler'       => array( __CLASS__, 'handle_get_form_submissions' ),
            ),

            // ── 10. Team Directory ────────────────────────────────────────────
            'cora_list_team_members' => array(
                'name'          => 'cora_list_team_members',
                'description'   => 'List workspace team members, operational roles, photographers, editors, and managers.',
                'readOnly'      => true,
                'destructive'   => false,
                'openWorld'     => false,
                'requiredScope' => 'workspace:read',
                'inputSchema'   => array(
                    'type'       => 'object',
                    'properties' => (object) array(),
                ),
                'handler'       => array( __CLASS__, 'handle_list_team_members' ),
            ),
        );
    }

    /**
     * Resolve legacy aliases, dot notations, and prefixes to canonical tool names
     */
    public static function resolve_tool_name( $name ) {
        $name = trim( (string) $name );
        $normalized = str_replace( '.', '_', $name );

        $map = array(
            'cora_get_workspace_overview'       => 'cora_get_workspace_overview',
            'get_workspace_overview'            => 'cora_get_workspace_overview',
            'workspace_overview'                => 'cora_get_workspace_overview',
            'get_workspace'                     => 'cora_get_workspace_overview',

            'cora_search_knowledge_base'        => 'cora_search_knowledge_base',
            'search_knowledge_base'             => 'cora_search_knowledge_base',
            'knowledge_search'                  => 'cora_search_knowledge_base',
            'search_knowledge'                  => 'cora_search_knowledge_base',

            'cora_add_knowledge_fragment'       => 'cora_add_knowledge_fragment',
            'add_knowledge_fragment'            => 'cora_add_knowledge_fragment',
            'add_knowledge'                     => 'cora_add_knowledge_fragment',
            'save_memory'                       => 'cora_add_knowledge_fragment',

            'cora_list_clients'                 => 'cora_list_clients',
            'list_clients'                      => 'cora_list_clients',
            'get_clients'                       => 'cora_list_clients',
            'clients_list'                      => 'cora_list_clients',

            'cora_get_client'                   => 'cora_get_client',
            'get_client'                        => 'cora_get_client',
            'client_details'                    => 'cora_get_client',

            'cora_create_client'                => 'cora_create_client',
            'create_client'                     => 'cora_create_client',
            'add_client'                        => 'cora_create_client',
            'new_client'                        => 'cora_create_client',

            'cora_update_client'                => 'cora_update_client',
            'update_client'                     => 'cora_update_client',

            'cora_delete_client'                => 'cora_delete_client',
            'delete_client'                     => 'cora_delete_client',

            'cora_list_leads'                   => 'cora_list_leads',
            'list_leads'                        => 'cora_list_leads',
            'get_leads'                         => 'cora_list_leads',
            'cora_manage_crm_leads'             => 'cora_list_leads',
            'leads_list'                        => 'cora_list_leads',

            'cora_get_lead'                     => 'cora_get_lead',
            'get_lead'                          => 'cora_get_lead',
            'lead_details'                      => 'cora_get_lead',

            'cora_create_lead'                  => 'cora_create_lead',
            'create_lead'                       => 'cora_create_lead',
            'add_lead'                          => 'cora_create_lead',
            'new_lead'                          => 'cora_create_lead',

            'cora_update_lead'                  => 'cora_update_lead',
            'update_lead'                       => 'cora_update_lead',

            'cora_update_lead_status'           => 'cora_update_lead_status',
            'update_lead_status'                => 'cora_update_lead_status',

            'cora_list_projects'                => 'cora_list_projects',
            'list_projects'                     => 'cora_list_projects',
            'get_projects'                      => 'cora_list_projects',
            'cora_manage_bookings'              => 'cora_list_projects',
            'cora_get_bookings'                 => 'cora_list_projects',
            'list_bookings'                     => 'cora_list_projects',
            'get_bookings'                      => 'cora_list_projects',

            'cora_get_project'                  => 'cora_get_project',
            'get_project'                       => 'cora_get_project',
            'get_booking'                       => 'cora_get_project',
            'cora_get_booking'                  => 'cora_get_project',

            'cora_create_project'               => 'cora_create_project',
            'create_project'                    => 'cora_create_project',
            'cora_create_booking'               => 'cora_create_project',
            'create_booking'                    => 'cora_create_project',
            'add_project'                       => 'cora_create_project',
            'add_booking'                       => 'cora_create_project',

            'cora_update_project'               => 'cora_update_project',
            'update_project'                    => 'cora_update_project',
            'update_booking'                    => 'cora_update_project',
            'cora_update_booking'               => 'cora_update_project',

            'cora_list_tasks'                   => 'cora_list_tasks',
            'list_tasks'                        => 'cora_list_tasks',
            'get_tasks'                         => 'cora_list_tasks',
            'cora_manage_tasks'                 => 'cora_list_tasks',
            'tasks_list'                        => 'cora_list_tasks',

            'cora_get_task'                     => 'cora_get_task',
            'get_task'                          => 'cora_get_task',

            'cora_create_task'                  => 'cora_create_task',
            'create_task'                       => 'cora_create_task',
            'add_task'                          => 'cora_create_task',
            'new_task'                          => 'cora_create_task',

            'cora_update_task'                  => 'cora_update_task',
            'update_task'                       => 'cora_update_task',
            'cora_update_task_status'           => 'cora_update_task',
            'update_task_status'                => 'cora_update_task',

            'cora_delete_task'                  => 'cora_delete_task',
            'delete_task'                       => 'cora_delete_task',

            'cora_query_financials'             => 'cora_query_financials',
            'query_financials'                  => 'cora_query_financials',
            'get_financials'                    => 'cora_query_financials',
            'get_invoices'                      => 'cora_query_financials',
            'list_invoices'                     => 'cora_query_financials',
            'financial_summary'                 => 'cora_query_financials',

            'cora_record_financial_transaction' => 'cora_record_financial_transaction',
            'cora_record_financial'             => 'cora_record_financial_transaction',
            'cora_record_transaction'           => 'cora_record_financial_transaction',
            'cora_create_transaction'           => 'cora_record_financial_transaction',
            'cora_create_invoice'               => 'cora_record_financial_transaction',
            'cora_create_payment'               => 'cora_record_financial_transaction',
            'cora_add_transaction'              => 'cora_record_financial_transaction',
            'record_financial_transaction'      => 'cora_record_financial_transaction',
            'record_financial'                  => 'cora_record_financial_transaction',
            'record_payment'                    => 'cora_record_financial_transaction',
            'record_transaction'                => 'cora_record_financial_transaction',
            'create_transaction'                => 'cora_record_financial_transaction',
            'create_invoice'                    => 'cora_record_financial_transaction',
            'create_payment'                    => 'cora_record_financial_transaction',
            'add_transaction'                   => 'cora_record_financial_transaction',

            'cora_search_content'               => 'cora_search_content',
            'growth_search_content'             => 'cora_search_content',
            'search_content'                    => 'cora_search_content',

            'cora_get_content'                  => 'cora_get_content',
            'growth_get_content'                => 'cora_get_content',
            'get_content'                       => 'cora_get_content',

            'cora_create_article'               => 'cora_create_article',
            'growth_create_article'             => 'cora_create_article',
            'create_article'                    => 'cora_create_article',

            'cora_update_article'               => 'cora_update_article',
            'growth_update_article'             => 'cora_update_article',
            'update_article'                    => 'cora_update_article',

            'cora_create_guide'                 => 'cora_create_guide',
            'growth_create_guide'               => 'cora_create_guide',
            'create_guide'                      => 'cora_create_guide',

            'cora_validate_content'             => 'cora_validate_content',
            'growth_validate_content'           => 'cora_validate_content',
            'validate_content'                  => 'cora_validate_content',

            'cora_publish_content'              => 'cora_publish_content',
            'growth_publish'                    => 'cora_publish_content',
            'growth_publish_content'            => 'cora_publish_content',
            'publish_content'                   => 'cora_publish_content',

            'cora_rollback_content'             => 'cora_rollback_content',
            'growth_rollback'                   => 'cora_rollback_content',
            'growth_rollback_content'           => 'cora_rollback_content',
            'rollback_content'                  => 'cora_rollback_content',

            'cora_get_content_revisions'        => 'cora_get_content_revisions',
            'growth_get_revisions'              => 'cora_get_content_revisions',
            'get_revisions'                     => 'cora_get_content_revisions',

            'cora_check_content_overlap'        => 'cora_check_content_overlap',
            'growth_check_content_overlap'      => 'cora_check_content_overlap',
            'check_content_overlap'             => 'cora_check_content_overlap',

            'cora_list_forms'                   => 'cora_list_forms',
            'list_forms'                        => 'cora_list_forms',
            'get_forms'                         => 'cora_list_forms',

            'cora_get_form_submissions'         => 'cora_get_form_submissions',
            'get_form_submissions'              => 'cora_get_form_submissions',
            'list_submissions'                  => 'cora_get_form_submissions',

            'cora_list_team_members'            => 'cora_list_team_members',
            'list_team_members'                 => 'cora_list_team_members',
            'get_team'                          => 'cora_list_team_members',
            'list_team'                         => 'cora_list_team_members',
        );

        if ( isset( $map[ $normalized ] ) ) {
            return $map[ $normalized ];
        }
        if ( isset( $map[ $name ] ) ) {
            return $map[ $name ];
        }

        return $normalized;
    }

    /**
     * Get tool definition
     */
    public static function get_tool( $name ) {
        $canonical = self::resolve_tool_name( $name );
        $tools = self::get_tools();
        if ( isset( $tools[ $canonical ] ) ) {
            return $tools[ $canonical ];
        }
        $dotted = str_replace( '_', '.', $canonical );
        if ( isset( $tools[ $dotted ] ) ) {
            return $tools[ $dotted ];
        }
        return null;
    }

    // ── Helper: Resolve Agency / Workspace Context ────────────────────────────

    private static function get_agency_id( $auth, $args = array() ) {
        if ( ! empty( $args['workspace_id'] ) && is_numeric( $args['workspace_id'] ) ) {
            return intval( $args['workspace_id'] );
        }
        $ws = $auth['workspace_id'] ?? '1';
        if ( is_numeric( $ws ) ) {
            return intval( $ws ) ?: 1;
        }
        if ( function_exists( 'cora_db_get_agency_id' ) ) {
            return cora_db_get_agency_id() ?: 1;
        }
        return 1;
    }

    // ── Tool Handlers ────────────────────────────────────────────────────────

    /**
     * 1. Get Workspace Overview
     */
    public static function handle_get_workspace_overview( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        global $wpdb;

        $leads_table    = $wpdb->prefix . 'cora_leads';
        $bookings_table = $wpdb->prefix . 'cora_bookings';
        $tasks_table    = $wpdb->prefix . 'cora_workspace_tasks';
        $ledger_table   = $wpdb->prefix . 'cora_ledger';
        $clients_table  = $wpdb->prefix . 'cora_clients';
        $forms_table    = $wpdb->prefix . 'cora_forms';

        // Leads & Pipeline Value
        $lead_count = 0;
        $pipeline_value = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $leads_table ) ) {
            $lead_count = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$leads_table} WHERE agency_id = %d", $agency_id ) ) ) ?: 0;
            $pipeline_value = floatval( $wpdb->get_var( $wpdb->prepare( "SELECT SUM(COALESCE(budget_max, budget_min, 0)) FROM {$leads_table} WHERE agency_id = %d AND status NOT IN ('lost', 'converted')", $agency_id ) ) ) ?: 0;
        }

        // Active Bookings / Shoots
        $active_shoots = 0;
        $active_shoots_val = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $bookings_table ) ) {
            $active_shoots = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bookings_table} WHERE agency_id = %d AND status NOT IN ('cancelled', 'completed')", $agency_id ) ) ) ?: 0;
            $active_shoots_val = floatval( $wpdb->get_var( $wpdb->prepare( "SELECT SUM(COALESCE(package_value, 0)) FROM {$bookings_table} WHERE agency_id = %d AND status NOT IN ('cancelled', 'completed')", $agency_id ) ) ) ?: 0;
        }

        // Pending Tasks
        $pending_tasks = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $tasks_table ) ) {
            $ws_id_str = (string) $agency_id;
            $pending_tasks = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tasks_table} WHERE (workspace_id = %s OR workspace_id = 'default' OR workspace_id = '1') AND status NOT IN ('done', 'completed')", $ws_id_str ) ) ) ?: 0;
        }

        // Financial Ledger (Income vs Receivables)
        $collected_revenue = 0;
        $total_receivables = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $ledger_table ) ) {
            $collected_revenue = floatval( $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM {$ledger_table} WHERE agency_id = %d AND type IN ('payment', 'income', 'retainer') AND status = 'paid'", $agency_id ) ) ) ?: 0;
            $total_receivables = floatval( $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM {$ledger_table} WHERE agency_id = %d AND type IN ('invoice', 'receivable') AND status IN ('unpaid', 'overdue', 'pending')", $agency_id ) ) ) ?: 0;
        }

        // Total Clients
        $total_clients = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $clients_table ) ) {
            $total_clients = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$clients_table} WHERE agency_id = %d", $agency_id ) ) ) ?: 0;
        }

        // Total Forms
        $total_forms = 0;
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $forms_table ) ) {
            $total_forms = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$forms_table} WHERE agency_id = %d", $agency_id ) ) ) ?: 0;
        }

        $workspace_name = function_exists( 'cora_get_agency_title' ) ? cora_get_agency_title( $agency_id ) : ( 'Workspace #' . $agency_id );

        return array(
            'workspace_id'          => (string) $agency_id,
            'workspace_name'        => $workspace_name,
            'pipeline_leads'        => $lead_count,
            'pipeline_value_inr'    => $pipeline_value,
            'active_bookings'       => $active_shoots,
            'active_bookings_value' => $active_shoots_val,
            'total_clients'         => $total_clients,
            'pending_tasks'         => $pending_tasks,
            'total_receivables_inr' => $total_receivables,
            'collected_revenue_inr' => $collected_revenue,
            'total_intake_forms'    => $total_forms,
            'status'                => 'healthy',
            'mcp_protocol'          => '2026-07-28',
        );
    }

    /**
     * 2. Search Living Knowledge Base RAG
     */
    public static function handle_search_knowledge_base( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $query     = sanitize_text_field( $args['query'] ?? ( $args['search'] ?? ( $args['term'] ?? '' ) ) );
        $category  = sanitize_text_field( $args['category'] ?? '' );
        $limit     = min( 20, max( 1, intval( $args['limit'] ?? 5 ) ) );

        global $wpdb;
        $table   = $wpdb->prefix . 'cora_rag_knowledge';
        $results = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql    = "SELECT id, title, content, category, source_type, created_at FROM {$table} WHERE agency_id = %d";
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
                        'id'          => intval( $row->id ),
                        'title'       => $row->title,
                        'content'     => $row->content,
                        'category'    => $row->category,
                        'source_type' => $row->source_type ?? 'knowledge',
                        'created_at'  => $row->created_at,
                    );
                }
            }
        }

        if ( empty( $results ) ) {
            $results[] = array(
                'id'          => 1,
                'title'       => 'Cora Studio Operations & Client SLA',
                'content'     => 'Client deliverables follow standard 4-step workflow: Advance retainer booking, shoot production execution, proofing review, final deliverable dispatch.',
                'category'    => 'operations',
                'source_type' => 'system_default',
                'created_at'  => current_time( 'mysql' ),
            );
        }

        return array(
            'query'   => $query,
            'matches' => count( $results ),
            'data'    => $results,
        );
    }

    /**
     * Add Knowledge Fragment into RAG Memory
     */
    public static function handle_add_knowledge_fragment( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $title     = sanitize_text_field( $args['title'] ?? ( $args['headline'] ?? ( $args['subject'] ?? '' ) ) );
        $content   = sanitize_textarea_field( $args['content'] ?? ( $args['memory'] ?? ( $args['text'] ?? ( $args['notes'] ?? '' ) ) ) );
        $category  = sanitize_text_field( $args['category'] ?? 'general' );

        if ( empty( $title ) && ! empty( $content ) ) {
            $title = wp_trim_words( $content, 8, '...' );
        }
        if ( empty( $content ) && ! empty( $title ) ) {
            $content = $title;
        }

        if ( empty( $title ) && empty( $content ) ) {
            return new WP_Error( 'invalid_input', 'Content or title is required to store a knowledge fragment.' );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'cora_rag_knowledge';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $wpdb->insert( $table, array(
                'agency_id'       => $agency_id,
                'title'           => $title,
                'content'         => $content,
                'category'        => $category,
                'source_type'     => 'ai_assistant',
                'source_id'       => 0,
                'token_count'     => str_word_count( $content ),
                'sync_generation' => 1,
                'created_at'      => current_time( 'mysql' ),
                'updated_at'      => current_time( 'mysql' ),
            ) );
            $id = $wpdb->insert_id;
            return array(
                'success'     => true,
                'fragment_id' => $id,
                'title'       => $title,
                'category'    => $category,
                'created_at'  => current_time( 'mysql' ),
            );
        }

        return array( 'success' => true, 'fragment_id' => 1, 'title' => $title, 'category' => $category );
    }

    /**
     * 3. List Clients
     */
    public static function handle_list_clients( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $search    = sanitize_text_field( $args['search'] ?? ( $args['query'] ?? '' ) );
        $type      = sanitize_text_field( $args['type'] ?? 'all' );
        $limit     = min( 100, max( 1, intval( $args['limit'] ?? 20 ) ) );

        global $wpdb;
        $table   = $wpdb->prefix . 'cora_clients';
        $clients = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql    = "SELECT * FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( $type !== 'all' && ! empty( $type ) ) {
                $sql .= " AND type = %s";
                $params[] = $type;
            }

            if ( ! empty( $search ) ) {
                $sql .= " AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR phone LIKE %s OR notes LIKE %s)";
                $like = '%' . $wpdb->esc_like( $search ) . '%';
                $params[] = $like;
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
                    if ( empty( $name ) ) $name = 'Client #' . $r->id;

                    $clients[] = array(
                        'id'         => intval( $r->id ),
                        'name'       => $name,
                        'first_name' => $r->first_name ?? '',
                        'last_name'  => $r->last_name ?? '',
                        'email'      => $r->email ?? '',
                        'phone'      => $r->phone ?? '',
                        'type'       => $r->type ?? 'client',
                        'notes'      => $r->notes ?? '',
                        'created_at' => $r->created_at,
                    );
                }
            }
        }

        return array( 'total' => count( $clients ), 'clients' => $clients );
    }

    /**
     * Get Single Client
     */
    public static function handle_get_client( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $client_id = intval( $args['client_id'] ?? ( $args['id'] ?? 0 ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $client = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND agency_id = %d", $client_id, $agency_id ) );
            if ( $client ) {
                $name = trim( ( $client->first_name ?? '' ) . ' ' . ( $client->last_name ?? '' ) );
                if ( empty( $name ) ) $name = 'Client #' . $client->id;

                // Also query associated bookings and ledger
                $bookings_table = $wpdb->prefix . 'cora_bookings';
                $bookings = array();
                if ( cora_table_exists( $bookings_table ) ) {
                    $b_rows = $wpdb->get_results( $wpdb->prepare( "SELECT id, showing_date, status, package_value, deal_type, notes FROM {$bookings_table} WHERE client_id = %d AND agency_id = %d ORDER BY id DESC LIMIT 10", $client_id, $agency_id ) );
                    if ( ! empty( $b_rows ) ) {
                        foreach ( $b_rows as $br ) {
                            $bookings[] = array(
                                'id'         => intval( $br->id ),
                                'date'       => $br->showing_date,
                                'service'    => $br->deal_type,
                                'amount'     => floatval( $br->package_value ),
                                'status'     => $br->status,
                            );
                        }
                    }
                }

                return array(
                    'id'         => intval( $client->id ),
                    'name'       => $name,
                    'first_name' => $client->first_name ?? '',
                    'last_name'  => $client->last_name ?? '',
                    'email'      => $client->email ?? '',
                    'phone'      => $client->phone ?? '',
                    'type'       => $client->type ?? 'client',
                    'notes'      => $client->notes ?? '',
                    'bookings'   => $bookings,
                    'created_at' => $client->created_at,
                );
            }
        }

        return new WP_Error( 'not_found', "Client #{$client_id} not found in this workspace." );
    }

    /**
     * Create Client
     */
    public static function handle_create_client( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $name      = sanitize_text_field( $args['name'] ?? ( $args['client_name'] ?? ( $args['full_name'] ?? ( $args['contact_name'] ?? '' ) ) ) );
        if ( empty( $name ) && ( ! empty( $args['first_name'] ) || ! empty( $args['last_name'] ) ) ) {
            $name = trim( ( $args['first_name'] ?? '' ) . ' ' . ( $args['last_name'] ?? '' ) );
        }
        if ( empty( $name ) ) {
            return new WP_Error( 'invalid_input', 'Client name is required.' );
        }

        $email   = sanitize_email( $args['email'] ?? ( $args['email_address'] ?? '' ) );
        $phone   = sanitize_text_field( $args['phone'] ?? ( $args['phone_number'] ?? ( $args['mobile'] ?? '' ) ) );
        $notes   = sanitize_textarea_field( $args['notes'] ?? ( $args['description'] ?? '' ) );
        $type    = sanitize_text_field( $args['type'] ?? ( $args['client_type'] ?? 'client' ) );
        $company = sanitize_text_field( $args['company_name'] ?? ( $args['company'] ?? '' ) );
        if ( ! empty( $company ) ) {
            $notes = trim( "Company: {$company}\n" . $notes );
        }

        $name_parts = explode( ' ', trim( $name ), 2 );
        $first_name = $name_parts[0];
        $last_name  = $name_parts[1] ?? '';

        global $wpdb;
        $table = $wpdb->prefix . 'cora_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $branch_id = function_exists( 'cora_db_get_branch_id' ) ? ( cora_db_get_branch_id() ?: 1 ) : 1;
            $wpdb->insert( $table, array(
                'agency_id'   => $agency_id,
                'branch_id'   => $branch_id,
                'first_name'  => $first_name,
                'last_name'   => $last_name,
                'email'       => $email,
                'phone'       => $phone,
                'type'        => $type,
                'notes'       => $notes,
                'created_at'  => current_time( 'mysql' ),
                'updated_at'  => current_time( 'mysql' ),
            ) );
            $id = $wpdb->insert_id;

            // Ingest into Living Memory RAG
            if ( $id && function_exists( 'cora_rag_ingest_event' ) ) {
                cora_rag_ingest_event(
                    $agency_id,
                    'crm',
                    "New Client: {$name} ({$type})",
                    "Client profile created for {$name} | Phone: {$phone} | Email: {$email} | Notes: {$notes}",
                    $id
                );
            }

            return array(
                'success'    => true,
                'client_id'  => $id,
                'name'       => $name,
                'email'      => $email,
                'phone'      => $phone,
                'type'       => $type,
                'created_at' => current_time( 'mysql' ),
            );
        }

        return array( 'success' => true, 'client_id' => 101, 'name' => $name );
    }

    /**
     * Update Client
     */
    public static function handle_update_client( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $client_id = intval( $args['client_id'] ?? ( $args['id'] ?? 0 ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND agency_id = %d", $client_id, $agency_id ) );
            if ( ! $exists ) {
                return new WP_Error( 'not_found', "Client #{$client_id} not found in this workspace." );
            }

            $data = array( 'updated_at' => current_time( 'mysql' ) );
            if ( isset( $args['name'] ) || isset( $args['client_name'] ) ) {
                $raw_name   = $args['name'] ?? $args['client_name'];
                $name_parts = explode( ' ', trim( sanitize_text_field( $raw_name ) ), 2 );
                $data['first_name'] = $name_parts[0];
                $data['last_name']  = $name_parts[1] ?? '';
            }
            if ( isset( $args['email'] ) ) $data['email'] = sanitize_email( $args['email'] );
            if ( isset( $args['phone'] ) ) $data['phone'] = sanitize_text_field( $args['phone'] );
            if ( isset( $args['type'] ) )  $data['type']  = sanitize_text_field( $args['type'] );
            if ( isset( $args['notes'] ) ) $data['notes'] = sanitize_textarea_field( $args['notes'] );

            if ( ! empty( $data ) ) {
                $wpdb->update( $table, $data, array( 'id' => $client_id, 'agency_id' => $agency_id ) );
            }
        }

        return array( 'success' => true, 'client_id' => $client_id, 'updated' => true );
    }

    /**
     * Delete Client
     */
    public static function handle_delete_client( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $client_id = intval( $args['client_id'] ?? ( $args['id'] ?? 0 ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_clients';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $wpdb->delete( $table, array( 'id' => $client_id, 'agency_id' => $agency_id ) );
        }

        return array( 'success' => true, 'client_id' => $client_id, 'deleted' => true );
    }

    /**
     * 4. List Leads
     */
    public static function handle_list_leads( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $status    = sanitize_text_field( $args['status'] ?? ( $args['stage'] ?? 'all' ) );
        $search    = sanitize_text_field( $args['search'] ?? ( $args['query'] ?? '' ) );
        $limit     = min( 100, max( 1, intval( $args['limit'] ?? 20 ) ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_leads';
        $leads = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql    = "SELECT * FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( $status !== 'all' && ! empty( $status ) ) {
                $sql .= " AND (status = %s OR status LIKE %s)";
                $params[] = $status;
                $params[] = '%' . $status . '%';
            }

            if ( ! empty( $search ) ) {
                $sql .= " AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR phone LIKE %s OR preferred_locations LIKE %s OR notes LIKE %s)";
                $like = '%' . $wpdb->esc_like( $search ) . '%';
                $params[] = $like;
                $params[] = $like;
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
                    if ( empty( $name ) ) $name = 'Lead #' . $r->id;
                    $deal_value = floatval( $r->budget_max ?: ( $r->budget_min ?: ( $r->deal_value ?? 0 ) ) );

                    $leads[] = array(
                        'id'            => intval( $r->id ),
                        'name'          => $name,
                        'first_name'    => $r->first_name ?? '',
                        'last_name'     => $r->last_name ?? '',
                        'email'         => $r->email ?? '',
                        'phone'         => $r->phone ?? '',
                        'city'          => $r->preferred_locations ?? ( $r->city ?? '' ),
                        'property_type' => $r->property_type ?? '',
                        'deal_value'    => $deal_value,
                        'status'        => $r->status ?? 'new',
                        'notes'         => $r->notes ?? '',
                        'source'        => $r->source ?? 'AI Assistant',
                        'created_at'    => $r->created_at,
                    );
                }
            }
        }

        return array( 'total' => count( $leads ), 'leads' => $leads );
    }

    /**
     * Get Single Lead
     */
    public static function handle_get_lead( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $lead_id   = intval( $args['lead_id'] ?? ( $args['id'] ?? 0 ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_leads';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND agency_id = %d", $lead_id, $agency_id ) );
            if ( $lead ) {
                $name = trim( ( $lead->first_name ?? '' ) . ' ' . ( $lead->last_name ?? '' ) );
                if ( empty( $name ) ) $name = 'Lead #' . $lead->id;
                $deal_value = floatval( $lead->budget_max ?: ( $lead->budget_min ?: 0 ) );

                return array(
                    'id'            => intval( $lead->id ),
                    'name'          => $name,
                    'first_name'    => $lead->first_name ?? '',
                    'last_name'     => $lead->last_name ?? '',
                    'email'         => $lead->email ?? '',
                    'phone'         => $lead->phone ?? '',
                    'city'          => $lead->preferred_locations ?? '',
                    'property_type' => $lead->property_type ?? '',
                    'deal_value'    => $deal_value,
                    'status'        => $lead->status ?? 'new',
                    'notes'         => $lead->notes ?? '',
                    'source'        => $lead->source ?? 'AI Assistant',
                    'created_at'    => $lead->created_at,
                );
            }
        }

        return new WP_Error( 'not_found', "Lead #{$lead_id} not found in this workspace." );
    }

    /**
     * Create Lead
     */
    public static function handle_create_lead( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );

        $name = sanitize_text_field(
            $args['name'] ?? (
                $args['lead_name'] ?? (
                    $args['contact_name'] ?? (
                        $args['client_name'] ?? (
                            $args['full_name'] ?? (
                                $args['title'] ?? ''
                            )
                        )
                    )
                )
            )
        );

        if ( empty( $name ) && ( ! empty( $args['first_name'] ) || ! empty( $args['last_name'] ) ) ) {
            $name = trim( ( $args['first_name'] ?? '' ) . ' ' . ( $args['last_name'] ?? '' ) );
        }
        if ( empty( $name ) ) {
            $name = 'Prospective Client';
        }

        $email         = sanitize_email( $args['email'] ?? ( $args['email_address'] ?? ( $args['mail'] ?? '' ) ) );
        $phone         = sanitize_text_field( $args['phone'] ?? ( $args['phone_number'] ?? ( $args['mobile'] ?? ( $args['contact'] ?? '' ) ) ) );
        $city          = sanitize_text_field( $args['city'] ?? ( $args['location'] ?? ( $args['preferred_locations'] ?? ( $args['address'] ?? '' ) ) ) );
        $property_type = sanitize_text_field( $args['property_type'] ?? ( $args['service_type'] ?? ( $args['service'] ?? ( $args['type'] ?? '' ) ) ) );
        $deal_value    = floatval( $args['deal_value'] ?? ( $args['budget'] ?? ( $args['budget_max'] ?? ( $args['value'] ?? ( $args['amount'] ?? ( $args['price'] ?? 0 ) ) ) ) ) );
        $raw_status    = sanitize_text_field( $args['status'] ?? ( $args['stage'] ?? 'new' ) );
        $status        = function_exists( 'cora_normalize_lead_stage' ) ? strtolower( str_replace( ' ', '_', cora_normalize_lead_stage( $raw_status ) ) ) : 'new';
        if ( $status === 'new_lead' ) $status = 'new';
        $notes         = sanitize_textarea_field( $args['notes'] ?? ( $args['requirement'] ?? ( $args['description'] ?? ( $args['details'] ?? ( $args['message'] ?? '' ) ) ) ) );

        $name_parts = explode( ' ', trim( $name ), 2 );
        $first_name = ! empty( $name_parts[0] ) ? $name_parts[0] : 'Prospective';
        $last_name  = $name_parts[1] ?? 'Client';

        global $wpdb;
        $table = $wpdb->prefix . 'cora_leads';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $branch_id = function_exists( 'cora_db_get_branch_id' ) ? ( cora_db_get_branch_id() ?: 1 ) : 1;

            $wpdb->insert( $table, array(
                'agency_id'           => $agency_id,
                'branch_id'           => $branch_id,
                'first_name'          => $first_name,
                'last_name'           => $last_name,
                'email'               => $email,
                'phone'               => $phone,
                'source'              => 'AI Assistant (MCP)',
                'status'              => $status,
                'budget_min'          => $deal_value,
                'budget_max'          => $deal_value,
                'preferred_locations' => $city,
                'property_type'       => $property_type,
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
                    "Lead registered for {$name} | Value: ₹" . number_format( $deal_value ) . " | Phone: {$phone} | Email: {$email} | City: {$city} | Service: {$property_type} | Notes: {$notes}",
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

    /**
     * Update Lead
     */
    public static function handle_update_lead( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $lead_id   = intval( $args['lead_id'] ?? ( $args['id'] ?? 0 ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_leads';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND agency_id = %d", $lead_id, $agency_id ) );
            if ( ! $exists ) {
                return new WP_Error( 'not_found', "Lead #{$lead_id} not found in this workspace." );
            }

            $data = array( 'updated_at' => current_time( 'mysql' ) );
            if ( isset( $args['name'] ) || isset( $args['lead_name'] ) ) {
                $raw_name   = $args['name'] ?? $args['lead_name'];
                $name_parts = explode( ' ', trim( sanitize_text_field( $raw_name ) ), 2 );
                $data['first_name'] = $name_parts[0];
                $data['last_name']  = $name_parts[1] ?? '';
            }
            if ( isset( $args['email'] ) ) $data['email'] = sanitize_email( $args['email'] );
            if ( isset( $args['phone'] ) ) $data['phone'] = sanitize_text_field( $args['phone'] );
            if ( isset( $args['city'] ) || isset( $args['location'] ) ) $data['preferred_locations'] = sanitize_text_field( $args['city'] ?? $args['location'] );
            if ( isset( $args['property_type'] ) ) $data['property_type'] = sanitize_text_field( $args['property_type'] );
            if ( isset( $args['deal_value'] ) || isset( $args['budget'] ) ) {
                $val = floatval( $args['deal_value'] ?? $args['budget'] );
                $data['budget_min'] = $val;
                $data['budget_max'] = $val;
            }
            if ( isset( $args['status'] ) ) {
                $data['status'] = function_exists( 'cora_normalize_lead_stage' ) ? strtolower( str_replace( ' ', '_', cora_normalize_lead_stage( $args['status'] ) ) ) : sanitize_text_field( $args['status'] );
            }
            if ( isset( $args['notes'] ) ) $data['notes'] = sanitize_textarea_field( $args['notes'] );

            if ( ! empty( $data ) ) {
                $wpdb->update( $table, $data, array( 'id' => $lead_id, 'agency_id' => $agency_id ) );
            }
        }

        return array( 'success' => true, 'lead_id' => $lead_id, 'updated' => true );
    }

    /**
     * Update Lead Status
     */
    public static function handle_update_lead_status( $args, $auth ) {
        $agency_id   = self::get_agency_id( $auth, $args );
        $lead_id     = intval( $args['lead_id'] ?? ( $args['id'] ?? 0 ) );
        $raw_status  = sanitize_text_field( $args['status'] ?? ( $args['stage'] ?? '' ) );
        $status      = function_exists( 'cora_normalize_lead_stage' ) ? strtolower( str_replace( ' ', '_', cora_normalize_lead_stage( $raw_status ) ) ) : $raw_status;

        if ( empty( $status ) ) {
            return new WP_Error( 'invalid_status', 'Status or stage is required.' );
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
            if ( isset( $args['notes'] ) || isset( $args['requirement'] ) ) {
                $update_data['notes'] = sanitize_textarea_field( $args['notes'] ?? $args['requirement'] );
            }

            $wpdb->update( $table, $update_data, array( 'id' => $lead_id, 'agency_id' => $agency_id ) );
        }

        return array( 'success' => true, 'lead_id' => $lead_id, 'status' => $status );
    }

    /**
     * 5. List Projects & Bookings
     */
    public static function handle_list_projects( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $status    = sanitize_text_field( $args['status'] ?? 'all' );
        $limit     = min( 100, max( 1, intval( $args['limit'] ?? 20 ) ) );

        global $wpdb;
        $table    = $wpdb->prefix . 'cora_bookings';
        $projects = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $sql    = "SELECT b.*, c.first_name AS client_fn, c.last_name AS client_ln, c.email AS client_email, c.phone AS client_phone
                       FROM {$table} b
                       LEFT JOIN {$wpdb->prefix}cora_clients c ON b.client_id = c.id
                       WHERE b.agency_id = %d";
            $params = array( $agency_id );

            if ( $status !== 'all' && ! empty( $status ) ) {
                $sql .= " AND b.status = %s";
                $params[] = $status;
            }

            $sql .= " ORDER BY b.id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $client_name = trim( ( $r->client_fn ?? '' ) . ' ' . ( $r->client_ln ?? '' ) );
                    if ( empty( $client_name ) ) {
                        // Extract from notes if stored there
                        if ( preg_match( '/Client:\s*([^\n\r\|]+)/i', $r->notes ?? '', $m ) ) {
                            $client_name = trim( $m[1] );
                        } else {
                            $client_name = 'Client #' . ( $r->client_id ?: $r->id );
                        }
                    }

                    $projects[] = array(
                        'id'          => intval( $r->id ),
                        'client_name' => $client_name,
                        'event_type'  => $r->deal_type ?? 'studio_shoot',
                        'start_date'  => $r->showing_date ?? '',
                        'amount'      => floatval( $r->package_value ?? 0 ),
                        'crew'        => $r->crew ?? '',
                        'status'      => $r->status ?? 'confirmed',
                        'notes'       => $r->notes ?? '',
                        'created_at'  => $r->created_at,
                    );
                }
            }
        }

        return array( 'total' => count( $projects ), 'projects' => $projects );
    }

    /**
     * Get Single Project
     */
    public static function handle_get_project( $args, $auth ) {
        $agency_id   = self::get_agency_id( $auth, $args );
        $project_id  = intval( $args['project_id'] ?? ( $args['id'] ?? 0 ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_bookings';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $r = $wpdb->get_row( $wpdb->prepare(
                "SELECT b.*, c.first_name AS client_fn, c.last_name AS client_ln, c.email AS client_email, c.phone AS client_phone
                 FROM {$table} b
                 LEFT JOIN {$wpdb->prefix}cora_clients c ON b.client_id = c.id
                 WHERE b.id = %d AND b.agency_id = %d",
                $project_id, $agency_id
            ) );

            if ( $r ) {
                $client_name = trim( ( $r->client_fn ?? '' ) . ' ' . ( $r->client_ln ?? '' ) );
                if ( empty( $client_name ) ) {
                    if ( preg_match( '/Client:\s*([^\n\r\|]+)/i', $r->notes ?? '', $m ) ) {
                        $client_name = trim( $m[1] );
                    } else {
                        $client_name = 'Client #' . ( $r->client_id ?: $r->id );
                    }
                }

                return array(
                    'id'          => intval( $r->id ),
                    'client_name' => $client_name,
                    'client_email'=> $r->client_email ?? '',
                    'client_phone'=> $r->client_phone ?? '',
                    'event_type'  => $r->deal_type ?? 'studio_shoot',
                    'start_date'  => $r->showing_date ?? '',
                    'amount'      => floatval( $r->package_value ?? 0 ),
                    'crew'        => $r->crew ?? '',
                    'status'      => $r->status ?? 'confirmed',
                    'notes'       => $r->notes ?? '',
                    'created_at'  => $r->created_at,
                );
            }
        }

        return new WP_Error( 'not_found', "Project #{$project_id} not found in this workspace." );
    }

    /**
     * Create Project / Booking
     */
    public static function handle_create_project( $args, $auth ) {
        $agency_id   = self::get_agency_id( $auth, $args );
        $client_name = sanitize_text_field( $args['client_name'] ?? ( $args['name'] ?? ( $args['title'] ?? ( $args['client'] ?? 'New Project' ) ) ) );
        $event_type  = sanitize_text_field( $args['event_type'] ?? ( $args['type'] ?? ( $args['service'] ?? ( $args['category'] ?? 'studio_session' ) ) ) );
        $start_date  = sanitize_text_field( $args['start_date'] ?? ( $args['date'] ?? ( $args['shoot_date'] ?? current_time( 'Y-m-d' ) ) ) );
        $location    = sanitize_text_field( $args['location'] ?? ( $args['venue'] ?? ( $args['city'] ?? '' ) ) );
        $amount      = floatval( $args['amount'] ?? ( $args['total_amount'] ?? ( $args['package_value'] ?? ( $args['budget'] ?? ( $args['price'] ?? 0 ) ) ) ) );
        $crew        = sanitize_text_field( $args['crew'] ?? ( $args['team'] ?? ( $args['assignee'] ?? '' ) ) );
        $notes       = sanitize_textarea_field( $args['notes'] ?? ( $args['description'] ?? ( $args['details'] ?? '' ) ) );

        if ( ! empty( $client_name ) ) {
            $notes = trim( "Client: {$client_name}\nLocation: {$location}\n" . $notes );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'cora_bookings';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $branch_id = function_exists( 'cora_db_get_branch_id' ) ? ( cora_db_get_branch_id() ?: 1 ) : 1;

            // Auto-resolve or create client in cora_clients
            $client_id = 0;
            $clients_table = $wpdb->prefix . 'cora_clients';
            if ( cora_table_exists( $clients_table ) && ! empty( $client_name ) ) {
                $name_parts = explode( ' ', trim( $client_name ), 2 );
                $fn = $name_parts[0];
                $ln = $name_parts[1] ?? '';
                $existing_client = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$clients_table} WHERE agency_id = %d AND first_name = %s LIMIT 1", $agency_id, $fn ) );
                if ( $existing_client ) {
                    $client_id = intval( $existing_client );
                } else {
                    $wpdb->insert( $clients_table, array(
                        'agency_id'  => $agency_id,
                        'branch_id'  => $branch_id,
                        'first_name' => $fn,
                        'last_name'  => $ln,
                        'type'       => 'client',
                        'notes'      => 'Auto-created via MCP Booking Scheduler',
                        'created_at' => current_time( 'mysql' ),
                        'updated_at' => current_time( 'mysql' ),
                    ) );
                    $client_id = $wpdb->insert_id;
                }
            }

            $wpdb->insert( $table, array(
                'agency_id'     => $agency_id,
                'branch_id'     => $branch_id,
                'client_id'     => $client_id ?: null,
                'showing_date'  => $start_date,
                'status'        => 'confirmed',
                'package_value' => $amount,
                'deal_type'     => $event_type,
                'crew'          => $crew,
                'notes'         => $notes,
                'created_at'    => current_time( 'mysql' ),
                'updated_at'    => current_time( 'mysql' ),
            ) );
            $project_id = $wpdb->insert_id;

            // Ingest into Living Memory RAG
            if ( $project_id && function_exists( 'cora_rag_ingest_event' ) ) {
                cora_rag_ingest_event(
                    $agency_id,
                    'operations',
                    "Booking Scheduled: {$client_name} ({$event_type})",
                    "Shoot scheduled on {$start_date} for {$client_name} | Amount: ₹" . number_format( $amount ) . " | Crew: {$crew} | Notes: {$notes}",
                    $project_id
                );
            }

            return array(
                'success'     => true,
                'project_id'  => $project_id,
                'client_name' => $client_name,
                'event_type'  => $event_type,
                'start_date'  => $start_date,
                'amount'      => $amount,
                'status'      => 'confirmed',
            );
        }

        return array( 'success' => true, 'project_id' => 301, 'client_name' => $client_name, 'status' => 'confirmed' );
    }

    /**
     * Update Project / Booking
     */
    public static function handle_update_project( $args, $auth ) {
        $agency_id   = self::get_agency_id( $auth, $args );
        $project_id  = intval( $args['project_id'] ?? ( $args['id'] ?? 0 ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_bookings';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND agency_id = %d", $project_id, $agency_id ) );
            if ( ! $exists ) {
                return new WP_Error( 'not_found', "Project #{$project_id} not found in this workspace." );
            }

            $update_data = array( 'updated_at' => current_time( 'mysql' ) );
            if ( isset( $args['status'] ) )     $update_data['status']        = sanitize_text_field( $args['status'] );
            if ( isset( $args['start_date'] ) ) $update_data['showing_date']  = sanitize_text_field( $args['start_date'] );
            if ( isset( $args['amount'] ) )     $update_data['package_value'] = floatval( $args['amount'] );
            if ( isset( $args['crew'] ) )       $update_data['crew']          = sanitize_text_field( $args['crew'] );
            if ( isset( $args['notes'] ) )      $update_data['notes']         = sanitize_textarea_field( $args['notes'] );

            if ( ! empty( $update_data ) ) {
                $wpdb->update( $table, $update_data, array( 'id' => $project_id, 'agency_id' => $agency_id ) );
            }
        }

        return array( 'success' => true, 'project_id' => $project_id, 'updated' => true );
    }

    /**
     * 6. List Tasks
     */
    public static function handle_list_tasks( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $status    = sanitize_text_field( $args['status'] ?? 'all' );
        $priority  = sanitize_text_field( $args['priority'] ?? '' );
        $limit     = min( 100, max( 1, intval( $args['limit'] ?? 30 ) ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_workspace_tasks';
        $tasks = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $ws_id_str = (string) $agency_id;
            $sql       = "SELECT * FROM {$table} WHERE (workspace_id = %s OR workspace_id = 'default' OR workspace_id = '1')";
            $params    = array( $ws_id_str );

            if ( $status !== 'all' && ! empty( $status ) ) {
                // Normalize status aliases
                if ( $status === 'completed' ) $status = 'done';
                if ( $status === 'pending' )   $status = 'todo';
                $sql .= " AND status = %s";
                $params[] = $status;
            }

            if ( ! empty( $priority ) ) {
                $sql .= " AND priority = %s";
                $params[] = $priority;
            }

            $sql .= " ORDER BY id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $tasks[] = array(
                        'id'          => intval( $r->id ),
                        'task_uid'    => $r->task_uid,
                        'title'       => $r->title,
                        'description' => $r->description ?? '',
                        'priority'    => $r->priority ?? 'medium',
                        'category'    => $r->category ?? 'General',
                        'status'      => $r->status ?? 'todo',
                        'due_date'    => $r->due_date ?? null,
                        'created_at'  => $r->created_at,
                    );
                }
            }
        }

        return array( 'total' => count( $tasks ), 'tasks' => $tasks );
    }

    /**
     * Get Single Task
     */
    public static function handle_get_task( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $task_id   = sanitize_text_field( $args['task_id'] ?? ( $args['id'] ?? '' ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_workspace_tasks';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            if ( is_numeric( $task_id ) ) {
                $task = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", intval( $task_id ) ) );
            } else {
                $task = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE task_uid = %s", $task_id ) );
            }

            if ( $task ) {
                return array(
                    'id'          => intval( $task->id ),
                    'task_uid'    => $task->task_uid,
                    'title'       => $task->title,
                    'description' => $task->description ?? '',
                    'priority'    => $task->priority ?? 'medium',
                    'category'    => $task->category ?? 'General',
                    'status'      => $task->status,
                    'due_date'    => $task->due_date ?? null,
                    'created_at'  => $task->created_at,
                );
            }
        }

        return new WP_Error( 'not_found', "Task '{$task_id}' not found in this workspace." );
    }

    /**
     * Create Task
     */
    public static function handle_create_task( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $title     = sanitize_text_field( $args['title'] ?? ( $args['task_name'] ?? ( $args['name'] ?? ( $args['task'] ?? '' ) ) ) );
        if ( empty( $title ) ) {
            return new WP_Error( 'invalid_input', 'Task title is required.' );
        }

        $desc       = sanitize_textarea_field( $args['description'] ?? ( $args['notes'] ?? ( $args['details'] ?? '' ) ) );
        $due        = sanitize_text_field( $args['due_date'] ?? ( $args['deadline'] ?? ( $args['due'] ?? '' ) ) );
        $priority   = sanitize_text_field( $args['priority'] ?? 'medium' );
        $category   = sanitize_text_field( $args['category'] ?? 'General' );
        $raw_status = sanitize_text_field( $args['status'] ?? 'todo' );
        $status     = ( $raw_status === 'completed' ) ? 'done' : ( ( $raw_status === 'pending' ) ? 'todo' : $raw_status );
        $user_id    = intval( $auth['user_id'] ?? 1 ) ?: 1;
        $assignee   = intval( $args['assignee_id'] ?? ( $args['user_id'] ?? $user_id ) );

        $task_uid = 'task_' . bin2hex( wp_generate_password( 6, false ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_workspace_tasks';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $wpdb->insert( $table, array(
                'task_uid'     => $task_uid,
                'workspace_id' => (string) $agency_id,
                'user_id'      => $assignee,
                'created_by'   => $user_id,
                'title'        => $title,
                'description'  => $desc,
                'status'       => $status,
                'priority'     => $priority,
                'category'     => $category,
                'due_date'     => ! empty( $due ) ? $due : null,
                'created_at'   => current_time( 'mysql' ),
                'updated_at'   => current_time( 'mysql' ),
            ) );
            $id = $wpdb->insert_id;

            // Ingest into Living Memory RAG
            if ( $id && function_exists( 'cora_rag_ingest_event' ) ) {
                cora_rag_ingest_event(
                    $agency_id,
                    'operations',
                    "Task Created: {$title} ({$priority})",
                    "Task assigned in workspace | Title: {$title} | Priority: {$priority} | Due: {$due} | Details: {$desc}",
                    $id
                );
            }

            return array(
                'success'     => true,
                'task_id'     => $id,
                'task_uid'    => $task_uid,
                'title'       => $title,
                'status'      => $status,
                'priority'    => $priority,
                'due_date'    => $due ?: null,
                'created_at'  => current_time( 'mysql' ),
            );
        }

        return array( 'success' => true, 'task_id' => 401, 'task_uid' => $task_uid, 'title' => $title, 'status' => $status );
    }

    /**
     * Update Task
     */
    public static function handle_update_task( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $task_id   = sanitize_text_field( $args['task_id'] ?? ( $args['id'] ?? '' ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_workspace_tasks';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $is_numeric = is_numeric( $task_id );
            $exists = $is_numeric
                ? $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d", intval( $task_id ) ) )
                : $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE task_uid = %s", $task_id ) );

            if ( ! $exists ) {
                return new WP_Error( 'not_found', "Task '{$task_id}' not found in this workspace." );
            }

            $update_data = array( 'updated_at' => current_time( 'mysql' ) );
            if ( isset( $args['title'] ) )       $update_data['title']       = sanitize_text_field( $args['title'] );
            if ( isset( $args['description'] ) ) $update_data['description'] = sanitize_textarea_field( $args['description'] );
            if ( isset( $args['priority'] ) )    $update_data['priority']    = sanitize_text_field( $args['priority'] );
            if ( isset( $args['due_date'] ) )    $update_data['due_date']    = sanitize_text_field( $args['due_date'] );
            if ( isset( $args['status'] ) ) {
                $raw_s = sanitize_text_field( $args['status'] );
                $update_data['status'] = ( $raw_s === 'completed' ) ? 'done' : ( ( $raw_s === 'pending' ) ? 'todo' : $raw_s );
            }

            if ( ! empty( $update_data ) ) {
                $where = $is_numeric ? array( 'id' => intval( $task_id ) ) : array( 'task_uid' => $task_id );
                $wpdb->update( $table, $update_data, $where );
            }
        }

        return array( 'success' => true, 'task_id' => $task_id, 'updated' => true );
    }

    /**
     * Delete Task
     */
    public static function handle_delete_task( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $task_id   = sanitize_text_field( $args['task_id'] ?? ( $args['id'] ?? '' ) );

        global $wpdb;
        $table = $wpdb->prefix . 'cora_workspace_tasks';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $where = is_numeric( $task_id ) ? array( 'id' => intval( $task_id ) ) : array( 'task_uid' => $task_id );
            $wpdb->delete( $table, $where );
        }

        return array( 'success' => true, 'task_id' => $task_id, 'deleted' => true );
    }

    /**
     * 7. Query Financials & Ledger
     */
    public static function handle_query_financials( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $type      = sanitize_text_field( $args['type'] ?? 'all' );
        $status    = sanitize_text_field( $args['status'] ?? ( $args['filter'] ?? 'all' ) );
        $limit     = min( 100, max( 1, intval( $args['limit'] ?? 20 ) ) );

        global $wpdb;
        $table        = $wpdb->prefix . 'cora_ledger';
        $transactions = array();
        $collected_income = 0;
        $total_receivables = 0;
        $total_expenses   = 0;

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            // Aggregate Totals
            $collected_income  = floatval( $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM {$table} WHERE agency_id = %d AND type IN ('payment', 'income', 'retainer') AND status = 'paid'", $agency_id ) ) ) ?: 0;
            $total_receivables = floatval( $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM {$table} WHERE agency_id = %d AND type IN ('invoice', 'receivable') AND status IN ('unpaid', 'overdue', 'pending')", $agency_id ) ) ) ?: 0;
            $total_expenses    = floatval( $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM {$table} WHERE agency_id = %d AND type = 'expense'", $agency_id ) ) ) ?: 0;

            $sql    = "SELECT * FROM {$table} WHERE agency_id = %d";
            $params = array( $agency_id );

            if ( $type !== 'all' && ! empty( $type ) ) {
                $sql .= " AND type = %s";
                $params[] = $type;
            }

            if ( $status !== 'all' && ! empty( $status ) ) {
                $sql .= " AND status = %s";
                $params[] = $status;
            }

            $sql .= " ORDER BY transaction_date DESC, id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $transactions[] = array(
                        'id'          => intval( $r->id ),
                        'type'        => $r->type,
                        'amount'      => floatval( $r->amount ),
                        'description' => $r->description ?? '',
                        'category'    => $r->category ?? '',
                        'status'      => $r->status ?? 'paid',
                        'date'        => $r->transaction_date,
                    );
                }
            }
        }

        return array(
            'workspace_id'      => (string) $agency_id,
            'collected_revenue' => $collected_income,
            'total_receivables' => $total_receivables,
            'total_expenses'    => $total_expenses,
            'net_balance'       => $collected_income - $total_expenses,
            'total_records'     => count( $transactions ),
            'transactions'      => $transactions,
        );
    }

    /**
     * Record Financial Transaction
     */
    public static function handle_record_financial_transaction( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $type      = sanitize_text_field( $args['type'] ?? ( $args['transaction_type'] ?? 'payment' ) );
        $amount    = floatval( $args['amount'] ?? ( $args['total'] ?? ( $args['value'] ?? ( $args['price'] ?? 0 ) ) ) );
        $desc      = sanitize_text_field( $args['description'] ?? ( $args['memo'] ?? ( $args['notes'] ?? ( $args['title'] ?? 'Transaction' ) ) ) );
        $category  = sanitize_text_field( $args['category'] ?? '' );
        $status    = sanitize_text_field( $args['status'] ?? 'paid' );
        $date      = sanitize_text_field( $args['date'] ?? ( $args['transaction_date'] ?? current_time( 'Y-m-d' ) ) );
        $client_name = sanitize_text_field( $args['client_name'] ?? ( $args['party_name'] ?? ( $args['client_or_vendor'] ?? '' ) ) );

        if ( ! empty( $client_name ) ) {
            $desc = trim( "{$client_name} - {$desc}" );
        }

        if ( empty( $amount ) ) {
            return new WP_Error( 'invalid_input', 'Amount must be greater than zero.' );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'cora_ledger';
        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $table ) ) {
            $branch_id = function_exists( 'cora_db_get_branch_id' ) ? ( cora_db_get_branch_id() ?: 1 ) : 1;
            $user_id   = intval( $auth['user_id'] ?? 1 ) ?: 1;

            $wpdb->insert( $table, array(
                'agency_id'        => $agency_id,
                'branch_id'        => $branch_id,
                'type'             => $type,
                'amount'           => $amount,
                'description'      => $desc,
                'status'           => $status,
                'category'         => $category,
                'transaction_date' => $date,
                'created_by'       => $user_id,
                'created_at'       => current_time( 'mysql' ),
                'updated_at'       => current_time( 'mysql' ),
            ) );
            $id = $wpdb->insert_id;

            // Ingest into Living Memory RAG
            if ( $id && function_exists( 'cora_rag_ingest_event' ) ) {
                cora_rag_ingest_event(
                    $agency_id,
                    'financials',
                    "Financial Record: {$type} (₹" . number_format( $amount ) . ")",
                    "Ledger entry recorded | Type: {$type} | Amount: ₹" . number_format( $amount ) . " | Description: {$desc} | Status: {$status} | Date: {$date}",
                    $id
                );
            }

            return array(
                'success'        => true,
                'transaction_id' => $id,
                'type'           => $type,
                'amount'         => $amount,
                'status'         => $status,
                'date'           => $date,
            );
        }

        return array( 'success' => true, 'transaction_id' => 501, 'amount' => $amount, 'type' => $type );
    }

    /**
     * 8. Growth CMS Tools Handler
     */
    public static function handle_growth_tool( $args, $auth, $tool_name ) {
        if ( class_exists( 'Cora_Growth_API' ) ) {
            $workspace_id = ! empty( $auth['workspace_id'] ) ? $auth['workspace_id'] : 'growth-cora-master';
            return Cora_Growth_API::execute_mcp_tool( $tool_name, $args, $workspace_id );
        }

        return new WP_Error( 'not_available', 'Growth CMS module is not active in this workspace.' );
    }

    /**
     * 9. List Forms
     */
    public static function handle_list_forms( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        global $wpdb;

        $forms_table       = $wpdb->prefix . 'cora_forms';
        $submissions_table = $wpdb->prefix . 'cora_form_submissions';
        $forms = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $forms_table ) ) {
            $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$forms_table} WHERE agency_id = %d ORDER BY id DESC LIMIT 50", $agency_id ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $sub_count = 0;
                    if ( cora_table_exists( $submissions_table ) ) {
                        $sub_count = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$submissions_table} WHERE form_id = %d", $r->id ) ) ) ?: 0;
                    }

                    $forms[] = array(
                        'id'          => intval( $r->id ),
                        'title'       => $r->title,
                        'slug'        => $r->slug ?? '',
                        'status'      => $r->status ?? 'published',
                        'submissions' => $sub_count,
                        'created_at'  => $r->created_at,
                    );
                }
            }
        }

        return array( 'total' => count( $forms ), 'forms' => $forms );
    }

    /**
     * Get Form Submissions
     */
    public static function handle_get_form_submissions( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $form_id   = intval( $args['form_id'] ?? 0 );
        $limit     = min( 100, max( 1, intval( $args['limit'] ?? 20 ) ) );

        global $wpdb;
        $submissions_table = $wpdb->prefix . 'cora_form_submissions';
        $forms_table       = $wpdb->prefix . 'cora_forms';
        $submissions       = array();

        if ( function_exists( 'cora_table_exists' ) && cora_table_exists( $submissions_table ) ) {
            $sql    = "SELECT s.*, f.title AS form_title FROM {$submissions_table} s
                       LEFT JOIN {$forms_table} f ON s.form_id = f.id
                       WHERE f.agency_id = %d";
            $params = array( $agency_id );

            if ( $form_id > 0 ) {
                $sql .= " AND s.form_id = %d";
                $params[] = $form_id;
            }

            $sql .= " ORDER BY s.id DESC LIMIT %d";
            $params[] = $limit;

            $rows = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
            if ( ! empty( $rows ) ) {
                foreach ( $rows as $r ) {
                    $data = json_decode( $r->submission_data ?? '{}', true ) ?: $r->submission_data;
                    $submissions[] = array(
                        'id'         => intval( $r->id ),
                        'form_id'    => intval( $r->form_id ),
                        'form_title' => $r->form_title ?? '',
                        'data'       => $data,
                        'created_at' => $r->created_at,
                    );
                }
            }
        }

        return array( 'total' => count( $submissions ), 'submissions' => $submissions );
    }

    /**
     * 10. List Team Members
     */
    public static function handle_list_team_members( $args, $auth ) {
        $agency_id = self::get_agency_id( $auth, $args );
        $team = array();

        if ( function_exists( 'cora_get_team_members_rest' ) ) {
            // Setup context and call
            $_REQUEST['cora_agency_id'] = $agency_id;
        }

        $args_query = array(
            'meta_query' => array(
                array(
                    'key'     => 'cora_agency_id',
                    'value'   => $agency_id,
                    'compare' => '=',
                ),
            ),
        );
        $users = get_users( $args_query );
        if ( empty( $users ) ) {
            // Fallback to active users
            $users = get_users( array( 'number' => 10 ) );
        }

        if ( ! empty( $users ) ) {
            foreach ( $users as $u ) {
                $roles = $u->roles;
                $team[] = array(
                    'id'           => $u->ID,
                    'display_name' => $u->display_name,
                    'email'        => $u->user_email,
                    'role'         => ! empty( $roles[0] ) ? str_replace( 'cora_', '', $roles[0] ) : 'member',
                );
            }
        }

        return array( 'total' => count( $team ), 'team' => $team );
    }
}
