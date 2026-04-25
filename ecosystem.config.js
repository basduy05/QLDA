{
  "apps": [
    {
      "name": "aperlex-backend",
      "script": "npm",
      "args": "run start",
      "cwd": "./server",
      "instances": "max",
      "exec_mode": "cluster",
      "env": {
        "NODE_ENV": "production",
        "PORT": 3000
      },
      "error_file": "./logs/backend-error.log",
      "out_file": "./logs/backend-out.log",
      "log_file": "./logs/backend-combined.log",
      "time": true,
      "merge_logs": true,
      "max_memory_restart": "1G",
      "restart_delay": 5000,
      "exp_backoff_restart_delay": 100,
      "max_restarts": 10,
      "min_uptime": "10s",
      "listen_timeout": 10000,
      "kill_timeout": 5000,
      "watch": false,
      "ignore_watch": [
        "node_modules",
        "logs",
        "dist",
        ".next"
      ],
      "env_production": {
        "NODE_ENV": "production"
      },
      "env_development": {
        "NODE_ENV": "development"
      }
    }
  ],
  "deploy": {
    "production": {
      "user": "node",
      "host": "your-server-ip",
      "ref": "origin/main",
      "repo": "https://github.com/your-username/QLDUAN.git",
      "path": "/var/www/QLDUAN",
      "post-deploy": "npm install && npm run build && pm2 reload ecosystem.config.js --env production"
    },
    "staging": {
      "user": "node",
      "host": "staging-server-ip",
      "ref": "origin/develop",
      "repo": "https://github.com/your-username/QLDUAN.git",
      "path": "/var/www/QLDUAN-staging",
      "post-deploy": "npm install && npm run build && pm2 reload ecosystem.config.js --env staging"
    }
  }
}
