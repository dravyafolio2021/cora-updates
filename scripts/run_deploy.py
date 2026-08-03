#!/usr/bin/env python3
import pty
import os
import sys

# Script to run scripts/deploy.sh automatically by feeding password to ssh/scp prompts
target = sys.argv[1] if len(sys.argv) > 1 else "both"
cmd = ["/Users/shrutian/Desktop/cora/scripts/deploy.sh", target]
password = b"Dravya@2026SHRUTIHAASAN\n"

pid, fd = pty.fork()
if pid == 0:
    os.execvp(cmd[0], cmd)
else:
    output = b""
    password_sent_count = 0
    while True:
        try:
            chunk = os.read(fd, 4096)
            if not chunk:
                break
            output += chunk
            # Print to stdout in real-time
            sys.stdout.buffer.write(chunk)
            sys.stdout.flush()
            
            # Watch for password prompts in chunk
            if b"password:" in chunk.lower():
                os.write(fd, password)
        except OSError:
            break

