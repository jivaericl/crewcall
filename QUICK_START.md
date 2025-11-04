# PLANNR - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

This guide will help you get PLANNR up and running quickly.

---

## Prerequisites

Before you begin, ensure you have:
- PHP 8.1 or higher
- Composer
- Node.js 18+ and NPM
- MySQL or PostgreSQL database
- Web server (Apache/Nginx) or Laravel Herd/Valet

---

## Installation Steps

### 1. Extract the Package

```bash
tar -xzf plannr-complete-20251104.tar.gz
cd laravel-app
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install --legacy-peer-deps
```

### 3. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit `.env` and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plannr
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Publish Livewire Assets

```bash
php artisan vendor:publish --tag=livewire:assets --force
```

### 8. Create Storage Link (Optional)

```bash
php artisan storage:link
```

### 9. Start the Application

**Option A: Using Laravel's built-in server**
```bash
php artisan serve
```
Visit: http://localhost:8000

**Option B: Using Laravel Herd (Mac)**
- Already configured, just visit: http://plannr.test

**Option C: Using Valet (Mac/Linux)**
```bash
valet link plannr
```
Visit: http://plannr.test

---

## First Login

### Create Your First User

```bash
php artisan tinker
```

Then run:
```php
$user = App\Models\User::create([
    'name' => 'Admin User',
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@plannr.com',
    'password' => bcrypt('password'),
    'is_super_admin' => true,
]);
exit
```

**Login credentials:**
- Email: `admin@plannr.com`
- Password: `password`

---

## Quick Tour

### 1. Create Your First Event

1. Log in with the credentials above
2. Click "Events" in the navigation
3. Click "Create Event"
4. Fill in:
   - Event Name
   - Start Date
   - End Date
   - Timezone
5. Click "Save Event"

### 2. View Event Dashboard

1. After creating an event, you'll be redirected to the dashboard
2. You'll see:
   - Statistics (Sessions, Contacts, Team, Comments)
   - Upcoming and recent sessions
   - Team members
   - Key contacts
   - Recent activity

### 3. Add Contacts

1. Click "People" → "Contacts" in the sidebar
2. Click "Create Contact"
3. Fill in contact details:
   - First Name, Last Name
   - Contact Type (Client, Producer, etc.)
   - Company, Email, Phone
4. Click "Save Contact"

### 4. Create a Session

1. Click "Sessions" in the sidebar
2. Click "Create Session"
3. Fill in session details:
   - Session Name
   - Start/End Dates
   - Client (select from contacts)
   - Producer (select from contacts)
   - Tags, Speakers
4. Click "Save Session"

### 5. Manage Tags

1. Click "Tags" in the sidebar
2. Click "Create Tag"
3. Enter tag name and choose a color
4. Tags can be used across sessions, segments, speakers, contacts

---

## Common Tasks

### Adding Team Members to an Event

1. Go to Events list
2. Click "Team" button for your event
3. Click "Assign User" or "Assign First User"
4. Select user and role
5. Click "Assign"

### Creating Speakers

1. Select an event
2. Click "People" → "Speakers"
3. Click "Create Speaker"
4. Fill in speaker details
5. Optionally create a user account for them

### Viewing Audit Logs

1. Select an event
2. Click "Audit Log" in the sidebar
3. View all changes made to the event

---

## Tips & Tricks

### Event Selector
- Use the event dropdown in the top navigation to quickly switch between events
- Your selection is remembered across pages

### Hierarchical Navigation
- The sidebar shows event-specific navigation
- Click "People" to expand and see Speakers, Contacts, Team

### Search & Filter
- Most list pages have search and filter capabilities
- Use the search box to quickly find what you need

### Dark Mode
- The entire application supports dark mode
- It follows your system preferences

### Comments
- Add comments to events, sessions, segments, speakers, contacts
- Use @mentions to notify team members

---

## Troubleshooting

### "Class not found" errors
```bash
composer dump-autoload
php artisan config:clear
```

### "View not found" errors
```bash
php artisan view:clear
```

### Livewire not working
```bash
php artisan vendor:publish --tag=livewire:assets --force
php artisan cache:clear
```

### Assets not loading
```bash
npm run build
php artisan config:clear
```

### Database errors
- Make sure your database is running
- Check your `.env` database credentials
- Run migrations: `php artisan migrate`

---

## Production Deployment

### Important Steps for Production

1. **Set environment to production**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Use MySQL or PostgreSQL**
   - SQLite has limitations with foreign keys
   - MySQL 8+ or PostgreSQL 13+ recommended

3. **Configure cache**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Set up queue worker**
   ```bash
   php artisan queue:work --daemon
   ```

5. **Configure mail**
   - Set up SMTP or mail service in `.env`
   - Test email notifications

6. **Enable HTTPS**
   - Use SSL certificate (Let's Encrypt)
   - Force HTTPS in production

7. **Set up backups**
   - Daily database backups
   - File storage backups

---

## Next Steps

1. **Read the Complete Implementation Guide** - Detailed documentation of all features
2. **Customize** - Adjust colors, branding, settings to your needs
3. **Add Users** - Invite your team members
4. **Create Events** - Start planning your events
5. **Explore Features** - Try Run of Show, Segments, Cues, Content management

---

## Support & Documentation

- **Complete Implementation Guide:** `COMPLETE_IMPLEMENTATION_GUIDE.md`
- **Deployment Guide:** `DEPLOYMENT_GUIDE.md`
- **Laravel Documentation:** https://laravel.com/docs
- **Livewire Documentation:** https://livewire.laravel.com

---

## 🎉 You're Ready!

PLANNR is now installed and ready to use. Start by creating your first event and exploring the features!

**Happy Planning!** 🚀
