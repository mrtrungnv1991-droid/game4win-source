import re
import os

input_path = r'D:\Web Game4win\gamewinn_shopclone7.sql'
output_dir = r'C:\Users\Admin\projects\game4win-clone\database'
os.makedirs(output_dir, exist_ok=True)

# Tables to KEEP data (system config needed for site to function):
KEEP_DATA = {'admin_role', 'currencies', 'languages', 'settings', 'translate',
             'promotions', 'menu', 'post_category', 'automations'}

print("Reading SQL...")
with open(input_path, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

print(f"Original size: {len(content):,} bytes")
lines = content.split('\n')
new_lines = []
skip_until_semicolon = False

for i, line in enumerate(lines):
    # Detect INSERT INTO start
    m = re.match(r"INSERT INTO `(\w+)`", line)
    if m:
        table = m.group(1)
        if table in KEEP_DATA:
            new_lines.append(line)
            skip_until_semicolon = False
            continue
        else:
            skip_until_semicolon = True
            # Don't add this line
            continue
    
    if skip_until_semicolon:
        if line.rstrip().endswith(';'):
            skip_until_semicolon = False
            # Don't add the closing line either
        continue
    
    new_lines.append(line)

output = '\n'.join(new_lines)
output_path = os.path.join(output_dir, 'schema_clean.sql')

with open(output_path, 'w', encoding='utf-8') as f:
    f.write(output)

print(f"Cleaned size: {len(output):,} bytes")
print(f"Done: {output_path}")
