# 🔒 SSL/HTTPS Setup with Let's Encrypt

## Overview

Setting up free SSL certificates with **Let's Encrypt** and **Certbot** for production deployment.

---

## 📋 Prerequisites

- Domain name pointing to your server
- Server with Nginx or Apache
- SSH access to server
- Ubuntu/Debian or other Linux distro

---

## 🚀 Installation Steps

### 1. Install Certbot

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install certbot python3-certbot-nginx -y
```

**CentOS/RHEL:**
```bash
sudo yum install certbot python3-certbot-nginx -y
```

---

### 2. Setup Nginx Configuration

**Create/edit `/etc/nginx/sites-available/aperlex`:**

```nginx
upstream api_backend {
    server 127.0.0.1:3000;
}

server {
    listen 80;
    server_name api.yourdom domain.com www.yourdomain.com;

    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.yourdomain.com www.yourdomain.com;

    # SSL certificates (Certbot will update these)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "DENY" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Logging
    access_log /var/log/nginx/aperlex_access.log;
    error_log /var/log/nginx/aperlex_error.log;

    # Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;
    gzip_min_length 1000;

    # API proxy
    location /api/ {
        proxy_pass http://api_backend;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }

    # WebSocket
    location /socket.io/ {
        proxy_pass http://api_backend;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_cache_bypass $http_upgrade;
    }

    # Frontend
    location / {
        root /var/www/aperlex/dist;
        try_files $uri $uri/ /index.html;
    }

    # Health check
    location /health {
        access_log off;
        return 200 "OK";
        add_header Content-Type text/plain;
    }
}
```

**Enable the site:**
```bash
sudo ln -s /etc/nginx/sites-available/aperlex /etc/nginx/sites-enabled/
sudo nginx -t  # Test configuration
sudo systemctl restart nginx
```

---

### 3. Get SSL Certificate

**First time (Certbot will verify your domain):**

```bash
sudo certbot certonly --nginx -d yourdomain.com -d www.yourdomain.com
```

Or with automatic Nginx update:
```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

**What Certbot will do:**
- Create SSL certificate at `/etc/letsencrypt/live/yourdomain.com/`
- Update Nginx configuration automatically
- Reload Nginx

---

### 4. Setup Auto-Renewal

Let's Encrypt certificates expire in 90 days. Certbot auto-renewal:

**Check renewal timer:**
```bash
sudo systemctl list-timers | grep certbot
```

**Manual renewal test:**
```bash
sudo certbot renew --dry-run
```

**Manual renewal:**
```bash
sudo certbot renew
```

---

## ✅ Verification

### Test SSL Certificate

```bash
# Check certificate expiry
sudo certbot certificates

# Test SSL configuration
curl -I https://yourdomain.com

# Grade check (SSL Labs)
# Visit: https://www.ssllabs.com/ssltest/
```

### Verify Nginx Configuration

```bash
# Check syntax
sudo nginx -t

# Check running
sudo systemctl status nginx

# View logs
sudo tail -f /var/log/nginx/aperlex_error.log
```

---

## 🔧 Renewal Management

### Manual Renewal Schedule

```bash
# Add to crontab (runs daily at 2 AM)
sudo crontab -e

# Add this line:
0 2 * * * /usr/bin/certbot renew --quiet
```

### Certbot Hooks

**Create renewal hook script:** `/usr/local/bin/certbot-renewal-hook.sh`

```bash
#!/bin/bash
# This script runs after certificate renewal

# Reload Nginx
systemctl reload nginx

# Notify user
echo "SSL certificate renewed at $(date)" >> /var/log/certbot-renewal.log

# Optional: Notify via webhook
curl -X POST https://yourdomain.com/webhook/ssl-renewed
```

**Make executable:**
```bash
chmod +x /usr/local/bin/certbot-renewal-hook.sh
```

**Update certbot config:**
```bash
# /etc/letsencrypt/renewal/yourdomain.com.conf
renew_hook = /usr/local/bin/certbot-renewal-hook.sh
```

---

## 📊 Certificate Information

### View Certificate Details

```bash
# View fullchain certificate
sudo openssl x509 -in /etc/letsencrypt/live/yourdomain.com/fullchain.pem -text -noout

# Check expiry date
sudo openssl x509 -enddate -noout -in /etc/letsencrypt/live/yourdomain.com/fullchain.pem
```

### Export Certificate

```bash
# Copy for use elsewhere
sudo cat /etc/letsencrypt/live/yourdomain.com/fullchain.pem
sudo cat /etc/letsencrypt/live/yourdomain.com/privkey.pem
```

---

## 🆘 Troubleshooting

### Certificate Not Renewing

```bash
# Check renewal log
sudo cat /var/log/letsencrypt/letsencrypt.log

# Force renewal
sudo certbot renew --force-renewal

# Debug renewal
sudo certbot renew --dry-run -v
```

### Certbot Can't Reach Domain

```bash
# Check DNS
nslookup yourdomain.com

# Check firewall (port 80 must be open)
sudo ufw status
sudo ufw allow 80
sudo ufw allow 443
```

### Wrong Certificate Renewed

```bash
# Revoke certificate
sudo certbot revoke --cert-path /etc/letsencrypt/live/yourdomain.com/fullchain.pem

# Get new one
sudo certbot certonly --nginx -d yourdomain.com
```

---

## 🔐 Security Best Practices

### 1. Update Permissions

```bash
# Restrict certificate access
sudo chmod 700 /etc/letsencrypt/live/
sudo chmod 700 /etc/letsencrypt/archive/
```

### 2. Backup Certificates

```bash
# Backup Let's Encrypt directory
sudo tar -czf letsencrypt-backup.tar.gz /etc/letsencrypt/

# Restore if needed
sudo tar -xzf letsencrypt-backup.tar.gz -C /
```

### 3. Monitor Certificate Expiry

```bash
# Email notification setup
sudo certbot renew --email your-email@example.com

# Or use monitoring service
sudo certbot register --update-registration --email your-email@example.com
```

---

## 📈 Performance Tips

### HTTP/2 Support

Nginx config already includes:
```nginx
listen 443 ssl http2;
```

### HSTS (HTTP Strict Transport Security)

Already configured:
```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```

### Enable OCSP Stapling

```nginx
# Add to SSL server block
ssl_stapling on;
ssl_stapling_verify on;
ssl_trusted_certificate /etc/letsencrypt/live/yourdomain.com/chain.pem;
```

---

## 🎓 Testing Commands

```bash
# Test SSL grade (using SSLLabs)
curl https://api.ssllabs.com/api/v3/analyze?host=yourdomain.com

# Test certificate chain
openssl s_client -connect yourdomain.com:443

# Test TLS version support
nmap --script ssl-enum-ciphers -p 443 yourdomain.com

# Performance test
# Use: https://www.ssllabs.com/ssltest/
```

---

## ✅ Checklist

- [ ] Certbot installed
- [ ] Domain DNS configured
- [ ] Nginx configuration created
- [ ] Initial certificate obtained
- [ ] HTTPS working (https://yourdomain.com)
- [ ] HTTP redirects to HTTPS
- [ ] Auto-renewal configured
- [ ] Monitoring setup
- [ ] Backups taken
- [ ] Security headers enabled

---

## 📞 Resources

- [Let's Encrypt](https://letsencrypt.org/)
- [Certbot Documentation](https://certbot.eff.org/docs/)
- [Nginx SSL](https://nginx.org/en/docs/http/ngx_http_ssl_module.html)
- [SSL/TLS Best Practices](https://mozilla.github.io/server-side-tls/ssl-config-generator/)

---

**SSL/HTTPS setup complete! 🔒**
