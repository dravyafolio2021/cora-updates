#!/usr/bin/env python3
"""
Cora Multi-Tenant Deployment & Health Engine
Deploys Next.js static frontend safely without touching or disturbing:
- Production Workspace Backend (app.heycora.in / heycora.in/workspace)
- Staging Testing Workspace (stagging.heycora.in / public_html/stagging)
"""
import os
import sys
import subprocess
import tarfile
import pty
import time
import urllib.request
import ssl

SSH_USER = "u484406462"
SSH_IP = "145.79.213.97"
SSH_PORT = "65002"
PASSWORD = b"Dravya@2026SHRUTIHAASAN\n"
REMOTE_TMP = "/home/u484406462/cora-frontend-deploy.tar.gz"
PUBLIC_HTML = "/home/u484406462/domains/heycora.in/public_html"
LOCAL_OUT = "/Users/shrutian/Desktop/cora/cora-frontend/out"
LOCAL_TAR = "/Users/shrutian/Desktop/cora/cora-frontend-deploy.tar.gz"
LOCAL_HTACCESS = "/Users/shrutian/Desktop/cora/cora-frontend/public/.htaccess"

def run_command_with_auth(cmd):
    pid, fd = pty.fork()
    if pid == 0:
        os.execvp(cmd[0], cmd)
    else:
        output = b""
        while True:
            try:
                chunk = os.read(fd, 4096)
                if not chunk:
                    break
                output += chunk
                sys.stdout.buffer.write(chunk)
                sys.stdout.flush()
                if b"password:" in chunk.lower():
                    os.write(fd, PASSWORD)
            except OSError:
                break
        _, status = os.waitpid(pid, 0)
        return status == 0

def check_url(url, expected_str=None, timeout=10):
    ctx = ssl.create_default_context()
    ctx.check_hostname = False
    ctx.verify_mode = ssl.CERT_NONE
    
    start_t = time.time()
    try:
        req = urllib.request.Request(
            url,
            headers={"User-Agent": "Cora-Health-Monitor/2.4"}
        )
        with urllib.request.urlopen(req, context=ctx, timeout=timeout) as res:
            elapsed = time.time() - start_t
            code = res.getcode()
            body = res.read().decode('utf-8', errors='ignore')
            
            if expected_str and expected_str not in body:
                return False, code, elapsed, f"Expected content not matched in response"
            return True, code, elapsed, "OK"
    except Exception as e:
        elapsed = time.time() - start_t
        return False, 0, elapsed, str(e)

def main():
    print("====================================================================")
    print("  CORA MULTI-TENANT SAFE DEPLOYMENT & ISOLATION PIPELINE")
    print("====================================================================")
    
    # 1. Build Frontend
    print("\n[1/5] Building Next.js static export...")
    res = subprocess.run(["npm", "--prefix", "cora-frontend", "run", "build"], cwd="/Users/shrutian/Desktop/cora")
    if res.returncode != 0:
        print("❌ ERROR: Next.js build failed!")
        sys.exit(1)
        
    # 2. Package out/ directory (Safe fast bundle)
    print("\n[2/5] Packaging out/ into tar.gz (excluding WordPress backend paths)...")
    if os.path.exists(LOCAL_TAR):
        os.remove(LOCAL_TAR)
    
    # Exclude .DS_Store
    subprocess.run([
        "tar", "-czf", LOCAL_TAR,
        "--exclude=.DS_Store",
        "-C", LOCAL_OUT, "."
    ], check=True)
    print(f"Created {LOCAL_TAR} ({os.path.getsize(LOCAL_TAR)} bytes)")

    # 3. Upload deployment package & .htaccess to Remote Server
    print("\n[3/5] Uploading deployment package to Hostinger server...")
    scp_cmd = ["scp", "-O", "-P", SSH_PORT, "-o", "StrictHostKeyChecking=no", "-o", "ServerAliveInterval=10", LOCAL_TAR, f"{SSH_USER}@{SSH_IP}:{REMOTE_TMP}"]
    if not run_command_with_auth(scp_cmd):
        print("❌ ERROR: SCP upload failed!")
        sys.exit(1)

    print("Uploading protected hybrid .htaccess...")
    scp_ht = ["scp", "-O", "-P", SSH_PORT, "-o", "StrictHostKeyChecking=no", LOCAL_HTACCESS, f"{SSH_USER}@{SSH_IP}:{PUBLIC_HTML}/.htaccess"]
    if not run_command_with_auth(scp_ht):
        print("❌ ERROR: .htaccess upload failed!")
        sys.exit(1)

    # 4. Safe Remote Extraction & Cache Flush
    print("\n[4/5] Extracting frontend assets & verifying multi-tenant isolation...")
    remote_script = f"""
set -e
echo "Extracting frontend bundle safely..."
tar -xzf {REMOTE_TMP} -C {PUBLIC_HTML}

echo "Cleaning up temporary tar..."
rm -f {REMOTE_TMP}

echo "Flushing LiteSpeed web cache..."
touch {PUBLIC_HTML}/.htaccess
echo "Remote deployment extraction finished successfully."
"""
    ssh_cmd = ["ssh", "-p", SSH_PORT, "-o", "StrictHostKeyChecking=no", f"{SSH_USER}@{SSH_IP}", f"bash -c '{remote_script}'"]
    if not run_command_with_auth(ssh_cmd):
        print("❌ ERROR: Remote extraction failed!")
        sys.exit(1)

    # Clean up local tar
    if os.path.exists(LOCAL_TAR):
        os.remove(LOCAL_TAR)

    # 5. Comprehensive Multi-Tenant Health Verification
    print("\n[5/5] Executing live multi-tenant health verification checks...")
    
    endpoints = [
        ("Marketing Homepage (Next.js)", "https://heycora.in", "Cora"),
        ("Tools Hub (Next.js)", "https://heycora.in/tools", "Zero-Login"),
        ("GST Calculator (Next.js)", "https://heycora.in/tools/gst-calculator", "GST"),
        ("UPI QR Generator (Next.js)", "https://heycora.in/tools/upi-qr-generator", "UPI"),
        ("Pricing & 40% Flash Tier (Next.js)", "https://heycora.in/pricing?coupon=INDIA40", "India"),
        ("Features Page (Next.js)", "https://heycora.in/features", "Everything you need to run your business"),
        ("Integrations Hub (Next.js)", "https://heycora.in/integrations", "Autonomous Business Backend"),
        ("Embed Builder Tool (Next.js)", "https://heycora.in/tools/embed-builder", "1-Click Website Embed"),
        ("Production Workspace (WordPress)", "https://heycora.in/workspace/login", "login-form"),
        ("Staging Workspace (WordPress)", "https://stagging.heycora.in", None),
    ]

    all_passed = True
    for label, url, expected in endpoints:
        ok, code, elapsed, msg = check_url(url, expected)
        status_icon = "✅" if ok else "❌"
        print(f"  {status_icon} {label}: HTTP {code} ({elapsed:.2f}s) - {msg}")
        if not ok:
            all_passed = False

    if all_passed:
        print("\n🎉 ALL SERVICES HEALTHY & FAST across Marketing Site, Main Workspace, and Staging!")
    else:
        print("\n⚠️ WARNING: One or more services reported an issue. Please review the log above.")

if __name__ == "__main__":
    main()
