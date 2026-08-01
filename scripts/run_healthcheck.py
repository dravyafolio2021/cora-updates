#!/usr/bin/env python3
import pty
import os
import sys

cmd = ["/Users/shrutian/Desktop/cora/scripts/healthcheck.sh"]
password = b"Dravya@2026SHRUTIHAASAN\n"

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
            
            if b"password:" in chunk:
                os.write(fd, password)
        except OSError:
            break
