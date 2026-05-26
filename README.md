# Rapha Garden School - Website Documentation

---

## Overview

This is the official marketing website for Rapha Garden School, Athi River, Machakos County, Kenya. It is built with PHP and MySQL. It has a public-facing website and a password-protected admin panel for managing content without touching any code.

Current live URL (temporary free hosting): rapha.free.nf

---

## Technology

- PHP (server-side scripting)
- MySQL (database)
- HTML and CSS (structure and styling)
- JavaScript (navigation, gallery lightbox, hero slideshow)
- Apache .htaccess (clean URLs, security, caching)
- Google Fonts (Inter and Merriweather)

---

## File Structure

```
/
├── index.php               Homepage
├── about.php               About page (story, vision, team)
├── admissions.php          Admissions page (steps, requirements, forms)
├── apply.php               Online application form
├── fees.php                Fee structure page
├── gallery.php             Photo gallery page
├── news.php                News, events and notices page
├── contact.php             Contact form page
├── .htaccess               URL rewriting, clean URLs, caching
├── database.sql            Full database setup - run once on new server
├── reset_admin.php         Temporary password reset tool - delete after use
│
├── includes/
│   ├── config.php          Database credentials, connection, shared functions
│   ├── header.php          Site header and navigation bar (included on all pages)
│   └── footer.php          Site footer (included on all pages)
│
├── admin/
│   ├── login.php           Admin login page
│   ├── logout.php          Destroys session and redirects to login
│   ├── dashboard.php       Admin home showing stats and quick action links
│   ├── gallery.php         Upload and delete gallery photos
│   ├── news.php            Create, edit and delete news posts
│   ├── fees.php            Add, edit and delete fee records
│   ├── team.php            Add, edit and delete team member profiles
│   ├── forms.php           Upload and delete downloadable admission forms
│   ├── applications.php    View and manage admission applications
│   ├── contacts.php        View and reply to contact form messages
│   └── includes/
│       └── sidebar.php     Admin navigation sidebar
│
├── assets/
│   ├── css/style.css       All website styles including admin styles
│   └── js/main.js          Navigation, gallery lightbox, hero slideshow
│
└── uploads/
    ├── gallery/            Stores photos uploaded via admin gallery and team pages
    └── forms/              Stores PDF and Word files uploaded via admin forms page
```

---

## URL Structure

All public pages use clean URLs without the .php extension.

```
yourdomain.com/              Homepage
yourdomain.com/about         About page
yourdomain.com/admissions    Admissions page
yourdomain.com/apply         Online application form
yourdomain.com/fees          Fee structure
yourdomain.com/gallery       Photo gallery
yourdomain.com/news          News and events
yourdomain.com/contact       Contact page
```

The admin panel keeps the .php extension because it is excluded from clean URL rewriting.

```
yourdomain.com/admin/login.php      Admin login
yourdomain.com/admin/dashboard.php  Admin dashboard
```

The .htaccess file handles all URL rewriting. If clean URLs stop working, check that mod_rewrite is enabled on the server.

---

## Pages

### Homepage
Shows a hero section with a full-screen background slideshow, a features section, an about snippet, latest three news posts, a gallery preview and a call to action. The slideshow pulls up to five random photos from the gallery database. If no photos have been uploaded yet, the hero shows a solid green gradient background. The slideshow pauses on mouse hover and has clickable gold dot indicators at the bottom.

### About
Three sections: Our Story (static text), Vision and Mission (static text), and Our Team (pulled from the team database table, managed via admin). The Our Team section shows a message if no team members have been added yet.

### Admissions
Step by step guide to applying, list of required documents, age requirements table, FAQ accordion, and a downloadable forms section that pulls from the forms database table. The Apply Now button links to the online application form.

### Apply
Online application form for parents to submit their child's details. Collects student name, date of birth, grade applying for, previous school, parent name, phone, email, home address, and additional information. Submissions are saved to the applications table and appear in the admin panel.

### Fees
Displays the fee structure table from the database grouped by grade level. Shows tuition amount, levies, total per term, what is included in fees, payment methods and payment deadlines. If no fee records are in the database, placeholder data is shown.

### Gallery
Uniform image grid where every photo is the same size (4:3 aspect ratio, object-fit cover). Has category filter tabs for All, Classroom, Sports, Events, and Facilities. Clicking an image opens a full-screen lightbox.

### News
Lists all posts filtered by category (News, Events, Notices). Clicking a post opens the full article on the same page. If no posts exist a placeholder message is shown.

### Contact
Contact form that saves submissions to the contacts database table. Also shows the school address, phone, email, office hours and a WhatsApp direct chat link.

---

## Admin Panel

Access at: yourdomain.com/admin/login.php

Default credentials after importing database.sql:
- Username: admin
- Password: password

Change the password immediately by visiting yourdomain.com/reset_admin.php before using the site publicly. Delete reset_admin.php from the server straight after changing the password.

### Gallery
Upload multiple photos at once by selecting several files. Assign a category and optional title. Photos appear on the gallery page and are also used automatically in the homepage hero slideshow. Delete photos individually.

### News and Events
Create posts with a title, category (News, Events, or Notices), a short excerpt shown on cards, full article content, and an optional featured image. Edit or delete any post.

### Fee Structure
Add fee records per grade level and per term. Set tuition amount, other levies, total amount and notes. If the total is left blank it is calculated automatically. Edit or delete individual records. Records are displayed grouped by grade on the fees page.

### Our Team
Add team members with their full name, role or title, description, an emoji icon, optional photo, and a display order number. Lower display order numbers appear first on the About page. Photos are stored in uploads/gallery/.

### Download Forms
Upload PDF or Word document forms for parents to download. Set a title, description and level (Pre-Primary, Primary, JSS). Files appear on the Admissions page. Delete removes both the database record and the file from the server.

### Applications
View all online applications from the Apply page. Summary cards show total, new, accepted and rejected counts. Filter the list by status. Click View to see full application details including all student and parent information. Update the status (New, Reviewed, Accepted, Rejected) on the detail page. Email the parent directly using the email link. Delete applications when no longer needed.

### Messages
View all contact form submissions. Each message shows the sender name, email, phone, subject and message text. Reply by clicking the email link. Delete messages individually.

---

## Database

### Tables

admin_users - Admin login credentials. Passwords are stored as bcrypt hashes.

gallery - Photo records with filename, title and category.

news - News, event and notice posts with title, category, excerpt, content and optional image.

fees - Fee records per grade level and term with tuition, levies, total, year and notes.

team - Team member profiles with name, role, description, icon, optional photo and sort order.

forms - Downloadable form records with title, description, filename and level.

applications - Online admission applications with all student and parent details and a status field.

contacts - Contact form messages with name, email, phone, subject and message.

### Setup Instructions

For a fresh server:

1. Create a database in cPanel
2. Open phpMyAdmin and click the database name in the left sidebar to select it
3. Click the Import tab
4. Upload database.sql and click Go
5. Do not include or run the CREATE DATABASE line - InfinityFree and most shared hosts do not allow this via SQL. The database must be created through cPanel first.

Note: The team, forms and applications tables may need to be created separately if you already imported an earlier version of database.sql. Run the following in phpMyAdmin SQL tab:

```sql
CREATE TABLE IF NOT EXISTS team (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  role VARCHAR(150) NOT NULL,
  description TEXT,
  icon VARCHAR(20) DEFAULT '',
  photo VARCHAR(255),
  sort_order INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS forms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description VARCHAR(255),
  filename VARCHAR(255) NOT NULL,
  level VARCHAR(100),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(150) NOT NULL,
  date_of_birth DATE,
  grade_applying VARCHAR(50),
  parent_name VARCHAR(150) NOT NULL,
  parent_phone VARCHAR(30) NOT NULL,
  parent_email VARCHAR(150),
  address VARCHAR(255),
  previous_school VARCHAR(150),
  additional_info TEXT,
  status ENUM('new','reviewed','accepted','rejected') DEFAULT 'new',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

Note: Emoji characters cannot be used as MySQL column default values on InfinityFree. The icon column uses an empty string default and the emoji fallback is handled in PHP.

---

## Configuration

All site-wide settings are in includes/config.php at the top of the file.

```php
define('DB_HOST', '');        // Database host from your hosting panel
define('DB_USER', '');        // Database username
define('DB_PASS', '');        // Database password
define('DB_NAME', '');        // Database name

define('SITE_NAME', 'Rapha Garden School');
define('SITE_TAGLINE', 'Nurturing Excellence, Growing Futures');
define('SITE_PHONE', '+254 722 272 063');
define('SITE_EMAIL', 'info@raphagardenschool.ac.ke');
define('SITE_ADDRESS', 'Athi River, Machakos County, Kenya');
```

Update SITE_NAME, SITE_PHONE, SITE_EMAIL and SITE_ADDRESS whenever the school details change. These values appear automatically in the header, footer, contact page and all meta tags.

---

## Hosting Options

### Current Setup - InfinityFree (Free)

The site is currently live on InfinityFree free hosting at rapha.free.nf. This is suitable for testing but has limitations including query rate limits, slower speeds and occasional downtime.

Database host on InfinityFree is not localhost. It is a specific hostname shown in the cPanel, in the format sql___.epizy.com. Use this value for DB_HOST in config.php.

FTP details for uploading files:
- FTP host: ftpupload.net
- Username and password: found in InfinityFree cPanel
- Upload all files to the htdocs folder

### Recommended Upgrade - Hostway Kenya

Hostway Kenya offers the best value for a school website with a free domain included.

- Starter plan: KES 1,999 per year
- Includes free .co.ke domain registration
- 30 GB NVMe SSD storage
- Free SSL, daily backups, cPanel
- LiteSpeed servers (faster than standard Apache)
- M-Pesa payment accepted
- Website: hostway.co.ke

### Alternative - TrueHost Kenya Shared Hosting

- Starter plan: KES 2,500 per year
- Domain purchased separately: KES 999 registration, KES 1,200 renewal per year
- Total year one cost: KES 3,499
- Recommended domain: raphagardenschool.ac.ke (KES 1,000 registration, KES 1,500 renewal)
- Website: truehost.co.ke

### Moving to TrueHost or Hostway (from InfinityFree)

1. Purchase hosting and domain from chosen provider
2. Create a MySQL database in cPanel
3. Open phpMyAdmin, select the database, import database.sql
4. Update DB_HOST in config.php. On TrueHost and Hostway the host is localhost
5. Update DB_USER, DB_PASS and DB_NAME with the new credentials
6. Upload all files to public_html using FileZilla
7. FTP credentials are in the cPanel of the new host
8. Set uploads folder permission to 755 in FileZilla
9. Visit yourdomain/reset_admin.php to set the admin password
10. Delete reset_admin.php immediately after

The key difference from InfinityFree is that DB_HOST will be localhost instead of a sql___.epizy.com address.

### TrueHost VPS - For JavaScript Projects

If you need to run a Node.js backend, Next.js in server-side mode, or any persistent JavaScript process, shared hosting is not suitable. Use TrueHost VPS hosting which gives full root access and allows you to run Node.js applications permanently using PM2.

The Rapha Garden School website (PHP and MySQL) runs fine on shared hosting and does not require a VPS.

---

## Updating Content After Launch

Photos - Admin panel, Gallery section, upload images. They appear on the gallery page and in the homepage hero slideshow.

News - Admin panel, News and Events, New Post.

Fees - Admin panel, Fee Structure, Add Fee Record.

Team - Admin panel, Our Team, Add Member.

Forms - Admin panel, Download Forms, upload PDF.

Contact details, school name, phone, email - Edit constants in includes/config.php.

Colors - Edit the CSS variables at the top of assets/css/style.css under the Tokens section.

---

## Colors

Primary green: #1B5E20
Secondary green: #2E7D32
Gold accent: #F9A825
Background: #FFFFFF

---

## Known Issues and Limitations

InfinityFree has a MySQL query rate limit per hour. For a school marketing website with normal traffic this is unlikely to be reached. If the site grows, move to paid hosting.

The hero slideshow requires photos to be uploaded via the admin gallery first. Before any photos are uploaded the hero shows a plain green gradient background.

Emoji characters cannot be used as MySQL default values on InfinityFree. This affects the team icon column which uses an empty string as the database default. The emoji fallback is applied in PHP.

The admin panel pages keep the .php extension in the URL. This is intentional. Clean URLs are only applied to public-facing pages.

If the gallery or news sections show blank content, the most likely cause is a database connection error. Check that the credentials in includes/config.php match exactly what is shown in the cPanel. On InfinityFree the database name, username and password are all prefixed with the account ID.
