#!/bin/sh
set -e

if [ ! -d "node_modules" ]; then
  echo "Installing dependencies..."
  npm install
fi

echo "Starting dev server..."
exec npm run dev -- --host
