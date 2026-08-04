[![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php)]()
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql)]()
[![Python](https://img.shields.io/badge/Python-3.10%2B-3776AB?logo=python)]()
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)]()
[![Multi-Tenant SaaS](https://img.shields.io/badge/Architecture-Multi--Tenant-success)]()
# IRISERP – Dynamic Multi-Tenant SaaS ERP System

IRISERP is a **Multi-Tenant ERP and Student Management System** built with **PHP, MySQL, Bootstrap, AJAX, Python, and Flask**. It supports **both Schools and Colleges from a single codebase** and dynamically adapts its academic structure without requiring code changes.

---

## Problem Statement

Most ERP solutions in the education sector suffer from three major problems:

* A separate application and database is required for every institute.
* School and college structures are different, so separate systems must be developed.
* Large numbers of tables and duplicated data increase maintenance and storage complexity.

IRISERP was built specifically to solve these problems.

---

## What Makes IRISERP Unique?

### 1. Multi-Tenant SaaS Architecture

* Multiple institutes run on a **single application**.
* Data of different institutes is stored separately and never mixes.
* New institutes can be onboarded without creating a new deployment.

### 2. Dynamic Institute Architecture

Schools use:

* Class
* Section

Colleges use:

* Course
* Branch
* Semester

IRISERP automatically changes its structure according to the registered institute type. The same codebase supports both school and college workflows.

**No code modification is required when a new institute is added.**

### 3. Metadata-Driven Database Design

Instead of creating many separate tables and columns, the system uses a **metadata architecture** based on **key-value pairs**.

Benefits:

* Flexible schema
* Reduced database complexity
* Easier customization
* Better maintainability

---

# Key Features

## Student Management

* Student registration
* Profile management
* Academic records
* ID and profile management

## Academic Module

* Classes / Courses
* Sections / Branches
* Subjects
* Timetable management
* Lecturer allocation

## Attendance System

* QR-based attendance
* Face verification
* **Liveness detection before attendance marking**

### AI Attendance Pipeline

* Python
* Flask API
* MediaPipe
* QR Scanner integration

Attendance is marked only after successful liveness verification, reducing proxy attendance.

## Examination Module

* Exam creation
* Date sheet generation
* Admit card generation
* Result management
* GPA calculation
* PDF result generation

## Learning Management Features

* Study material upload
* Assignment upload
* Assignment submission tracking
* Timetable access

## Communication Features

* Notices and announcements
* Student updates
* Institute-wide notifications

## AI Chatbot

Integrated AI chatbot using **DeepSeek API**.

### Hybrid Chatbot Design

* Rule-based responses for common queries
* AI responses for complex queries

This hybrid approach significantly reduces token consumption and API cost.

---

# Performance Optimization

IRISERP uses **AJAX-based asynchronous operations** extensively.

Benefits:

* No full page refresh
* Faster user experience
* Reduced server load
* Real-time interactions

---

# Security Features

* Password hashing
* OTP-based password reset
* Session management
* Role-based access control (RBAC)
* CSRF protection
* Input validation and sanitization
* Secure authentication flow

---

# Technology Stack

| Layer            | Technology                                           |
| ---------------- | ---------------------------------------------------- |
| Frontend         | HTML5, CSS3, Bootstrap, AdminLTE, JavaScript, jQuery |
| Backend          | PHP                                                  |
| Database         | MySQL                                                |
| Async Operations | AJAX                                                 |
| AI / ML          | Python, MediaPipe                                    |
| API Layer        | Flask API                                            |
| AI Integration   | DeepSeek API                                         |
| Libraries        | DataTables, FullCalendar, PHPMailer, PhpSpreadsheet  |
| Environment      | XAMPP                                                |

---

# System Architecture

```text
Users
   ↓
PHP + AJAX Frontend
   ↓
Multi-Tenant Business Layer
   ↓
Metadata Engine (Key-Value Storage)
   ↓
MySQL Database
   ↓
Python Flask Services
   ↓
MediaPipe / AI Modules
```

---

# Database Strategy

Core tables include:

* institutes
* users
* usermeta
* students
* attendance
* exams
* results
* assignments
* study_material

The **usermeta** table stores dynamic attributes using a metadata approach.

---

# Installation

## Prerequisites

* PHP 8+
* MySQL 5.7+
* Python 3.10+
* Composer
* XAMPP / Apache

## Steps

```bash
git clone https://github.com/your-username/Multi-Tenant-Saas-Application.git
cd Multi-Tenant-Saas-Application
composer install
```

Import the database SQL file and configure database credentials in the config folder.

Start Apache, MySQL, and the Flask attendance API.

---

# Research & Engineering Contributions

This project demonstrates practical implementation of:

* Multi-tenant SaaS architecture
* Dynamic ERP modeling
* Metadata-driven database systems
* Hybrid AI chatbot optimization
* AI-assisted attendance verification
* PHP–Python interoperability
* REST API integration
* AJAX-driven real-time UI

---

# Future Enhancements

* Mobile application
* Parent portal
* Online fee payment gateway
* Cloud deployment with subdomain tenants
* Analytics dashboard
* AI-powered academic insights

---

# Project Impact

IRISERP transforms a traditional institute-specific ERP into a **configurable SaaS platform** that supports multiple educational organizations from a single deployment while maintaining data isolation, flexibility, security, and scalability.

---

# Author

**Archi Mittal**

* B.Tech (2026), Ajay Kumar Garg Engineering College
* Full Stack Developer

GitHub: https://github.com/archimittal0403

LinkedIn: https://www.linkedin.com/in/archi-mittal-177b18299/

Live DEMO : https://erpsm.innovesenjournals.in/student%20management/index.php

---

# License

This project is released under the **MIT License**.
