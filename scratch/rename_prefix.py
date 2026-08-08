import re

file_path = "/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-workspace/cora-workspace.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace all occurrences of 'cora_real_estate_ai_' with 'cora_workspace_'
# but ensure we don't break other namespaces if any.
updated_content = content.replace("cora_real_estate_ai_", "cora_workspace_")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(updated_content)

print("Prefix replacement complete in cora-workspace.php")
