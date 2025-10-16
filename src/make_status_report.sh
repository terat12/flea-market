#!/usr/bin/env bash
set -euo pipefail

OUT="status_report_$(date +%Y-%m-%d_%H%M).md"
IGNORE_DIRS='vendor|node_modules|storage|bootstrap/cache|public/storage|.git'
FILTER_DEPR="sed '/^Deprecated:/d'"

{
  echo "# Laravel Project Context"
  echo "- Generated: $(date -Is)"
  echo ""
  echo "## Basic"
  (php artisan --version 2>/dev/null | eval "$FILTER_DEPR") || true
  echo ""
  echo "### Git"
  git rev-parse --short HEAD 2>/dev/null && echo ""
  echo "## Tree (top 3 levels, ignored: $IGNORE_DIRS)"
  if command -v tree >/dev/null 2>&1; then
    (tree -a -L 3 -I "$IGNORE_DIRS|.env|.env.example" | sed -E '/── \.env(\.example)?$/d') || true
  else
    (find . -maxdepth 3 \
      -path "./vendor" -o -path "./node_modules" -o -path "./storage" \
      -o -path "./public/storage" -o -path "./bootstrap/cache" -o -path "./.git" -prune \
      -o -print | sed -E '/\/\.env(\.example)?$/d') || true
  fi
  echo ""
  echo "## composer.json (first 200 lines)"
  sed -n '1,200p' composer.json 2>/dev/null || true
  echo -e "\n---\n"
  echo "## routes/web.php"
  sed -n '1,300p' routes/web.php 2>/dev/null || true
  echo -e "\n---\n"
  echo "## routes/api.php (if any)"
  sed -n '1,200p' routes/api.php 2>/dev/null || true
  echo -e "\n---\n"
  echo "## Route List"
  (php artisan route:list --columns=Method,URI,Name,Action,Middleware 2>/dev/null | eval "$FILTER_DEPR") || true
  echo -e "\n---\n"
  echo "## Migrations"
  ls -1 database/migrations 2>/dev/null | sed 's/^/- /' || true
  echo -e "\n---\n"
  echo "## Seeders"
  ls -1 database/seeders 2>/dev/null | sed 's/^/- /' || true
  echo -e "\n---\n"
} > "$OUT"

list_dir () {
  local dir="$1" title="$2"
  if [ -d "$dir" ]; then
    local files
    files=$( (git ls-files "$dir" 2>/dev/null; find "$dir" -type f -name '*.php' 2>/dev/null) | sort -u )
    {
      echo "## $title (file list)"
      if [ -n "$files" ]; then
        echo "$files" | sed 's/^/- /'
        echo -e "\n### $title (first ~200 lines each)\n"
        echo "$files" | while IFS= read -r f; do
          [ -f "$f" ] || continue
          echo -e "\n**$f**\n\`\`\`php"
          sed -n '1,200p' "$f"
          echo -e "\n\`\`\`\n"
        done
      else
        echo "(no files)"
      fi
      echo -e "\n---\n"
    } >> "$OUT"
  fi
}

list_dir "app/Http/Controllers" "Controllers"
list_dir "app/Http/Requests"    "Form Requests"
list_dir "app/Models"           "Models"

{
  echo "## Views (paths)"
  find resources/views -type f -name '*.blade.php' 2>/dev/null | sed 's/^/- /' || true
  echo -e "\n---\n"
  echo "## Git Status"
  git status -sb 2>/dev/null || true
  echo -e "\n### Recent Commits"
  git log --oneline -n 20 2>/dev/null || true
} >> "$OUT"

echo "Generated: $OUT"
