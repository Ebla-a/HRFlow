# HRFlow

### Human Resources Management System

HRFlow is a modular **Backend API** for Human Resources Management.

The system centralizes employee lifecycle management, organizational structure, leave management, attendance, payroll processing, performance reviews, notifications, and HR reporting in one secure and maintainable backend.

HRFlow is designed around the complete employee lifecycle — from hiring a new employee and managing their salary structure to leave requests, attendance, payroll processing, performance evaluation, and termination.

---

## 📋 Table of Contents

* [Project Overview](#-project-overview)
* [Key Features](#-key-features)
* [System Roles](#-system-roles)
* [Architecture](#-architecture)
* [Employee Hiring Workflow](#-employee-hiring-workflow)
* [Requirements](#️-requirements)
* [Installation & Setup](#-installation--setup)
* [Environment Configuration](#-environment-configuration)
* [Authentication](#-authentication)
* [Database Structure](#-database-structure)
* [API Documentation](#-api-documentation)
* [Business Rules](#-business-rules)
* [Leave Approval Workflow](#-leave-approval-workflow)
* [Payroll Processing](#-payroll-processing)
* [Events, Listeners & Jobs](#-events-listeners--jobs)
* [Caching](#-caching)
* [Testing](#-testing)
* [Postman Collection](#-postman-collection)
* [Project Structure](#-project-structure)
* [Development Phases](#-development-phases)
* [Security](#-security)
* [Development Team](#-development-team)
* [License](#-license)

---

# 📌 Project Overview

In many organizations, HR operations are distributed across spreadsheets, messaging applications, emails, and manual payroll processes.

This makes employee information difficult to manage consistently and increases the possibility of errors in areas such as leave balances, attendance, and payroll calculations.

HRFlow addresses this problem by providing a centralized backend system where HR operations are connected and managed through well-defined business workflows.

The system provides:

* Employee lifecycle management
* Organizational structure management
* Leave management
* Leave approval workflows
* Leave balance management
* Attendance tracking
* Salary structures
* Salary history
* Payroll processing
* Payslip management
* Performance reviews
* Notifications
* Background jobs
* HR reports
* Role-based authorization
* RESTful API

The system requirements define the HR domain, roles, workflows, API endpoints, business rules, testing requirements, and implementation phases.

---

# ✨ Key Features

## 👤 Employee Management

HRFlow manages the employee lifecycle from hiring to termination.

Features include:

* Hire new employees
* Create and associate user accounts
* Generate unique employee numbers
* Assign departments
* Assign job titles
* Assign direct managers
* Track employment type
* Track employee status
* Store personal information
* Upload employee documents
* Maintain salary history
* Terminate employees without deleting their records

Employee records remain available after termination for historical and reporting purposes.

---

## 🏢 Organizational Structure

HRFlow supports hierarchical organizational structures.

Features include:

* Parent and child departments
* Unique department codes
* Department managers
* Job titles
* Job title grades
* Department/job-title relationships

Departments can contain nested departments, allowing the system to represent organizational hierarchies.

---

## 🏖️ Leave Management

The Leave module supports:

* Multiple leave types
* Annual leave allocation
* Paid and unpaid leave
* Leave balances
* Leave requests
* Two-stage approval
* Automatic leave-day calculation
* Overlapping request prevention
* Medical document requirements
* Automatic balance deduction

The system prevents employees from submitting leave requests when their remaining balance is insufficient.

---

## 🕒 Attendance

The Attendance module provides:

* Daily attendance records
* Check-in time
* Check-out time
* Attendance status
* Attendance notes
* Employee attendance history
* Department attendance summaries
* Automatic absence registration

## The system can automatically register employees as absent when no attendance record exists for the day.

## 💰 Payroll

The Payroll module supports:

* Salary structures
* Basic salary
* Housing allowance
* Transport allowance
* Other allowances
* Salary history
* Monthly payroll runs
* Payroll processing
* Payroll finalization
* Payslips
* Deductions
* Unpaid leave deductions
* Read-only finalized payslips

Payroll runs follow the following lifecycle:

```text
Draft → Processing → Finalized
```

The payroll requirements define these three states and the rules governing processing and finalization.

---

## 📊 Performance Reviews

The Performance module supports:

* Performance cycles
* Draft / Active / Closed states
* Manager evaluations
* Overall ratings
* Strengths
* Areas for improvement
* Goals
* Employee performance history
* Employee access to results after cycle closure

Managers can only evaluate employees within their permitted organizational scope.

---

## 🔔 Notifications

HRFlow supports notifications for important business events, including:

* New employee welcome notification
* Leave approval/rejection notification
* Manager notification for pending leave approval
* Payroll publication notification

---

## 📈 Reports

The system provides HR reports such as:

* Headcount
* Payroll Summary
* Leave Utilization
* Attendance Overview
* Employee Turnover
* Performance Summary

---

# 👥 System Roles

HRFlow contains three main roles:

| Role                   | Description                                                     |
| ---------------------- | --------------------------------------------------------------- |
| **HR Admin**           | Full HR system access                                           |
| **Department Manager** | Manages employees and workflows within the permitted department |
| **Employee**           | Accesses personal HR information and self-service features      |

---

## 🔴 HR Admin

HR Admin has full access to HR operations.

Main responsibilities:

* Manage departments
* Manage job titles
* Hire employees
* Manage employees
* Manage salary structures
* Process payroll
* Finalize payroll
* Manage leave types
* Handle final leave approvals
* Create performance cycles
* View reports

---

## 🟡 Department Manager

Department Managers can:

* View employees within their permitted scope
* Approve or reject leave requests
* Perform performance reviews
* View department attendance summaries

Managers do not have access to payroll information.

---

## 🟢 Employee

Employees can:

* View their personal information
* Update their permitted information
* Submit leave requests
* View leave balances
* Track leave request status
* View attendance
* View payslips
* View performance results after the cycle is closed

---

# 🏗️ Architecture

HRFlow follows a **Modular Backend Architecture**.

The project is a backend-only application and does not contain a frontend application.

Business domains are separated into modules so that each module owns its responsibilities and business rules.

### Main Architecture Principles

* Modular Architecture
* SOLID Principles
* Object-Oriented Programming
* Dependency Injection
* Separation of Concerns
* Repository Pattern where applicable
* Actions for business operations
* Services for application-level orchestration
* DTOs for structured data transfer
* API Resources
* Form Requests
* Policies
* Events & Listeners
* Observers
* Queued Jobs
* Scheduled Jobs
* Enums
* Automated Testing

---

# 🔄 Employee Hiring Workflow

Employee hiring is implemented as a **cross-module event-driven workflow**.

The Employee module is responsible for the employee hiring operation and dispatches an event when the employee has been successfully hired.

Other modules can react to that event through dedicated listeners.

### Hiring Flow

```text
HR Admin
    │
    ▼
Hire Employee Action
    │
    ▼
Employee Created
    │
    ▼
EmployeeHired Event
    │
    ├──────────────► User-related Listener
    │
    ├──────────────► Payroll Listener
    │
    └──────────────► Notification Listener
```

This prevents the Employee module from becoming tightly coupled to the internal implementation of other modules.

### Why Events & Listeners?

This approach provides:

* Loose coupling
* Clear module boundaries
* Separation of responsibilities
* Easier testing
* Easier maintenance
* Easier extension

For example, a future module can react to `EmployeeHired` without requiring changes to the core employee hiring workflow.

The original system requirements also define event-driven behavior for employee and payroll workflows and queued welcome notifications.

---

# ⚙️ Requirements

| Component       | Version        |
| --------------- | -------------- |
| PHP             | 8.4+           |
| Laravel         | 13.x           |
| Composer        | 2.x            |
| Database        | SQLite / MySQL |
| Laravel Sanctum | Authentication |

> Required PHP extensions must be enabled according to the installed Laravel and Composer packages.

---

# 🚀 Installation & Setup

## 1. Clone the Repository

```bash
git clone https://github.com/Ebla-a/HRFlow.git
cd HRFlow
```

---

## 2. Install Dependencies

```bash
composer install
```

---

## 3. Configure Environment

Copy the environment file.

### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

### Linux / macOS

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

---

## 4. Configure Database

### SQLite

```env
DB_CONNECTION=sqlite
```

Create the database:

```bash
New-Item database/database.sqlite -ItemType File
```

### MySQL

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrflow
DB_USERNAME=root
DB_PASSWORD=
```

---

## 5. Run Migrations

```bash
php artisan migrate
```

---

## 6. Run Seeders

```bash
php artisan db:seed
```

For a fresh development database:

```bash
php artisan migrate:fresh --seed
```

> Do not use `migrate:fresh` on production databases.

---

## 7. Storage

Create the storage link if employee documents are stored through Laravel's public storage:

```bash
php artisan storage:link
```

---

## 8. Start the API

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000
```

---

# 🔧 Environment Configuration

Important environment variables include:

```env
APP_NAME=HRFlow
APP_ENV=local
APP_KEY=
APP_DEBUG=true

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

CACHE_STORE=
QUEUE_CONNECTION=
```

Environment-specific values should be configured inside `.env.example`.

Never commit the actual `.env` file or production credentials to the repository.

---

# 🔐 Authentication

HRFlow uses **Laravel Sanctum** for API authentication.

Protected endpoints require:

```http
Authorization: Bearer {token}
```

All API endpoints use the `/api/v1` prefix.

Example:

```http
GET /api/v1/auth/me
Authorization: Bearer {token}
```

The authentication endpoints are:

| Method | Endpoint                | Access        |
| ------ | ----------------------- | ------------- |
| POST   | `/auth/login`           | Public        |
| POST   | `/auth/logout`          | Authenticated |
| GET    | `/auth/me`              | Authenticated |
| PUT    | `/auth/password`        | Authenticated |
| POST   | `/auth/forgot-password` | Public        |
| POST   | `/auth/reset-password`  | Public        |

---

# 🗄️ Database Structure

The system is divided into several business domains.

## Users

Stores authentication and account information.

```text
users
├── id
├── name
├── email
├── password
├── status
├── created_at
└── updated_at
```

---

## Departments

```text
departments
├── id
├── name
├── code
├── parent_id
├── manager_id
├── created_at
└── updated_at
```

---

## Job Titles

```text
job_titles
├── id
├── department_id
├── name
├── grade
├── created_at
└── updated_at
```

---

## Employees

```text
employees
├── id
├── user_id
├── employee_number
├── department_id
├── job_title_id
├── manager_id
├── employment_type
├── status
├── hire_date
├── termination_date
├── termination_reason
├── identity_number
├── phone
└── address
```

---

## Employee Documents

```text
employee_documents
├── id
├── employee_id
├── uploaded_by
├── title
├── type
└── file_path
```

---

## Leave

Main entities:

```text
leave_types
leave_balances
leave_requests
```

---

## Attendance

```text
attendance
```

Stores daily employee attendance information.

---

## Payroll

Main payroll entities include:

```text
salary_structures
salary_histories
salary_history_items
payroll_runs
payslips
payslip_deductions
```

---

## Performance

```text
performance_cycles
performance_reviews
```

---

# 🌐 API Documentation

All endpoints are prefixed with:

```text
/api/v1
```

Authentication:

```http
Authorization: Bearer {token}
```

The API specification defines authentication, organizational structure, employees, leave, attendance, payroll, performance, and reporting endpoints.

---

## Departments & Job Titles

| Method | Endpoint            | Access             |
| ------ | ------------------- | ------------------ |
| GET    | `/departments`      | Authenticated      |
| POST   | `/departments`      | HR Admin           |
| GET    | `/departments/{id}` | HR Admin / Manager |
| PUT    | `/departments/{id}` | HR Admin           |
| DELETE | `/departments/{id}` | HR Admin           |
| GET    | `/job-titles`       | Authenticated      |
| POST   | `/job-titles`       | HR Admin           |
| PUT    | `/job-titles/{id}`  | HR Admin           |
| DELETE | `/job-titles/{id}`  | HR Admin           |

---

## Employees

| Method | Endpoint                         | Access             |
| ------ | -------------------------------- | ------------------ |
| GET    | `/employees`                     | HR Admin / Manager |
| POST   | `/employees`                     | HR Admin           |
| GET    | `/employees/{id}`                | HR Admin / Manager |
| PUT    | `/employees/{id}`                | HR Admin           |
| POST   | `/employees/{id}/terminate`      | HR Admin           |
| GET    | `/employees/{id}/salary-history` | HR Admin           |
| POST   | `/employees/{id}/documents`      | HR Admin           |
| GET    | `/employees/{id}/documents`      | HR Admin / Manager |
| GET    | `/employees/me`                  | Employee           |
| PUT    | `/employees/me`                  | Employee           |

---

## Leave Management

| Method | Endpoint                        | Access             |
| ------ | ------------------------------- | ------------------ |
| GET    | `/leave-types`                  | Authenticated      |
| POST   | `/leave-types`                  | HR Admin           |
| PUT    | `/leave-types/{id}`             | HR Admin           |
| GET    | `/leave-requests`               | Authenticated      |
| POST   | `/leave-requests`               | Employee           |
| GET    | `/leave-requests/{id}`          | Authenticated      |
| POST   | `/leave-requests/{id}/approve`  | Manager / HR Admin |
| POST   | `/leave-requests/{id}/reject`   | Manager / HR Admin |
| DELETE | `/leave-requests/{id}`          | Employee           |
| GET    | `/leave-requests/balance`       | Employee           |
| GET    | `/employees/{id}/leave-balance` | HR Admin / Manager |

---

## Attendance

| Method | Endpoint              | Access             |
| ------ | --------------------- | ------------------ |
| GET    | `/attendance`         | HR Admin / Manager |
| POST   | `/attendance`         | HR Admin           |
| PUT    | `/attendance/{id}`    | HR Admin           |
| GET    | `/attendance/my`      | Employee           |
| GET    | `/attendance/summary` | Manager / HR Admin |

---

## Payroll

| Method | Endpoint                      | Access              |
| ------ | ----------------------------- | ------------------- |
| GET    | `/salary-structures`          | HR Admin            |
| POST   | `/salary-structures`          | HR Admin            |
| PUT    | `/salary-structures/{id}`     | HR Admin            |
| GET    | `/payroll-runs`               | HR Admin            |
| POST   | `/payroll-runs`               | HR Admin            |
| POST   | `/payroll-runs/{id}/process`  | HR Admin            |
| POST   | `/payroll-runs/{id}/finalize` | HR Admin            |
| GET    | `/payroll-runs/{id}/payslips` | HR Admin            |
| GET    | `/payslips/{id}`              | HR Admin / Employee |
| GET    | `/payslips/my`                | Employee            |
| POST   | `/payslips/{id}/deductions`   | HR Admin            |

---

## Performance

| Method | Endpoint                            | Access             |
| ------ | ----------------------------------- | ------------------ |
| GET    | `/performance-cycles`               | Authenticated      |
| POST   | `/performance-cycles`               | HR Admin           |
| POST   | `/performance-cycles/{id}/activate` | HR Admin           |
| POST   | `/performance-cycles/{id}/close`    | HR Admin           |
| GET    | `/performance-reviews`              | HR Admin / Manager |
| POST   | `/performance-reviews`              | Manager            |
| PUT    | `/performance-reviews/{id}`         | Manager            |
| GET    | `/employees/{id}/performance`       | HR Admin / Manager |
| GET    | `/performance-reviews/my`           | Employee           |

---

## Reports

| Method | Endpoint                       | Access   |
| ------ | ------------------------------ | -------- |
| GET    | `/reports/headcount`           | HR Admin |
| GET    | `/reports/payroll-summary`     | HR Admin |
| GET    | `/reports/leave-utilization`   | HR Admin |
| GET    | `/reports/attendance-overview` | HR Admin |
| GET    | `/reports/turnover`            | HR Admin |
| GET    | `/reports/performance-summary` | HR Admin |

---

# 📐 Business Rules

## Employee

* A new employee receives a user account.
* Employee numbers are unique.
* An employee cannot be deleted as part of normal termination.
* Termination stores the date and reason.
* Terminated employees cannot log in.
* A job title must belong to the employee's department.
* Employee historical information remains available after termination.

---

## Leave

* Leave days are calculated automatically.
* Overlapping leave requests are not allowed.
* Employees cannot request more days than their available balance.
* Rejected leave does not reduce the balance.
* Sick leave exceeding two days requires a medical document.
* Employees without a manager are routed directly to HR Admin.
* Approved leave updates the employee's leave balance.

---

## Payroll

* Only one payroll run can exist for the same month.
* Active employees must have salary structures before finalization.
* Deductions can only be added before finalization.
* Finalized payslips are read-only.

---

## Performance

* Managers can evaluate employees within their permitted scope.
* Closed performance cycles cannot receive new evaluations.
* An employee cannot receive multiple evaluations from the same manager within the same cycle.

---

# 🏖️ Leave Approval Workflow

Leave requests follow a two-stage approval workflow.

### Employee With Manager

```text
Employee
    │
    ▼
Department Manager
    │
    ▼
HR Admin
    │
    ▼
Approved
```

### Employee Without Manager

```text
Employee
    │
    ▼
HR Admin
    │
    ▼
Approved
```

This workflow is defined by the HRFlow business requirements.

---

# 💰 Payroll Processing

Payroll follows three states:

```text
Draft
  │
  ▼
Processing
  │
  ▼
Finalized
```

## Net Salary

```text
Net Salary =
Basic Salary
+ Total Allowances
- Total Deductions
- Unpaid Leave Deduction
```

## Unpaid Leave Deduction

```text
Unpaid Leave Deduction =
(Basic Salary / 30) × Unpaid Leave Days
```

During processing, the system calculates the employee's salary according to the payroll rules.

A payroll run cannot be finalized if an active employee does not have a salary structure.

After finalization, payslips become read-only.

---

# 📨 Events, Listeners & Jobs

HRFlow uses Events, Listeners and Jobs to decouple business workflows.

## Employee Hiring Event

The employee hiring process dispatches an event that can be consumed by listeners in other modules.

```text
Employee Module
      │
      ▼
EmployeeHired
      │
      ├──────────────► User Module
      │
      ├──────────────► Payroll Module
      │
      └──────────────► Notification
```

This allows each module to handle its own responsibility without introducing unnecessary direct dependencies.

---

## Queued Operations

The system uses queued processing for operations such as:

* Welcome emails
* Leave notifications
* Payroll notifications

The technical requirements explicitly define queued operations for these workflows.

---

## Scheduled Jobs

### Daily Job

Registers absence for employees who do not have attendance records.

### Monthly Job

Creates leave balances for active employees.

---

# ⚡ Caching

HRFlow uses caching for frequently accessed or expensive data.

According to the technical requirements:

| Data                   |              Cache Duration |
| ---------------------- | --------------------------: |
| Departments            |                  60 minutes |
| Job Titles             |                  60 minutes |
| Reports                |                  15 minutes |
| Employee Leave Balance | With immediate invalidation |

Cache invalidation is performed when the underlying business data changes.

---

# 🧪 Testing

HRFlow follows an automated testing strategy.

## Feature Tests

Feature tests cover the main API endpoints and workflows.

Examples include:

* Authentication
* Employee management
* Leave management
* Payroll endpoints
* Performance endpoints
* Authorization

## Unit Tests

Unit tests cover important business logic such as:

* Payroll calculation
* Leave balance calculation
* Domain/business rules

The project specification targets a minimum test coverage of **70%**.

---

# 📮 Postman Collection

HRFlow is a **Backend API project only**.

There is no frontend application included in this repository.

API development and testing are performed through a Postman Collection.

The Postman Collection uses environment variables instead of hard-coded URLs and authentication tokens.

## Environment Variables

```text
base_url
hr_admin
manager
employee
```

---

## `base_url`

Defines the base URL of the HRFlow API.

Example:

```text
{{base_url}}/api/v1
```

This allows the collection to be reused across different environments.

---

## `hr_admin`

Contains the authentication token for the HR Admin account.

Example:

```http
Authorization: Bearer {{hr_admin}}
```

---

## `manager`

Contains the authentication token for the Manager account.

Example:

```http
Authorization: Bearer {{manager}}
```

---

## `employee`

Contains the authentication token for the Employee account.

Example:

```http
Authorization: Bearer {{employee}}
```

---

## Postman Collection Structure

```text
HRFlow API
│
├── Auth
│
├── Departments
│
├── Job Titles
│
├── Employees
│
├── Leave
│
├── Attendance
│
├── Payroll
│
├── Performance
│
└── Reports
```

### Example Request

```http
GET {{base_url}}/api/v1/employees
```

With HR Admin authentication:

```http
Authorization: Bearer {{hr_admin}}
```

With Manager authentication:

```http
Authorization: Bearer {{manager}}
```

The same collection can therefore be used to verify role-based authorization without manually changing tokens in every request.

---

# 📁 Project Structure

HRFlow follows a modular project structure.

```text
Modules/
│
├── Auth/
│
├── User/
│
├── Department/
│
├── Employee/
│
├── Leave/
│
├── Attendance/
│
├── Payroll/
│
└── Performance/
```

A module may contain components such as:

```text
Module/
├── App/
│   ├── Actions/
│   ├── DTOs/
│   ├── Enums/
│   ├── Events/
│   ├── Exceptions/
│   ├── Listeners/
│   ├── Services/
│   └── ...
│
├── Entities/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
│
├── Policies/
├── Providers/
├── Database/
│   ├── Migrations/
│   └── Seeders/
└── Tests/
```

The exact contents of each module depend on its business responsibilities.

---

# 🔒 Security

HRFlow handles sensitive HR and payroll-related information, therefore authorization is applied at multiple levels.

Security mechanisms include:

* Laravel Sanctum
* Bearer token authentication
* Role-based access control
* Permissions
* Policies
* Middleware
* Form Request validation
* Resource authorization
* Employee ownership restrictions
* Department-level authorization
* Restricted payroll access

The specification requires Policies on resources and additional protection for payroll routes.

### Examples

An employee cannot access another employee's private information.

A manager cannot access payroll data.

Payroll operations are restricted to HR Admin.

Employees can access their own payslips but not other employees' payslips.

---

# 📦 API Standards

The API follows a consistent response structure:

```json
{
    "status": true,
    "message": "Operation completed successfully.",
    "data": {}
}
```

Error responses follow:

```json
{
    "status": false,
    "message": "Something went wrong.",
    "errors": {}
}
```

The API specification requires consistent response formatting, API Resources, Resource Collections, pagination, filtering and sorting.

---

# 🗺️ Development Phases

The original implementation plan is divided into seven phases:

| Phase   |  Days | Deliverables                                                |
| ------- | ----: | ----------------------------------------------------------- |
| Phase 1 |   1–3 | Project setup, migrations, authentication, roles & policies |
| Phase 2 |   4–7 | Departments, job titles, employees, termination             |
| Phase 3 |  8–12 | Leave types, requests, approval workflow, balances          |
| Phase 4 | 13–17 | Salary structures, payroll runs, payslips, calculations     |
| Phase 5 | 18–21 | Attendance and performance                                  |
| Phase 6 | 22–23 | Queues, events, observers, jobs, caching                    |
| Phase 7 | 24–25 | Tests, Postman Collection, README                           |

---

# 📚 Documentation

The project documentation covers:

* Project architecture
* Installation
* Environment configuration
* Authentication
* Roles & permissions
* Employee lifecycle
* Leave workflows
* Payroll processing
* Events & listeners
* Background jobs
* Caching
* API endpoints
* Testing
* Postman API testing

The project specification also requires an ERD diagram and Postman Collection as part of the final delivery.

---

# 👨‍💻 Development Team

## Team Leader / Backend Developer

**Ebla Zyab Ali**

Responsibilities include:

* Project architecture
* Modular architecture
* Backend development
* API architecture
* Core business logic
* Security
* Payroll architecture
* Employee lifecycle
* Code quality
* Technical decisions

## Team Members

| Role                            | Name                    |
| ------------------------------- | -------------           |
| Team Leader                     | Ebla Zyab Ali           |
| Assistant                       |Kinda Alabdullah         |
| Database Specialist /Backend    | Bedoor Ali Deep         |
| Backend Developer               | Shahd Al Esami          |
| Backend Developer               | Osama Mlikh             |

---


# 🏆 Acknowledgments

### 🎉 Special Thanks

**Focal X Agency**

For their commitment to student growth and learning opportunities.

### 👨‍🏫 Mentors

**Mr. Somar Kesen **

- Technical guidance
- Concept clarification
- Inspirational mentorship

**Mr. Youse Saleh **

- Support throughout development

### 👩‍💼 Supervisor

**Mr. Bsher Al-Mahayni **


### 🤝 Focal X Team

For building and supporting this educational journey.

### 👨‍💼 CEO & Founder

**Alaa Darwish**

# 🎯 Project Goals

HRFlow aims to demonstrate a maintainable and production-oriented backend architecture through:

* Modular architecture
* SOLID principles
* Clean separation of responsibilities
* Secure authorization
* Domain-oriented business logic
* Event-driven communication
* Asynchronous processing
* Scheduled jobs
* Caching
* Automated testing
* API standards
* Payroll domain modeling
* Maintainable HR workflows

---
## 📮 Postman Collection

The complete Postman API Collection is available here:

[Download HRFlow Postman Collection](https://github.com/Ebla-a/HRFlow/blob/main/docs/HRFlow.postman_collection.json)

# ⭐ HRFlow

### Human Resources Management System

**Manage employees. Automate HR operations. Process payroll with confidence.**
