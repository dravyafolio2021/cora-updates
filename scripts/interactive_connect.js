#!/usr/bin/env node
/**
 * Cora Universal MCP - Interactive OAuth Connect Experience Tester
 *
 * Runs a local callback server on http://localhost:8080/callback, opens your browser
 * to the Cora OAuth consent screen, captures the authorization code, exchanges it
 * for a scoped Bearer token via PKCE, and tests an MCP tool execution.
 */

const http = require('http');
const crypto = require('crypto');
const { exec } = require('child_process');

const PORT = 8080;
const HOST = 'localhost';
const REDIRECT_URI = `http://${HOST}:${PORT}/callback`;

const TARGET_BASE = process.env.CORA_TARGET_URL || 'https://app.heycora.in';
const AUTH_URL = `${TARGET_BASE}/oauth/authorize`;
const TOKEN_URL = `${TARGET_BASE}/wp-json/cora/v1/oauth/token`;
const MCP_URL = `${TARGET_BASE}/wp-json/cora/v1/mcp`;

// 1. Generate PKCE Verifier & Challenge (RFC 7636)
function generateCodeVerifier() {
    return crypto.randomBytes(32).toString('base64url');
}

function generateCodeChallenge(verifier) {
    return crypto.createHash('sha256').update(verifier).digest('base64url');
}

const codeVerifier = generateCodeVerifier();
const codeChallenge = generateCodeChallenge(codeVerifier);
const state = crypto.randomBytes(16).toString('hex');

console.log(`\n======================================================`);
console.log(`✨ CORA UNIVERSAL MCP — INTERACTIVE OAUTH TESTER`);
console.log(`======================================================`);
console.log(`Target Platform:  ${TARGET_BASE}`);
console.log(`Local Callback:   ${REDIRECT_URI}`);
console.log(`Code Challenge:   ${codeChallenge.substring(0, 16)}...`);
console.log(`======================================================\n`);

// 2. Start local listener to capture callback
const server = http.createServer(async (req, res) => {
    const reqUrl = new URL(req.url, `http://${req.headers.host}`);

    if (reqUrl.pathname === '/callback') {
        const code = reqUrl.searchParams.get('code');
        const returnedState = reqUrl.searchParams.get('state');
        const error = reqUrl.searchParams.get('error');

        if (error) {
            res.writeHead(400, { 'Content-Type': 'text/html; charset=utf-8' });
            res.end(`
                <body style="font-family: -apple-system, sans-serif; background: #fafafa; padding: 40px; text-align: center;">
                    <div style="background: white; border: 1px solid #e4e4e7; border-radius: 20px; padding: 32px; max-width: 480px; margin: 0 auto;">
                        <h2 style="color: #ef4444; margin: 0 0 8px 0;">Authorization Denied</h2>
                        <p style="color: #71717a; font-size: 14px;">Error: ${error}</p>
                    </div>
                </body>
            `);
            console.log(`\n❌ Authorization was denied by user: ${error}\n`);
            setTimeout(() => process.exit(1), 1000);
            return;
        }

        if (!code) {
            res.writeHead(400, { 'Content-Type': 'text/plain' });
            res.end('Missing code parameter.');
            return;
        }

        console.log(`\n✅ Received Authorization Code from Cora:`);
        console.log(`   ${code.substring(0, 32)}...`);

        // 3. Exchange Code for Scoped Bearer Token via PKCE
        process.stdout.write(`\n⏳ Exchanging Code for Bearer Token at ${TOKEN_URL}... `);
        try {
            const tokenRes = await fetch(TOKEN_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    grant_type: 'authorization_code',
                    client_id: 'mcp_interactive_client',
                    code: code,
                    redirect_uri: REDIRECT_URI,
                    code_verifier: codeVerifier,
                }),
            });

            const tokenData = await tokenRes.json();
            if (!tokenRes.ok || !tokenData.access_token) {
                throw new Error(tokenData.error_description || tokenData.error || 'Token exchange failed');
            }

            console.log(`\x1b[32mSUCCESS!\x1b[0m`);
            console.log(`\n🔑 Granted Token Details:`);
            console.log(`   - Access Token:   ${tokenData.access_token.substring(0, 24)}... (Redacted)`);
            console.log(`   - Workspace ID:   ${tokenData.workspace_id}`);
            console.log(`   - Granted Scopes: ${tokenData.scope}`);
            console.log(`   - Expires In:     ${tokenData.expires_in} seconds (30 days)`);

            // 4. Test an MCP Tool Execution using the new token
            process.stdout.write(`\n⏳ Executing MCP Tool 'cora.get_workspace_overview'... `);
            const mcpRes = await fetch(MCP_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${tokenData.access_token}`,
                },
                body: JSON.stringify({
                    jsonrpc: '2.0',
                    method: 'tools/call',
                    params: { name: 'cora.get_workspace_overview', arguments: {} },
                    id: 1,
                }),
            });

            const mcpData = await mcpRes.json();
            console.log(`\x1b[32mSUCCESS!\x1b[0m`);
            console.log(`\n📊 Live Workspace Pulse from MCP Response:`);
            if (mcpData.result?.content?.[0]?.text) {
                console.log(mcpData.result.content[0].text);
            } else {
                console.log(JSON.stringify(mcpData, null, 2));
            }

            // Return nice HTML page in browser
            res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
            res.end(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Cora Universal MCP — Connection Successful</title>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #fafafa; color: #18181b; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
                        .card { background: white; border: 1px solid #e4e4e7; border-radius: 24px; padding: 40px; max-width: 520px; width: 100%; box-shadow: 0 4px 20px rgba(0,0,0,0.03); text-align: center; }
                        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; background: #f4f4f5; font-size: 11px; font-weight: 600; color: #3f3f46; margin-bottom: 16px; }
                        .dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; }
                        h1 { font-size: 20px; font-weight: 600; margin: 0 0 8px 0; letter-spacing: -0.02em; }
                        p { font-size: 13px; color: #71717a; margin: 0 0 24px 0; line-height: 1.5; }
                        .details { background: #f4f4f5; border-radius: 16px; padding: 16px; text-align: left; font-size: 12px; margin-bottom: 24px; font-family: ui-monospace, monospace; color: #27272a; }
                        .details-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
                        .details-row:last-child { margin-bottom: 0; }
                        .label { color: #71717a; }
                        .btn { display: inline-block; background: #18181b; color: white; padding: 10px 20px; border-radius: 12px; font-size: 13px; font-weight: 500; text-decoration: none; }
                    </style>
                </head>
                <body>
                    <div class="card">
                        <div class="badge"><span class="dot"></span> Connected Successfully</div>
                        <h1>Cora AI Gateway Connected</h1>
                        <p>Your client is now authorized to operate your workspace via the Universal Model Context Protocol.</p>
                        
                        <div class="details">
                            <div class="details-row"><span class="label">Workspace:</span> <span>#${tokenData.workspace_id}</span></div>
                            <div class="details-row"><span class="label">Auth Type:</span> <span>OAuth 2.1 (PKCE)</span></div>
                            <div class="details-row"><span class="label">Protocol:</span> <span>MCP 2026-07-28</span></div>
                            <div class="details-row"><span class="label">Token Status:</span> <span style="color: #10b981;">Active</span></div>
                        </div>

                        <p style="font-size: 11px; color: #a1a1aa; margin: 0;">You can close this tab and return to your terminal or application.</p>
                    </div>
                </body>
                </html>
            `);

            console.log(`\n======================================================`);
            console.log(`🎉 End-to-End OAuth 2.1 Connection Flow Completed Successfully!`);
            console.log(`======================================================\n`);

            setTimeout(() => {
                server.close();
                process.exit(0);
            }, 3000);

        } catch (err) {
            console.log(`\x1b[31mFAILED\x1b[0m: ${err.message}`);
            res.writeHead(500, { 'Content-Type': 'text/plain' });
            res.end(`Error: ${err.message}`);
            setTimeout(() => process.exit(1), 1000);
        }
    }
});

server.listen(PORT, HOST, () => {
    // 5. Build Authorization URL
    const authParams = new URLSearchParams({
        response_type: 'code',
        client_id: 'mcp_interactive_client',
        client_name: 'Interactive Test AI Assistant',
        redirect_uri: REDIRECT_URI,
        state: state,
        code_challenge: codeChallenge,
        code_challenge_method: 'S256',
        scope: 'workspace:read knowledge:read clients:read clients:write leads:read leads:write projects:read projects:write tasks:read tasks:write finance:read content:read content:write',
    });

    const fullAuthUrl = `${AUTH_URL}?${authParams.toString()}`;

    console.log(`📡 Local listener started on http://${HOST}:${PORT}/callback`);
    console.log(`\n👉 Opening browser to Cora OAuth Authorization screen...`);
    console.log(`\nIf it does not open automatically, visit this URL:\n${fullAuthUrl}\n`);

    // Open browser on macOS
    exec(`open "${fullAuthUrl}"`, (err) => {
        if (err) {
            console.log(`(Please open the link above in your browser)`);
        }
    });
});
