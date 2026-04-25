#!/bin/bash
# Complete cleanup of all Laravel/PHP files
# Keeps only: frontend, server/, and root config files

echo ""
echo "========================================"
echo "   APERLEX - Complete PHP Cleanup"
echo "========================================"
echo ""
echo "Removing all Laravel/PHP files..."
echo ""

# ========================================
# REMOVE DIRECTORIES
# ========================================

echo "[1/12] Removing app/..."
rm -rf app && echo " ✓ Removed" || echo " - Not found"

echo "[2/12] Removing bootstrap/..."
rm -rf bootstrap && echo " ✓ Removed" || echo " - Not found"

echo "[3/12] Removing config/..."
rm -rf config && echo " ✓ Removed" || echo " - Not found"

echo "[4/12] Removing database/..."
rm -rf database && echo " ✓ Removed" || echo " - Not found"

echo "[5/12] Removing routes/..."
rm -rf routes && echo " ✓ Removed" || echo " - Not found"

echo "[6/12] Removing tests/..."
rm -rf tests && echo " ✓ Removed" || echo " - Not found"

echo "[7/12] Removing vendor/..."
rm -rf vendor && echo " ✓ Removed" || echo " - Not found"

echo "[8/12] Removing docker/..."
rm -rf docker && echo " ✓ Removed" || echo " - Not found"

echo "[9/12] Removing lang/..."
rm -rf lang && echo " ✓ Removed" || echo " - Not found"

# ========================================
# REMOVE FILES
# ========================================

echo "[10/12] Removing PHP files..."
rm -f artisan && echo " ✓ Removed artisan" || echo " - Not found"
rm -f composer.json && echo " ✓ Removed composer.json" || echo " - Not found"
rm -f composer.lock && echo " ✓ Removed composer.lock" || echo " - Not found"
rm -f phpunit.xml && echo " ✓ Removed phpunit.xml" || echo " - Not found"
rm -f Procfile && echo " ✓ Removed Procfile" || echo " - Not found"
rm -f realtime-server.mjs && echo " ✓ Removed realtime-server.mjs" || echo " - Not found"

echo "[11/12] Removing test PHP files..."
rm -f test_*.php && echo " ✓ Removed test files" || echo " - Not found"

echo "[12/12] Cleaning up archive files..."
rm -f archive_laravel.bat && echo " ✓ Removed archive_laravel.bat" || echo " - Not found"
rm -f archive_laravel.sh && echo " ✓ Removed archive_laravel.sh" || echo " - Not found"

echo ""
echo "========================================"
echo ""
echo "✅ Cleanup Complete!"
echo ""
echo "Remaining structure:"
echo "   ├── server/                (✓ Node.js backend)"
echo "   ├── resources/             (✓ Frontend Vue.js)"
echo "   ├── public/                (✓ Frontend assets)"
echo "   ├── package.json           (✓ Frontend dependencies)"
echo "   ├── vite.config.js         (✓ Frontend build)"
echo "   ├── tailwind.config.js     (✓ Frontend styles)"
echo "   └── .github/               (✓ GitHub config)"
echo ""
echo "Removed:"
echo "   ✗ app/                     (Laravel backend)"
echo "   ✗ bootstrap/               (Laravel bootstrap)"
echo "   ✗ config/                  (Laravel config)"
echo "   ✗ database/                (Laravel database)"
echo "   ✗ routes/                  (Laravel routes)"
echo "   ✗ tests/                   (Laravel tests)"
echo "   ✗ vendor/                  (PHP dependencies)"
echo "   ✗ All PHP files"
echo ""
echo "📦 Your project is now 100% JavaScript!"
echo ""
echo "Next steps:"
echo "   1. cd server"
echo "   2. npm run dev"
echo "   3. Update frontend APIs"
echo ""
echo "========================================"
echo ""
