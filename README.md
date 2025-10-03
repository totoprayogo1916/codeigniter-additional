# CodeIgniter 4 Additional Utilities

This library provides a collection of useful helpers and Spark commands to enhance your CodeIgniter 4 development experience.

## Features

### 1. Improved `url_title()` Helper

This library overrides the default `url_title()` helper function, offering enhanced capabilities for generating URL-friendly strings.

### 2. Additional Spark Commands

A set of convenient commands have been added to help with common development tasks.

#### `db:wipe`

Drops all tables from your database. This is particularly useful during development and testing for quickly resetting your database schema.

**Usage:**
```bash
php spark db:wipe
```

#### `writable:link`

Creates a symbolic link from `writable/uploads` to your public folder (`FCPATH`). This makes files stored in the writable directory (like user uploads) publicly accessible.

**Usage:**
```bash
php spark writable:link
```

#### `sessions:clear`

Deletes all session files from the `writable/session` directory, while safely ignoring `index.html` and `.htaccess` files. This is useful for clearing out old or invalid session data.

**Usage:**
```bash
php spark sessions:clear
```