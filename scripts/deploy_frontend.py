#!/usr/bin/env python3
import os
import sys
import subprocess
import tarfile
import pty

SSH_USER = "u484406462"
SSH_IP = "145.79.213.97"
SSH_PORT = "65002"
PASSWORD = b"Dravya@2026SHRUTIHAASAN\n"
REMOTE_TMP = "/home/u484406462/cora-frontend-deploy.tar.gz"
PUBLIC_HTML = "/home/u484406462/domains/heycora.in/public_html"
LOCAL_OUT = "/Users/shrutian/Desktop/cora/cora-frontend/out"
LOCAL_TAR = "/Users/shrutian/Desktop/cora/cora-frontend-deploy.tar.gz"

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

def main():
    print("=== Deploying Cora Frontend to heycora.in ===")
    
    # 1. Build Frontend
    print("\n1. Building Next.js static export...")
    res = subprocess.run(["npm", "--prefix", "cora-frontend", "run", "build"], cwd="/Users/shrutian/Desktop/cora")
    if res.returncode != 0:
        print("ERROR: Build failed!")
        sys.exit(1)
        
    # 2. Package out/
    print("\n2. Packaging out/ into tar.gz...")
    if os.path.exists(LOCAL_TAR):
        os.remove(LOCAL_TAR)
    with tarfile.open(LOCAL_TAR, "w:gz") as tar:
        tar.add(LOCAL_OUT, arcname=".")
    print(f"Created {LOCAL_TAR} ({os.path.getsize(LOCAL_TAR)} bytes)")

    # 3. Upload to Remote Server
    print("\n3. Uploading tar.gz to Hostinger server...")
    scp_cmd = ["scp", "-P", SSH_PORT, "-o", "StrictHostKeyChecking=no", LOCAL_TAR, f"{SSH_USER}@{SSH_IP}:{REMOTE_TMP}"]
    if not run_command_with_auth(scp_cmd):
        print("ERROR: SCP upload failed!")
        sys.exit(1)

    # 4. Extract and Deploy on Remote Server
    print("\n4. Extracting on remote server into public_html...")
    remote_script = f"""
set -e
echo "Backing up index.html..."
cp {PUBLIC_HTML}/index.html {PUBLIC_HTML}/index.html.bak 2>/dev/null || true

echo "Extracting frontend bundle..."
tar -xzf {REMOTE_TMP} -C {PUBLIC_HTML}

echo "Cleaning up remote temp..."
rm -f {REMOTE_TMP}

echo "Flushing LiteSpeed web cache..."
touch {PUBLIC_HTML}/.htaccess

echo "Deployment finished successfully."
"""
    ssh_cmd = ["ssh", "-p", SSH_PORT, "-o", "StrictHostKeyChecking=no", f"{SSH_USER}@{SSH_IP}", f"bash -c '{remote_script}'"]
    if not run_command_with_auth(ssh_cmd):
        print("ERROR: Remote extraction failed!")
        sys.exit(1)

    # Clean up local tar
    if os.path.exists(LOCAL_TAR):
        os.remove(LOCAL_TAR)

    # 5. Verify live site
    print("\n5. Verifying live site at https://heycora.in...")
    test_res = subprocess.run(["curl", "-s", "-I", "https://heycora.in"], capture_output=True, text=True)
    print(test_res.stdout)
    print("\n✅ Successfully deployed to https://heycora.in!")

if __name__ == "__main__":
    main()
