#!/usr/bin/env python3
import sys
import json
import urllib.request
import urllib.error

def log(msg):
    sys.stderr.write(f"LOG: {msg}\n")
    sys.stderr.flush()

def main():
    if len(sys.argv) < 3:
        log("Usage: python3 cora-bridge.py <API_URL> <BEARER_TOKEN>")
        sys.exit(1)

    api_url = sys.argv[1]
    bearer_token = sys.argv[2]

    log("Cora MCP Stdio Bridge Connected")
    log(f"API Target: {api_url}")

    for line in sys.stdin:
        if not line.strip():
            continue
        try:
            payload = json.loads(line)
            # Make HTTP POST request to Cora REST API
            req = urllib.request.Request(
                api_url,
                data=json.dumps(payload).encode('utf-8'),
                headers={
                    "Authorization": f"Bearer {bearer_token}",
                    "Content-Type": "application/json"
                },
                method="POST"
            )
            with urllib.request.urlopen(req) as res:
                response = json.loads(res.read().decode('utf-8'))
                sys.stdout.write(json.dumps(response) + "\n")
                sys.stdout.flush()
        except urllib.error.HTTPError as e:
            try:
                err_body = e.read().decode('utf-8')
                err_json = json.loads(err_body)
                sys.stdout.write(json.dumps(err_json) + "\n")
            except:
                err_resp = {
                    "jsonrpc": "2.0",
                    "error": {"code": e.code, "message": f"HTTP Error {e.code}: {e.reason}"},
                    "id": payload.get("id") if 'payload' in locals() else None
                }
                sys.stdout.write(json.dumps(err_resp) + "\n")
            sys.stdout.flush()
        except Exception as e:
            err_resp = {
                "jsonrpc": "2.0",
                "error": {"code": -32603, "message": str(e)},
                "id": payload.get("id") if 'payload' in locals() else None
            }
            sys.stdout.write(json.dumps(err_resp) + "\n")
            sys.stdout.flush()

if __name__ == "__main__":
    main()
