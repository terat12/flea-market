#!/usr/bin/env bash
set -euo pipefail

# ===== 設定 =====
TS="$(date +%Y-%m-%d_%H%M)"
OUT="status_catalog_${TS}.md"

# 除外ディレクトリ（巨大/生成物）
EXCLUDE_DIRS=(
  "./vendor"
  "./node_modules"
  "./storage"
  "./bootstrap/cache"
  "./public/storage"
  "./.git"
  "./.idea"
  "./.vscode"
)

# 除外ファイルパターン（秘密/バイナリ等）
EXCLUDE_GLOBS=(
  ".env" ".env.*"
  "*.png" "*.jpg" "*.jpeg" "*.gif" "*.webp" "*.ico" "*.svg"
  "*.pdf" "*.zip" "*.tgz" "*.gz"
  "*.ttf" "*.woff" "*.woff2" "*.eot"
  "status_report_*.md" "status_snapshot_*.zip" "status_snapshot_*.tgz"
)

# 収集モード：
#   broad（既定）: 典型ディレクトリ一式＋Git追加/未追跡の和集合
#   mine          : Gitで「追加」されたもの＋未追跡のみ（“自作だけ”に近い）
MODE="${MODE:-broad}"

# ===== 前提確認 =====
ls artisan composer.json >/dev/null 2>&1 || {
  echo "ここは Laravel のプロジェクト直下（artisan と composer.json がある場所）ではありません。"
  exit 1
}

# ===== ファイル候補を集める =====
declare -a CAND
# Git 由来（追加されたファイル）
if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  while IFS= read -r p; do [ -n "$p" ] && CAND+=("$p"); done < <(git log --diff-filter=A --name-only --pretty="" 2>/dev/null || true)
  # 未追跡（新規）
  while IFS= read -r p; do [ -n "$p" ] && CAND+=("$p"); done < <(git ls-files --others --exclude-standard 2>/dev/null || true)
fi

# 典型ディレクトリ（broad のときだけ）
if [[ "$MODE" == "broad" ]]; then
  for target in \
    "app/Http/Controllers" \
    "app/Http/Requests" \
    "app/Models" \
    "routes" \
    "resources/views" \
    "resources/js" \
    "resources/css" \
    "public/css" \
    "public/js" \
    "database/migrations" \
    "database/seeders"
  do
    [ -d "$target" ] || continue
    while IFS= read -r p; do [ -n "$p" ] && CAND+=("$p"); done < <(find "$target" -type f -print)
  done
fi

# 重複排除＆ソート
mapfile -t CAND < <(printf "%s\n" "${CAND[@]}" | sed 's#^\./##' | sort -u)

# 除外にマッチするものを落とす
filtered=()
for f in "${CAND[@]}"; do
  skip=""
  # ディレクトリ除外
  for d in "${EXCLUDE_DIRS[@]}"; do
    d="${d#./}"
    [[ "$f" == "$d"* ]] && { skip=1; break; }
  done
  [ -n "$skip" ] && continue
  # グロブ除外
  for g in "${EXCLUDE_GLOBS[@]}"; do
    if [[ "$f" == $g ]]; then skip=1; break; fi
  done
  [ -n "$skip" ] && continue
  # バイナリっぽいものも念のため弾く
  if file -b --mime-type -- "$f" | grep -qE '^(image/|application/(pdf|zip|x-zip|x-gzip|octet-stream))'; then
    continue
  fi
  filtered+=("$f")
done

# ===== ヘッダ =====
{
  echo "# Project Source Catalog"
  echo "- Generated: $(date -Is)"
  echo "- Mode: $MODE"
  echo "- Root: $(pwd)"
  echo
  echo "## Included files ($((${#filtered[@]})))"
  for f in "${filtered[@]}"; do echo "- $f"; done
  echo
  echo "---"
} > "$OUT"

# ===== ファイル本文を丸ごと収録 =====
detect_lang () {
  local f="$1"
  case "$f" in
    *.blade.php) echo "html" ;;   # bladeはhtml扱いに
    *.php)       echo "php" ;;
    *.js)        echo "javascript" ;;
    *.ts)        echo "typescript" ;;
    *.css)       echo "css" ;;
    *.scss)      echo "scss" ;;
    *.vue)       echo "vue" ;;
    *.json)      echo "json" ;;
    *.yml|*.yaml)echo "yaml" ;;
    *.md)        echo "markdown" ;;
    *.sh)        echo "bash" ;;
    *)           echo "" ;;
  esac
}

for f in "${filtered[@]}"; do
  [ -f "$f" ] || continue
  lang="$(detect_lang "$f")"
  echo -e "\n**$f**" >> "$OUT"
  if [ -n "$lang" ]; then
    echo -e "\n\`\`\`$lang" >> "$OUT"
  else
    echo -e "\n\`\`\`" >> "$OUT"
  fi
  # 中身をそのまま
  cat -- "$f" >> "$OUT"
  echo -e "\n\`\`\`\n" >> "$OUT"
  echo -e "\n---\n" >> "$OUT"
done

echo "Generated: $OUT"
