import pty, os, time

remote_cmd = "cd ~/domains/heycora.in/public_html && wp db query \"SELECT option_name, option_value FROM wp_options WHERE option_name LIKE '%office%' OR option_name LIKE '%location%'\""

cmd = ["ssh", "-o", "StrictHostKeyChecking=no", "-p", "65002", "u484406462@145.79.213.97", remote_cmd]

pid, fd = pty.fork()
if pid == 0:
    os.execvp(cmd[0], cmd)
else:
    out = b""
    sent = False
    while True:
        try:
            chunk = os.read(fd, 1024)
            if not chunk:
                break
            out += chunk
            if b"password:" in chunk and not sent:
                os.write(fd, b"Dravya@2026SHRUTIHAASAN\n")
                sent = True
        except OSError:
            break
    print(out.decode('utf-8', errors='ignore'))
