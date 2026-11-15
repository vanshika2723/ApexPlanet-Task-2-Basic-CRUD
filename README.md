# Blog CRUD Application — ApexPlanet Task 2

A simple and responsive **Blog Management System** built using PHP and MySQL as part of the ApexPlanet Web Development Internship.

## 📌 Project Overview

The Blog CRUD Application allows users to register, log in, and manage blog posts. It demonstrates CRUD operations, database connectivity, session-based authentication, and password hashing using PHP.

## 🚀 Features

- **User Registration:** Create an account with a username and password.
- **Secure Password Storage:** Passwords are hashed using PHP's `password_hash()`.
- **User Login:** Authenticate users using `password_verify()`.
- **Session Management:** Protect the dashboard and post management pages.
- **Create Posts:** Add new blog posts with a title and content.
- **Read Posts:** View all saved blog posts on the dashboard.
- **Update Posts:** Edit existing post titles and content.
- **Delete Posts:** Delete posts after confirmation.
- **Responsive Design:** CSS styling for desktop and mobile screens.
- **Database Integration:** Store user accounts and posts in MySQL.

## 🛠️ Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- XAMPP
- Git and GitHub

## 📂 Project Structure

```text
Blog-CRUD-App/
├── db.php
├── register.php
├── login.php
├── dashboard.php
├── create.php
├── edit.php
├── delete.php
├── logout.php
├── style.css
└── README.md
```

## ⚙️ Installation and Setup

### 1. Install XAMPP

Install XAMPP and start the **Apache** and **MySQL** services.

### 2. Copy the Project

Place the project folder inside the XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\Blog-CRUD-App
```

### 3. Create the Database

Open phpMyAdmin:

http://localhost/phpmyadmin

Create a database named `blog`, then execute:

```sql
CREATE DATABASE IF NOT EXISTS blog
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE blog;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4. Configure the Database

Open `db.php` and check the database connection settings. The default configuration is for a local XAMPP installation with MySQL username `root` and a blank password.

Update the credentials if your local MySQL configuration differs.

### 5. Run the Application

Open your browser and visit:

```text
http://localhost/Blog-CRUD-App/register.php
```

Register an account, log in, and start managing blog posts.

## 🗄️ Database Schema

### Users Table

| Column | Description |
|---|---|
| `id` | Unique user ID |
| `username` | Unique username |
| `password` | Hashed password |

### Posts Table

| Column | Description |
|---|---|
| `id` | Unique post ID |
| `title` | Blog post title |
| `content` | Blog post content |
| `created_at` | Post creation timestamp |

## 🔐 Security Practices

- Password hashing with `password_hash()`.
- Password verification with `password_verify()`.
- Prepared statements for database queries.
- Session checks on protected pages.
- HTML escaping with `htmlspecialchars()`.

**Note:** This is an internship learning project. CSRF protection and post ownership authorization should be added before production use.

## 🎯 Learning Outcomes

- Understanding PHP and MySQL integration.
- Implementing CRUD operations.
- Creating registration and login functionality.
- Managing user sessions.
- Using prepared statements for database queries.
- Building responsive layouts with CSS.
- Using Git and GitHub for version control.

## 👩‍💻 Internship

**Organization:** ApexPlanet Software Pvt. Ltd.  
**Task:** Task 2 — Basic CRUD Application  
**Project:** Blog Management System  
**Developer:** Vanshika Khandelwal

## 📄 License

This project was developed for educational and internship purposes.
