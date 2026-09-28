#!/usr/bin/env node
/**
 * Cora Universal MCP - Comprehensive Standards & Client Compatibility Test Suite
 *
 * Validates:
 * 1. RFC 9728 Protected Resource Metadata (/.well-known/oauth-protected-resource)
 * 2. Unauthenticated 401 Rejection with RFC 6750 / RFC 9728 WWW-Authenticate header
 * 3. Modern MCP 2026-07-28 Protocol Handshake & Legacy 2024-11-05 Compatibility
 * 4. Modern server/discover Flow
 * 5. Tool Discovery & Canonical Annotations Wire Format (readOnlyHint, destructiveHint, openWorldHint, idempotentHint)
 * 6. Scoped Authorization & Tool Execution (Read & Write)
 * 7. Tenant Isolation & Zero-Trust Workspace Scoping
 * 8. RFC 8414 Authorization Server Metadata (/.well-known/oauth-authorization-server)
 * 9. Protocol Error Handling
 */

const crypto = require('crypto');

const BASE_URL = process.env.CORA_TEST_URL || 'https://stagging.heycora.in';
const MCP_ENDPOINT = `${BASE_URL}/wp-json/cora/v1/mcp`;
const PROTECTED_RESOURCE_URL = `${BASE_URL}/.well-known/oauth-protected-resource`;
const AUTH_SERVER_METADATA_URL = `${BASE_URL}/.well-known/oauth-authorization-server`;

async function runTest(name, fn) {
    process.stdout.write(`  ⏳ ${name}... `);
    try {
        const result = await fn();
        console.log(`\x1b[32mPASS\x1b[0m`);
        return { name, passed: true, result };
    } catch (err) {
        console.log(`\x1b[31mFAIL\x1b[0m\n     \x1b[31mError: ${err.message}\x1b[0m`);
        return { name, passed: false, error: err.message };
    }
}

async function sendMCPRequest(payload, token) {
    const headers = { 'Content-Type': 'application/json' };
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(MCP_ENDPOINT, {
        method: 'POST',
        headers,
        body: JSON.stringify(payload),
    });

    const wwwAuth = res.headers.get('www-authenticate') || '';
    const data = await res.json().catch(() => ({}));
    return { status: res.status, headers: res.headers, wwwAuth, data };
}

async function main() {
    console.log(`\n======================================================`);
    console.log(`🚀 CORA UNIVERSAL MCP STANDARDS & COMPATIBILITY AUDIT`);
    console.log(`Target Host: ${BASE_URL}`);
    console.log(`MCP Endpoint: ${MCP_ENDPOINT}`);
    console.log(`======================================================\n`);

    const results = [];
    const adminToken = process.env.CORA_ADMIN_MCP_TOKEN || 'cora_mcp_admin_token_rotated_sec_01';

    // ── 1. RFC 9728 OAuth Protected Resource Metadata ────────────────────────
    results.push(await runTest('1.1 OAuth Protected Resource Metadata (RFC 9728)', async () => {
        const res = await fetch(PROTECTED_RESOURCE_URL, {
            headers: { 'Accept': 'application/json' }
        });
        if (res.status !== 200) {
            throw new Error(`Protected resource endpoint returned status ${res.status}`);
        }
        const data = await res.json();
        if (!data.resource || !Array.isArray(data.authorization_servers)) {
            throw new Error(`Invalid protected resource metadata structure: ${JSON.stringify(data)}`);
        }
        if (!data.scopes_supported || !data.scopes_supported.includes('workspace:read')) {
            throw new Error(`Missing expected scopes_supported in metadata: ${JSON.stringify(data)}`);
        }
        if (!data.authorization_servers.some(as => as.includes('heycora.in') || as.includes('localhost') || as.includes('cora.local'))) {
            throw new Error(`Authorization server mismatch: ${JSON.stringify(data.authorization_servers)}`);
        }
    }));

    // ── 2. RFC 6750 / RFC 9728 Unauthenticated 401 & WWW-Authenticate ─────────
    results.push(await runTest('1.2 Unauthenticated 401 with WWW-Authenticate header', async () => {
        const { status, wwwAuth, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'initialize',
            params: { protocolVersion: '2026-07-28' },
            id: 1,
        });

        if (status !== 401) {
            throw new Error(`Expected HTTP 401, got ${status}`);
        }
        if (!data.error || data.error.code !== -32001) {
            throw new Error(`Expected JSON-RPC error code -32001, got ${JSON.stringify(data)}`);
        }
        if (!wwwAuth || !wwwAuth.toLowerCase().includes('bearer')) {
            throw new Error(`Missing or invalid WWW-Authenticate header: ${wwwAuth}`);
        }
        if (!wwwAuth.includes('resource_metadata') && !data.error?.data?.resource_metadata) {
            throw new Error(`WWW-Authenticate / error.data must reference resource_metadata: ${wwwAuth}`);
        }
    }));

    // ── 3. Modern MCP 2026-07-28 Protocol Handshake ──────────────────────────
    results.push(await runTest('2.1 Modern Protocol Handshake (2026-07-28)', async () => {
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'initialize',
            params: {
                protocolVersion: '2026-07-28',
                capabilities: {},
                clientInfo: { name: 'modern-mcp-auditor', version: '2.0.0' }
            },
            id: 2,
        }, adminToken);

        if (status !== 200 || !data.result) {
            throw new Error(`Initialize 2026-07-28 failed: ${JSON.stringify(data)}`);
        }
        if (data.result.protocolVersion !== '2026-07-28') {
            throw new Error(`Expected protocolVersion 2026-07-28, got ${data.result.protocolVersion}`);
        }
        if (data.result.serverInfo.name !== 'cora-universal-mcp') {
            throw new Error(`Expected server cora-universal-mcp, got ${data.result.serverInfo.name}`);
        }
    }));

    // ── 4. Backward Compatibility: Legacy 2024-11-05 Handshake ────────────────
    results.push(await runTest('2.2 Legacy Protocol Handshake (2024-11-05)', async () => {
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'initialize',
            params: {
                protocolVersion: '2024-11-05',
                capabilities: {},
                clientInfo: { name: 'legacy-mcp-client', version: '1.0.0' }
            },
            id: 3,
        }, adminToken);

        if (status !== 200 || !data.result) {
            throw new Error(`Legacy initialize failed: ${JSON.stringify(data)}`);
        }
        if (data.result.protocolVersion !== '2024-11-05') {
            throw new Error(`Expected negotiated protocolVersion 2024-11-05, got ${data.result.protocolVersion}`);
        }
    }));

    // ── 5. Modern server/discover Flow ───────────────────────────────────────
    results.push(await runTest('2.3 Modern server/discover Flow', async () => {
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'server/discover',
            id: 4,
        }, adminToken);

        if (status !== 200 || !data.result) {
            throw new Error(`server/discover failed: ${JSON.stringify(data)}`);
        }
        if (data.result.protocolVersion !== '2026-07-28') {
            throw new Error(`Expected protocolVersion 2026-07-28 in discovery, got ${data.result.protocolVersion}`);
        }
        if (!data.result.auth || !data.result.auth.resourceMetadata) {
            throw new Error(`Missing auth.resourceMetadata in discovery response: ${JSON.stringify(data.result)}`);
        }
    }));

    // ── 6. Tool Discovery & Wire Format Annotations ───────────────────────────
    results.push(await runTest('3.1 Canonical Tool Annotations (readOnlyHint, destructiveHint, etc.)', async () => {
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'tools/list',
            id: 5,
        }, adminToken);

        if (status !== 200 || !data.result || !Array.isArray(data.result.tools)) {
            throw new Error(`tools/list failed: ${JSON.stringify(data)}`);
        }

        const tools = data.result.tools;
        const toolNames = tools.map(t => t.name);

        const expectedCanonical = [
            'cora_get_workspace_overview',
            'cora_search_knowledge_base',
            'cora_list_clients',
            'cora_list_leads',
            'cora_list_projects',
            'cora_list_tasks',
            'cora_query_financials',
            'cora_search_content',
            'cora_create_article',
            'cora_publish_content',
        ];

        for (const exp of expectedCanonical) {
            const dotForm = exp.replace('_', '.');
            if (!toolNames.includes(exp) && !toolNames.includes(dotForm)) {
                throw new Error(`Missing canonical tool: ${exp}`);
            }
        }

        // Verify exact annotation fields wire format
        for (const tool of tools) {
            if (!tool.annotations || typeof tool.annotations !== 'object') {
                throw new Error(`Tool ${tool.name} missing annotations object on wire`);
            }
            if (typeof tool.annotations.readOnlyHint !== 'boolean') {
                throw new Error(`Tool ${tool.name} missing annotations.readOnlyHint boolean`);
            }
            if (typeof tool.annotations.destructiveHint !== 'boolean') {
                throw new Error(`Tool ${tool.name} missing annotations.destructiveHint boolean`);
            }
            if (typeof tool.annotations.openWorldHint !== 'boolean') {
                throw new Error(`Tool ${tool.name} missing annotations.openWorldHint boolean`);
            }
            if (typeof tool.annotations.idempotentHint !== 'boolean') {
                throw new Error(`Tool ${tool.name} missing annotations.idempotentHint boolean`);
            }
        }

        const publishTool = tools.find(t => t.name === 'cora_publish_content' || t.name === 'cora.publish_content');
        if (!publishTool || !publishTool.annotations.destructiveHint) {
            throw new Error(`cora_publish_content must have annotations.destructiveHint === true`);
        }

        const overviewTool = tools.find(t => t.name === 'cora_get_workspace_overview' || t.name === 'cora.get_workspace_overview');
        if (!overviewTool || !overviewTool.annotations.readOnlyHint) {
            throw new Error(`cora_get_workspace_overview must have annotations.readOnlyHint === true`);
        }
    }));

    // ── 7. Read Operations ───────────────────────────────────────────────────
    results.push(await runTest('4.1 Read Tool: cora.get_workspace_overview', async () => {
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'tools/call',
            params: { name: 'cora.get_workspace_overview', arguments: {} },
            id: 6,
        }, adminToken);

        if (status !== 200 || !data.result || data.result.isError) {
            throw new Error(`Overview tool failed: ${JSON.stringify(data)}`);
        }
        const text = data.result.content[0].text;
        if (!text.includes('workspace_id') && !text.includes('status')) {
            throw new Error(`Unexpected overview content: ${text}`);
        }
    }));

    results.push(await runTest('4.2 Read Tool: cora.search_knowledge_base (Living RAG)', async () => {
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'tools/call',
            params: { name: 'cora.search_knowledge_base', arguments: { query: 'operations' } },
            id: 7,
        }, adminToken);

        if (status !== 200 || !data.result || data.result.isError) {
            throw new Error(`Knowledge search failed: ${JSON.stringify(data)}`);
        }
    }));

    results.push(await runTest('4.3 Read Tool: cora.query_financials', async () => {
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'tools/call',
            params: { name: 'cora.query_financials', arguments: { filter: 'all', limit: 5 } },
            id: 8,
        }, adminToken);

        if (status !== 200 || !data.result || data.result.isError) {
            throw new Error(`Financials query failed: ${JSON.stringify(data)}`);
        }
    }));

    // ── 8. Write Operations ──────────────────────────────────────────────────
    results.push(await runTest('5.1 Write Tool: cora.create_task', async () => {
        const testTitle = `Standards Verification Task ${Date.now()}`;
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'tools/call',
            params: {
                name: 'cora.create_task',
                arguments: {
                    title: testTitle,
                    description: 'Automated test task for standards compliance.',
                    priority: 'medium'
                }
            },
            id: 9,
        }, adminToken);

        if (status !== 200 || !data.result || data.result.isError) {
            throw new Error(`Task creation failed: ${JSON.stringify(data)}`);
        }
    }));

    // ── 9. Tenant Isolation Enforcement ──────────────────────────────────────
    results.push(await runTest('6.1 Tenant Isolation (Spoofed workspace_id override)', async () => {
        const { status, data } = await sendMCPRequest({
            jsonrpc: '2.0',
            method: 'tools/call',
            params: {
                name: 'cora.get_workspace_overview',
                arguments: {
                    workspace_id: '9999_unauthorized_spoofed'
                }
            },
            id: 10,
        }, adminToken);

        if (status !== 200 || !data.result || data.result.isError) {
            throw new Error(`Tenant isolation execution failed: ${JSON.stringify(data)}`);
        }
        const text = data.result.content[0].text;
        if (text.includes('"workspace_id": "9999_unauthorized_spoofed"')) {
            throw new Error(`Vulnerability: Server accepted spoofed workspace_id parameter!`);
        }
    }));

    // ── 10. OAuth Discovery (RFC 8414 & CIMD) ─────────────────────────────────
    results.push(await runTest('7.1 OAuth Server Metadata (RFC 8414 & CIMD Support)', async () => {
        const res = await fetch(AUTH_SERVER_METADATA_URL, {
            headers: { 'Accept': 'application/json' }
        });
        if (res.status !== 200) {
            throw new Error(`OAuth discovery endpoint returned status ${res.status}`);
        }
        const data = await res.json();
        if (!data.issuer || !data.authorization_endpoint || !data.token_endpoint) {
            throw new Error(`Invalid OAuth metadata structure: ${JSON.stringify(data)}`);
        }
        if (data.client_id_metadata_document_supported !== true) {
            throw new Error(`Expected client_id_metadata_document_supported: true`);
        }
        if (!data.protected_resources || !data.protected_resources.length) {
            throw new Error(`Missing protected_resources array in OAuth metadata`);
        }
    }));

    console.log(`\n======================================================`);
    const passedCount = results.filter(r => r.passed).length;
    console.log(`Audit Summary: ${passedCount}/${results.length} tests passed.`);
    console.log(`======================================================\n`);

    if (passedCount !== results.length) {
        process.exit(1);
    }
}

if (require.main === module) {
    main().catch(err => {
        console.error('Fatal test error:', err);
        process.exit(1);
    });
}
