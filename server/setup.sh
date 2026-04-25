#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🚀 Aperlex Server Setup${NC}"
echo "========================================="

# Check Node.js
echo -e "\n${YELLOW}Checking Node.js...${NC}"
if ! command -v node &> /dev/null; then
    echo -e "${RED}Node.js is not installed!${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Node.js $(node -v)${NC}"

# Check npm
echo -e "\n${YELLOW}Checking npm...${NC}"
if ! command -v npm &> /dev/null; then
    echo -e "${RED}npm is not installed!${NC}"
    exit 1
fi
echo -e "${GREEN}✓ npm $(npm -v)${NC}"

# Install dependencies
echo -e "\n${YELLOW}Installing dependencies...${NC}"
npm install
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Dependencies installed${NC}"
else
    echo -e "${RED}Failed to install dependencies${NC}"
    exit 1
fi

# Create .env file if it doesn't exist
echo -e "\n${YELLOW}Setting up environment...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✓ Created .env file (please edit with your database credentials)${NC}"
else
    echo -e "${GREEN}✓ .env file already exists${NC}"
fi

# Generate Prisma client
echo -e "\n${YELLOW}Generating Prisma client...${NC}"
npm run prisma:generate
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Prisma client generated${NC}"
else
    echo -e "${RED}Failed to generate Prisma client${NC}"
    exit 1
fi

# Build TypeScript
echo -e "\n${YELLOW}Building TypeScript...${NC}"
npm run build
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ TypeScript compiled${NC}"
else
    echo -e "${RED}Failed to compile TypeScript${NC}"
    exit 1
fi

echo -e "\n${GREEN}=========================================${NC}"
echo -e "${GREEN}✓ Setup completed successfully!${NC}"
echo -e "${YELLOW}Next steps:${NC}"
echo -e "1. Edit .env with your database configuration"
echo -e "2. Run: npm run prisma:migrate (to set up database)"
echo -e "3. Run: npm run dev (to start development server)"
echo -e "\n${YELLOW}For Docker setup:${NC}"
echo -e "docker-compose up -d"
