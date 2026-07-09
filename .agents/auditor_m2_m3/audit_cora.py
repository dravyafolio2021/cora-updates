import re

filepath = '/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php'

with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Find all function cora_ajax_...
functions = re.finditer(r'function\s+(cora_ajax_[a-zA-Z0-9_]+)\s*\(', content)

results = []
for match in functions:
    func_name = match.group(1)
    start_pos = match.start()
    
    # Extract the next 500 characters which should contain the checks
    body_snippet = content[start_pos:start_pos + 800]
    
    has_nonce = 'check_ajax_referer' in body_snippet or 'wp_verify_nonce' in body_snippet
    has_cap = 'current_user_can' in body_snippet
    
    results.append({
        'name': func_name,
        'has_nonce': has_nonce,
        'has_cap': has_cap,
        'snippet': body_snippet.split('\n')[:6]
    })

print(f"Total AJAX functions found: {len(results)}")
print("-" * 80)
missing_checks = 0
for r in results:
    if not r['has_nonce'] or not r['has_cap']:
        missing_checks += 1
        print(f"WARNING: Function {r['name']} might be missing checks!")
        print(f"  Nonce check: {r['has_nonce']}")
        print(f"  Capability check: {r['has_cap']}")
        print("  Snippet:")
        for line in r['snippet']:
            print(f"    {line}")
        print("-" * 80)

print(f"Scan complete. Functions with missing checks: {missing_checks}")
