import json

with open(r'C:/Users/Admin/projects/game4win-clone/demo/games_full.json', 'r', encoding='utf-8') as f:
    games = json.load(f)

# Generate SQL
sql = []
sql.append("\n-- ============================================")
sql.append("-- GameTopup Tables + Seed Data")
sql.append("-- ============================================\n")

# CREATE TABLE games
sql.append("DROP TABLE IF EXISTS `topup_tiers`;")
sql.append("DROP TABLE IF EXISTS `game_servers`;")
sql.append("DROP TABLE IF EXISTS `games`;")
sql.append("DROP TABLE IF EXISTS `topup_api_logs`;\n")

sql.append("""CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `full_name` varchar(500) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `icon` varchar(10) DEFAULT NULL,
  `currency_name` varchar(100) DEFAULT NULL,
  `currency_unit` varchar(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n""")

sql.append("""CREATE TABLE `topup_tiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `type` enum('gem','pack','allpack') NOT NULL DEFAULT 'gem',
  `label` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `price` int(11) NOT NULL DEFAULT 0,
  `cost` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_game_type` (`game_id`, `type`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n""")

sql.append("""CREATE TABLE `game_servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_game` (`game_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n""")

sql.append("""CREATE TABLE `topup_api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `request_data` text DEFAULT NULL,
  `response_data` text DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `duration_ms` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n""")

# SEED GAMES + TIERS
sql.append("-- Seed 121 games")
game_values = []
tier_values = []

for g in games:
    name = g['name'].replace("'", "\\'")
    full = (g.get('fullName') or g['name']).replace("'", "\\'")
    cat = (g.get('cat') or '').replace("'", "\\'")
    icon = (g.get('icon') or '').replace("'", "\\'")
    cname = (g.get('currencyName') or '').replace("'", "\\'")
    cunit = (g.get('currencyUnit') or '').replace("'", "\\'")
    
    game_values.append(f"({g['id']}, '{name}', '{full}', '{cat}', '{icon}', '{cname}', '{cunit}', 1, {g['id']})")

    # Tiers
    for tier_type in ['gem', 'pack', 'allpack']:
        tiers = g.get(tier_type, [])
        for order, t in enumerate(tiers):
            label = t['label'].replace("'", "\\'")
            # Parse amount from label (e.g., "60 Kim cương" -> 60)
            import re
            amt_match = re.match(r'(\d+)', t['label'])
            amount = int(amt_match.group(1)) if amt_match else 0
            tier_values.append(f"({g['id']}, '{tier_type}', '{label}', {amount}, {t['vnd']}, 0, 1, {order})")

# Write games INSERT - batch 50 per statement
for i in range(0, len(game_values), 50):
    batch = game_values[i:i+50]
    sql.append(f"\nINSERT INTO `games` (`id`, `name`, `full_name`, `category`, `icon`, `currency_name`, `currency_unit`, `status`, `sort_order`) VALUES\n" + ",\n".join(batch) + ";")

# Write tiers INSERT - batch 100 per statement  
for i in range(0, len(tier_values), 100):
    batch = tier_values[i:i+100]
    sql.append(f"\nINSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES\n" + ",\n".join(batch) + ";")

sql.append(f"\n-- Total: {len(games)} games, {len(tier_values)} tiers seeded\n")

# Append to schema file
with open(r'C:/Users/Admin/projects/game4win-clone/database/schema_clean.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Insert before COMMIT
content = content.replace('\nCOMMIT;\n', '\n' + '\n'.join(sql) + '\n\nCOMMIT;\n')

with open(r'C:/Users/Admin/projects/game4win-clone/database/schema_clean.sql', 'w', encoding='utf-8') as f:
    f.write(content)

print(f"Added {len(games)} games, {len(tier_values)} tiers to schema")
print(f"New file size: {len(content):,} bytes")
