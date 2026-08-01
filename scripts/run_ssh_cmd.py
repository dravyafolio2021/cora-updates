#!/usr/bin/env python3
import pty
import os
import sys

# Script to run arbitrary ssh command
cmd = ["ssh", "-p", "65002", "-o", "StrictHostKeyChecking=no", "u484406462@145.79.213.97", sys.argv[1]]
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
