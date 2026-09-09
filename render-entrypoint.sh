#!/bin/bash
set -e

PORT=${PORT:-80}

if [ -f /etc/apache2/sites-available/000-default.conf ]; then
  sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf 2>/dev/null || true
  sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true
fi

if [ -f /etc/apache2/sites-available/default-ssl.conf ]; then
  sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/default-ssl.conf 2>/dev/null || true
fi

apache2ctl -D FOREGROUND
