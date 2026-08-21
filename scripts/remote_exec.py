#!/usr/bin/env python3
import os
import sys
import pty

PASSWORD = b"Dravya@2026SHRUTIHAASAN\n"
SSH_USER = "u484406462"
SSH_IP = "145.79.213.97"
SSH_PORT = "65002"

def exec_remote(remote_cmd):
    cmd = ["ssh", "-p", SSH_PORT, f"{SSH_USER}@{SSH_IP}", remote_cmd]
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
                if b"password:" in chunk.lower():
                    os.write(fd, PASSWORD)
            except OSError:
                break
        _, status = os.waitpid(pid, 0)
        return output.decode("utf-8", errors="ignore")

if __name__ == "__main__":
    remote_cmd = " ".join(sys.argv[1:]) if len(sys.argv) > 1 else "ls -la /home/u484406462/domains/"
    result = exec_remote(remote_cmd)
    print(result)
