#!/bin/bash

# ============================================
# APERLEX - ONE-CLICK DEPLOYMENT SCRIPT
# ============================================
# Usage: ./deploy.sh
# This script deploys to a self-hosted server

set -e  # Exit on error

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}"
echo "╔════════════════════════════════════╗"
echo "║  APERLEX - Deployment Script      ║"
echo "║  Self-Hosted Server Edition       ║"
echo "╚════════════════════════════════════╝"
echo -e "${NC}"

# ============================================
# Configuration
# ============================================

PROJECT_DIR=$(pwd)
SERVER_DIR="$PROJECT_DIR/server"
LOG_FILE="$PROJECT_DIR/deployment.log"

echo "[1/8] Checking environment..."

if [ ! -f "$PROJECT_DIR/server/package.json" ]; then
    echo -e "${RED}❌ server/package.json not found!${NC}"
    exit 1
fi

if ! command -v node &> /dev/null; then
    echo -e "${RED}❌ Node.js not installed!${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Environment OK${NC}"

# ============================================
# Stop old process
# ============================================

echo -e "\n[2/8] Stopping old process..."

if command -v pm2 &> /dev/null; then
    pm2 stop aperlex-backend 2>/dev/null || true
    echo -e "${GREEN}✓ Old process stopped${NC}"
else
    echo -e "${YELLOW}⚠ PM2 not installed, skipping...${NC}"
fi

# ============================================
# Update code
# ============================================

echo -e "\n[3/8] Updating code from git..."

cd "$PROJECT_DIR"
git fetch origin
git reset --hard origin/main 2>&1 | tee -a "$LOG_FILE"
echo -e "${GREEN}✓ Code updated${NC}"

# ============================================
# Backend setup
# ============================================

echo -e "\n[4/8] Installing backend dependencies..."

cd "$SERVER_DIR"
npm install 2>&1 | tee -a "$LOG_FILE"
echo -e "${GREEN}✓ Dependencies installed${NC}"

# ============================================
# Database migration
# ============================================

echo -e "\n[5/8] Running database migrations..."

if [ -f "$SERVER_DIR/.env" ]; then
    npm run prisma:migrate:deploy 2>&1 | tee -a "$LOG_FILE"
    echo -e "${GREEN}✓ Database migrated${NC}"
else
    echo -e "${YELLOW}⚠ .env not found, skipping migrations${NC}"
fi

# ============================================
# Build backend
# ============================================

echo -e "\n[6/8] Building backend..."

npm run build 2>&1 | tee -a "$LOG_FILE"
echo -e "${GREEN}✓ Backend built${NC}"

# ============================================
# Start backend
# ============================================

echo -e "\n[7/8] Starting backend with PM2..."

if command -v pm2 &> /dev/null; then
    pm2 start "npm run start" \
        --name "aperlex-backend" \
        --env "production" \
        --restart-delay 5000 \
        --exp-backoff-restart-delay 100 \
        2>&1 | tee -a "$LOG_FILE"
    
    pm2 save
    echo -e "${GREEN}✓ Backend started${NC}"
else
    echo -e "${RED}❌ PM2 not installed!${NC}"
    echo "Install: npm install -g pm2"
    exit 1
fi

# ============================================
# Frontend build
# ============================================

echo -e "\n[8/8] Building frontend..."

cd "$PROJECT_DIR"
npm install 2>&1 | tee -a "$LOG_FILE"
npm run build 2>&1 | tee -a "$LOG_FILE"
echo -e "${GREEN}✓ Frontend built${NC}"

# ============================================
# Summary
# ============================================

echo -e "\n${GREEN}"
echo "╔════════════════════════════════════╗"
echo "║  ✅ DEPLOYMENT COMPLETE           ║"
echo "╚════════════════════════════════════╝"
echo -e "${NC}"

echo ""
echo "📊 Status:"
pm2 status

echo ""
echo "📝 Logs:"
echo "  Backend:  pm2 logs aperlex-backend"
echo "  Monitor:  pm2 monit"
echo ""

echo "🔗 Access:"
echo "  Frontend:  http://localhost:5173 (dev) or http://localhost/build (prod)"
echo "  API:       http://localhost:3000/api"
echo "  WebSocket: ws://localhost:3000/socket.io"
echo ""

echo "✅ Deployment successful! $(date)" >> "$LOG_FILE"
