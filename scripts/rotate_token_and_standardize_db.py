#!/usr/bin/env python3
import os
import sys
import pty
import secrets

SECRET_FILE = "/Users/shrutian/Desktop/cora/.env.growth_token.secret"
SSH_USER = "u484406462"
SSH_IP = "145.79.213.97"
SSH_PORT = "65002"
SSH_PASSWORD = b"Dravya@2026SHRUTIHAASAN\n"

# Generate 64-char cryptographically secure token
new_token = "cora_growth_sec_" + secrets.token_hex(24)

# Save to local gitignored secret file
with open(SECRET_FILE, "w") as f:
    f.write(new_token.strip())
os.chmod(SECRET_FILE, 0o600)

php_snippet = f"""
require_once '/home/u484406462/domains/heycora.in/public_html/stagging/wp-load.php';
Cora_Growth_DB::migrate();
update_option('cora_growth_service_token', '{new_token}');
global $wpdb;
$p = $wpdb->prefix;
$wpdb->query("UPDATE {{$p}}cora_content_entries SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_content_assets SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_content_revisions SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_growth_queue SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_growth_metrics SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_growth_audit_log SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
echo 'STAGGING_DB_AND_TOKEN_UPDATED\n';

require_once '/home/u484406462/domains/heycora.in/public_html/demo/wp-load.php';
Cora_Growth_DB::migrate();
update_option('cora_growth_service_token', '{new_token}');
$p = $wpdb->prefix;
$wpdb->query("UPDATE {{$p}}cora_content_entries SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_content_assets SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_content_revisions SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_growth_queue SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_growth_metrics SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
$wpdb->query("UPDATE {{$p}}cora_growth_audit_log SET workspace_id = 'growth_cora_main_01' WHERE workspace_id = 'growth_workspace'");
echo 'DEMO_DB_AND_TOKEN_UPDATED\n';
"""

# Write temporary PHP script locally
LOCAL_TMP_PHP = "/Users/shrutian/Desktop/cora/tmp_rotate.php"
REMOTE_TMP_PHP = "/home/u484406462/tmp_rotate.php"

with open(LOCAL_TMP_PHP, "w") as f:
    f.write(f"<?php\n{php_snippet}\n")

def run_ssh(cmd_args):
    pid, fd = pty.fork()
    if pid == 0:
        os.execvp(cmd_args[0], cmd_args)
    else:
        while True:
            try:
                chunk = os.read(fd, 4096)
                if not chunk:
                    break
                if b"password:" in chunk.lower():
                    os.write(fd, SSH_PASSWORD)
                else:
                    # filter out secrets from stdout
                    chunk_clean = chunk.replace(new_token.encode('utf-8'), b'[REDACTED_TOKEN]')
                    sys.stdout.buffer.write(chunk_clean)
                    sys.stdout.flush()
            except OSError:
                break
        _, status = os.waitpid(pid, 0)
        return status == 0

# 1. SCP file
scp_cmd = ["scp", "-P", SSH_PORT, "-o", "StrictHostKeyChecking=no", LOCAL_TMP_PHP, f"{SSH_USER}@{SSH_IP}:{REMOTE_TMP_PHP}"]
if not run_ssh(scp_cmd):
    print("SCP failed")
    sys.exit(1)

# 2. SSH execute
ssh_cmd = ["ssh", "-p", SSH_PORT, "-o", "StrictHostKeyChecking=no", f"{SSH_USER}@{SSH_IP}", f"php {REMOTE_TMP_PHP} && rm -f {REMOTE_TMP_PHP}"]
if run_ssh(ssh_cmd):
    print("\n✅ Token rotation and database workspace standardization complete.")
else:
    print("\n❌ Execution failed.")
    sys.exit(1)

if os.path.exists(LOCAL_TMP_PHP):
    os.remove(LOCAL_TMP_PHP)
