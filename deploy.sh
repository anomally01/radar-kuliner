#!/bin/bash

# ============================================================
#  🍜 Radar Kuliner — Deploy Script (GitHub + Vercel)
#  Usage:
#    bash deploy.sh              → cek status saja
#    bash deploy.sh "pesan"      → commit, push & deploy
#    bash deploy.sh --prod "msg" → deploy ke production Vercel
# ============================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEPLOY_PROD=false
COMMIT_MSG=""

# ---- Parse argumen ----
for arg in "$@"; do
    if [[ "$arg" == "--prod" ]]; then
        DEPLOY_PROD=true
    elif [[ -z "$COMMIT_MSG" && "$arg" != "--prod" ]]; then
        COMMIT_MSG="$arg"
    fi
done

# ---- Banner ----
echo -e "${CYAN}${BOLD}"
echo "  ██████╗  █████╗ ██████╗  █████╗ ██████╗ "
echo "  ██╔══██╗██╔══██╗██╔══██╗██╔══██╗██╔══██╗"
echo "  ██████╔╝███████║██║  ██║███████║██████╔╝"
echo "  ██╔══██╗██╔══██║██║  ██║██╔══██║██╔══██╗"
echo "  ██║  ██║██║  ██║██████╔╝██║  ██║██║  ██║"
echo "  ╚═╝  ╚═╝╚═╝  ╚═╝╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝"
echo -e "  🍜 Radar Kuliner — GitHub + Vercel Deploy${NC}"
echo ""

cd "$REPO_DIR" || { echo -e "${RED}❌ Gagal masuk ke direktori project!${NC}"; exit 1; }

# ===========================================================
#  SECTION 1: CEK STATUS
# ===========================================================
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}📊 STATUS PROJECT${NC}"
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

echo -e "${CYAN}📂 Direktori :${NC} $REPO_DIR"
echo -e "${CYAN}🌿 Branch    :${NC} $(git branch --show-current)"
echo -e "${CYAN}🔗 Remote    :${NC} $(git remote get-url origin 2>/dev/null || echo 'tidak ada')"
echo ""

# Cek git status
CHANGED_FILES=$(git status --short)
if [ -z "$CHANGED_FILES" ]; then
    echo -e "${GREEN}✅ Git: Working tree bersih${NC}"
else
    echo -e "${YELLOW}📋 Git: Ada perubahan:${NC}"
    git status --short
fi

echo ""
echo -e "${CYAN}📜 5 commit terakhir:${NC}"
git log --oneline -5
echo ""

# Cek Vercel CLI
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}☁️  CEK VERCEL CLI${NC}"
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if ! command -v vercel &> /dev/null; then
    echo -e "${YELLOW}⚠️  Vercel CLI belum terinstall.${NC}"
    echo -e "${YELLOW}   Install dengan: npm i -g vercel${NC}"
    VERCEL_AVAILABLE=false
else
    VERCEL_VERSION=$(vercel --version 2>/dev/null)
    echo -e "${GREEN}✅ Vercel CLI: ${VERCEL_VERSION}${NC}"
    VERCEL_AVAILABLE=true
fi
echo ""

# ===========================================================
#  Jika tidak ada argumen → hanya cek, tidak deploy
# ===========================================================
if [ -z "$COMMIT_MSG" ] && [ -z "$CHANGED_FILES" ]; then
    echo -e "${GREEN}${BOLD}ℹ️  Mode: CEK SAJA. Untuk deploy jalankan:${NC}"
    echo -e "   ${CYAN}bash deploy.sh \"pesan commit\"${NC}          → preview Vercel"
    echo -e "   ${CYAN}bash deploy.sh --prod \"pesan commit\"${NC}   → production Vercel"
    exit 0
fi

# ===========================================================
#  SECTION 2: GIT COMMIT & PUSH
# ===========================================================
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}🐙 GIT PUSH${NC}"
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if [ -n "$CHANGED_FILES" ]; then
    # Minta pesan commit jika belum ada
    if [ -z "$COMMIT_MSG" ]; then
        echo -e "${YELLOW}💬 Masukkan pesan commit:${NC}"
        read -r COMMIT_MSG
    fi
    if [ -z "$COMMIT_MSG" ]; then
        COMMIT_MSG="chore: update $(date +'%Y-%m-%d %H:%M')"
        echo -e "${YELLOW}⚠️  Pakai pesan default: '${COMMIT_MSG}'${NC}"
    fi

    echo -e "${CYAN}➕ git add -A${NC}"
    git add -A || { echo -e "${RED}❌ git add gagal!${NC}"; exit 1; }

    echo -e "${CYAN}📝 git commit: \"$COMMIT_MSG\"${NC}"
    git commit -m "$COMMIT_MSG" || { echo -e "${RED}❌ git commit gagal!${NC}"; exit 1; }

    echo -e "${CYAN}🚀 git push origin main...${NC}"
    git push origin main || { echo -e "${RED}❌ git push gagal!${NC}"; exit 1; }

    echo -e "${GREEN}✅ Kode berhasil di-push ke GitHub!${NC}"
else
    echo -e "${GREEN}✅ Tidak ada perubahan, skip git push.${NC}"
fi
echo ""

# ===========================================================
#  SECTION 3: DEPLOY KE VERCEL
# ===========================================================
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${CYAN}☁️  DEPLOY KE VERCEL${NC}"
echo -e "${BOLD}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if [ "$VERCEL_AVAILABLE" = false ]; then
    echo -e "${YELLOW}⚠️  Vercel CLI tidak tersedia, skip deploy.${NC}"
    echo -e "${YELLOW}   Install: npm i -g vercel${NC}"
    exit 0
fi

if [ "$DEPLOY_PROD" = true ]; then
    echo -e "${CYAN}🎯 Target: ${RED}${BOLD}PRODUCTION${NC}"
    echo -e "${YELLOW}❓ Deploy ke production? (y/n):${NC}"
    read -r CONFIRM
    if [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]]; then
        echo -e "${RED}🚫 Deploy production dibatalkan.${NC}"
        exit 0
    fi
    echo -e "${CYAN}🚀 Deploying ke Vercel production...${NC}"
    vercel --prod
else
    echo -e "${CYAN}🎯 Target: ${YELLOW}PREVIEW${NC}"
    echo -e "${CYAN}🚀 Deploying ke Vercel preview...${NC}"
    vercel
fi

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}${BOLD}✅ Deploy berhasil!${NC}"
    echo -e "${CYAN}🔗 GitHub : ${NC}https://github.com/anomally01/radar-kuliner"
    if [ "$DEPLOY_PROD" = true ]; then
        echo -e "${CYAN}🔗 Vercel  : ${NC}https://radar-kuliner.vercel.app"
    fi
else
    echo -e "${RED}❌ Deploy Vercel gagal!${NC}"
    exit 1
fi
