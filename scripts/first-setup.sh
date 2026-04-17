#!/bin/bash

# First-time setup script for fresh database initialization
# This sets a flag that tells project:init to run migrate:fresh only once
# Run this ONLY on first setup, or when you want to reset database completely

set -e

FRESH_INIT_FLAG="src/storage/.fresh_init_flag"

if [ -f "$FRESH_INIT_FLAG" ]; then
    echo "⚠️ Fresh init flag already set. Removing old flag..."
    rm "$FRESH_INIT_FLAG"
fi

echo "🚀 Enabling fresh initialization flag..."
touch "$FRESH_INIT_FLAG"
echo "✅ Flag created: $FRESH_INIT_FLAG"

echo ""
echo "Next steps:"
echo "1. Start containers:"
echo "   docker-compose up -d"
echo ""
echo "2. project:init will run migrate:fresh and seed data."
echo ""
echo "After this, your data will be preserved on restart!"
echo ""
