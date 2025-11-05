# PLANNR - Event Management System

A comprehensive event management system built with Laravel, Livewire, and Flux UI.

## Features

- **Event Management** - Create and manage events with sessions, segments, and cues
- **Content Management** - Upload and version control content files
- **User & Role Management** - Assign users to events with specific roles and permissions
- **Contact Management** - Manage speakers and contacts with custom fields
- **Custom Fields** - Add custom fields to any model (events, sessions, segments, cues, content, speakers, contacts)
- **Comments & Mentions** - Collaborative commenting with user mentions
- **Tags** - Organize content with tags across all models
- **Version Control** - Track and restore previous versions of content files

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0 or higher
- Git

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/jivaericl/crewcall.git
cd crewcall
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plannr
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

**Important:** The `db:seed` command will create default roles required for the system:
- Admin
- Producer
- Content Producer
- Client
- Viewer

### 5. Create storage link

```bash
php artisan storage:link
```

### 6. Build assets

```bash
npm run build
```

For development:
```bash
npm run dev
```

### 7. Start the development server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## First Time Setup

1. Register a new account (first user becomes super admin)
2. Create your first event
3. Assign users to the event with roles
4. Start adding sessions, segments, and content

## Development

### Running tests

```bash
php artisan test
```

### Code style

```bash
./vendor/bin/pint
```

### Clear caches

```bash
php artisan optimize:clear
```

Or individually:
```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Production Deployment

### 1. Optimize for production

```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Set proper permissions

```bash
chmod -R 755 storage bootstrap/cache
```

### 3. Configure web server

Point your web server document root to the `public` directory.

**Nginx example:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/plannr/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Troubleshooting

### No roles in dropdown

Run the seeder:
```bash
php artisan db:seed --class=RolesSeeder
```

### Permission errors

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Assets not loading

```bash
npm run build
php artisan storage:link
```

### Livewire not updating

```bash
php artisan view:clear
php artisan livewire:discover
```

## Support

For issues and questions, please open an issue on GitHub.

## License

This project is proprietary software. All rights reserved.
