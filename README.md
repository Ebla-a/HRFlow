# HRFlow

### Human Resources Management System

HRFlow is a modular **Backend API** for Human Resources Management.

The system centralizes employee lifecycle management, organizational structure, leave management, attendance, payroll processing, performance reviews, notifications, reporting, and other HR operations in one secure and maintainable backend.

HRFlow is designed around the complete employee lifecycle — from hiring a new employee and managing their salary structure to leave requests, attendance, payroll processing, performance evaluation, reporting, and employee termination.

> **HRFlow is a Backend-only project. No frontend application is included.**

---

## 📋 Table of Contents

- [Project Overview](#-project-overview)
- [Key Features](#-key-features)
- [System Roles](#-system-roles)
- [Architecture](#-architecture)
- [Employee Hiring Workflow](#-employee-hiring-workflow)
- [Requirements](#️-requirements)
- [Installation & Setup](#-installation--setup)
- [Environment Configuration](#-environment-configuration)
- [Authentication](#-authentication)
- [Database Structure](#-database-structure)
- [API Documentation](#-api-documentation)
- [Reports & Excel Export](#-reports--excel-export)
- [Currency Exchange Integration](#-currency-exchange-integration)
- [Business Rules](#-business-rules)
- [Leave Approval Workflow](#-leave-approval-workflow)
- [Payroll Processing](#-payroll-processing)
- [Events, Listeners & Jobs](#-events-listeners--jobs)
- [Caching](#-caching)
- [Testing](#-testing)
- [Postman Collection](#-postman-collection)
- [API Response Standards](#-api-response-standards)
- [Project Structure](#-project-structure)
- [Development Phases](#-development-phases)
- [Security](#-security)
- [Development Team](#-development-team)
- [Acknowledgments](#-acknowledgments)
- [Project Goals](#-project-goals)
- [License](#-license)

---

# 📌 Project Overview

In many organizations, HR operations are distributed across spreadsheets, messaging applications, emails, and manual payroll processes.

This makes employee information difficult to manage consistently and increases the possibility of errors in areas such as employee management, leave balances, attendance, payroll, and reporting.

HRFlow addresses this problem by providing a centralized backend system where HR operations are connected and managed through well-defined business workflows.

The system provides:

- Employee lifecycle management
- Organizational structure management
- Department management
- Job title management
- Leave management
- Leave approval workflows
- Leave balance management
- Attendance tracking
- Salary structures
- Salary history
- Payroll processing
- Payslip management
- Performance reviews
- Notifications
- Background jobs
- HR reports
- Excel report export
- Currency exchange integration
- Role-based authorization
- Permission-based authorization
- RESTful API
- Event-driven communication between modules

---

## 🛠️ Main Technologies

| Technology | Purpose |
|------------|---------|
| PHP 8.4+ | Backend language |
| Laravel 13 | Backend framework |
| Laravel Sanctum | API authentication |
| MySQL / SQLite | Database |
| Spatie Permission | Roles & permissions |
| TranslaGenius | Automatic translation & localization |
| Google Gemini | AI Employee Assistant |
| Postman | API testing |
| PhpSpreadsheet | Excel report export |

# ✨ Key Features

## 👤 Employee Management

HRFlow manages the employee lifecycle from hiring to termination.

Features include:

- Hire new employees
- Create and associate user accounts
- Generate unique employee numbers
- Assign departments
- Assign job titles
- Assign direct managers
- Track employment type
- Track employee status
- Store personal information
- Upload employee documents
- Maintain salary history
- Terminate employees without deleting their records

Employee records remain available after termination for historical and reporting purposes.

---

## 🏢 Organizational Structure

HRFlow supports hierarchical organizational structures.

Features include:

- Parent and child departments
- Unique department codes
- Department managers
- Job titles
- Job title grades
- Department/job-title relationships
- Assigning managers to departments

Departments can contain nested departments, allowing the system to represent organizational hierarchies.

---

## 🏖️ Leave Management

The Leave module supports:

- Multiple leave types
- Annual leave allocation
- Paid and unpaid leave
- Leave balances
- Leave requests
- Two-stage approval
- Automatic leave-day calculation
- Overlapping request prevention
- Medical document requirements
- Automatic balance deduction

The system prevents employees from submitting leave requests when their remaining balance is insufficient.

---

## 🕒 Attendance

The Attendance module provides:

- Daily attendance records
- Check-in time
- Check-out time
- Attendance status
- Attendance notes
- Employee attendance history
- Department attendance summaries
- Automatic absence registration

The system can automatically register employees as absent when no attendance record exists for the day.

---

## 💰 Payroll

The Payroll module supports:

- Salary structures
- Basic salary
- Housing allowance
- Transport allowance
- Other allowances
- Salary history
- Monthly payroll runs
- Payroll processing
- Payroll finalization
- Payslips
- Deductions
- Unpaid leave deductions
- Read-only finalized payslips

Payroll runs follow the following lifecycle:

```text
Draft → Processing → Finalized
```

---

## 📊 Performance Reviews

The Performance module supports:

- Performance cycles
- Draft / Active / Closed states
- Manager evaluations
- Overall ratings
- Strengths
- Areas for improvement
- Goals
- Employee performance history
- Employee access to results after cycle closure

Managers can only evaluate employees within their permitted organizational scope.

---

## 🔔 Notifications

HRFlow supports notifications for important business events, including:

- New employee welcome notification
- Leave approval/rejection notification
- Manager notification for pending leave approval
- Payroll publication notification

Queued operations are used where asynchronous processing is appropriate.

---

# 📈 Reports

HRFlow provides reporting functionality for HR operations.

Available report types include:

- Headcount
- Payroll Summary
- Leave Utilization
- Attendance Overview
- Employee Turnover
- Performance Summary

Reports are protected using authentication and permission-based authorization.

---

# 📥 Excel Report Export

HR Admin users can export supported HR reports as Excel files.

The Report module provides a dedicated export endpoint:

```http
GET /api/v1/reports/{type}/export
```

The endpoint is protected by:

```text
permission:export.report
```

This allows report exporting to be controlled independently from normal report viewing permissions.

### Example

```http
GET /api/v1/reports/headcount/export
Authorization: Bearer {hr_admin}
```

The generated report can be downloaded as an Excel file and used for:

- HR analysis
- Payroll review
- Employee reporting
- Management reporting
- Data analysis
- Offline processing

---

# 💱 Currency Exchange Integration

HRFlow integrates with an external currency exchange API to support currency conversion.

The integration is designed through an exchange-rate provider abstraction.

The Payroll domain can use the currency exchange service to convert monetary values between currencies when required.

### Conceptual Flow

```text
Payroll
   │
   ▼
Currency Conversion Service
   │
   ▼
Exchange Rate Provider
   │
   ▼
External Currency Exchange API
   │
   ▼
Exchange Rate
```

This approach keeps the external API implementation separated from the business logic.

The application can therefore request currency conversion without coupling payroll business logic directly to the external API implementation.

---

# 👥 System Roles

HRFlow contains three main roles:

| Role | Description |
|---|---|
| **HR Admin** | Full HR system access |
| **Department Manager** | Manages employees and workflows within the permitted department |
| **Employee** | Accesses personal HR information and self-service features |

---

## 🔴 HR Admin

HR Admin has full access to HR operations.

Main responsibilities:

- Manage departments
- Manage job titles
- Hire employees
- Manage employees
- Manage salary structures
- Process payroll
- Finalize payroll
- Manage leave types
- Handle final leave approvals
- Create performance cycles
- View reports
- Export reports to Excel
- Access administrative operations

---

## 🟡 Department Manager

Department Managers can:

- View employees within their permitted scope
- Approve or reject leave requests
- Perform performance reviews
- View department attendance summaries
- Manage department-level workflows

Managers do not have access to payroll information unless explicitly permitted.

---

## 🟢 Employee

Employees can:

- View their personal information
- Update their permitted information
- Submit leave requests
- View leave balances
- Track leave request status
- View attendance
- View payslips
- View performance results after the cycle is closed

---

# 🏗️ Architecture

HRFlow follows a **Modular Backend Architecture**.

The project is a backend-only application and does not contain a frontend application.

Business domains are separated into modules so that each module owns its responsibilities and business rules.

### Main Architecture Principles

- Modular Architecture
- SOLID Principles
- Object-Oriented Programming
- Dependency Injection
- Separation of Concerns
- Actions for business operations
- Services for application-level orchestration
- DTOs for structured data transfer
- API Resources
- Form Requests
- Policies
- Permissions
- Middleware
- Events & Listeners
- Observers
- Queued Jobs
- Scheduled Jobs
- Enums
- Automated Testing

---

# 🔄 Employee Hiring Workflow

Employee hiring is implemented as a **cross-module event-driven workflow**.

When an employee is successfully hired, the Employee module dispatches an event.

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

The important point is that the Employee module does not need to directly implement the internal logic of every other module.

Instead, it publishes the event and the interested modules react to it.

### Why Events & Listeners?

This approach provides:

- Loose coupling
- Clear module boundaries
- Separation of responsibilities
- Easier testing
- Easier maintenance
- Easier extension
- Reduced direct dependencies between modules

For example, a future module can react to the `EmployeeHired` event without changing the core employee hiring operation.

---

# ⚙️ Requirements

| Component | Version |
|---|---|
| PHP | 8.4+ |
| Laravel | 13.x |
| Composer | 2.x |
| Database | SQLite / MySQL |
| Authentication | Laravel Sanctum |

Required PHP extensions must be enabled according to the installed Laravel and Composer packages.

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

The project includes a `.env.example` file containing the required environment variables.

Copy the example environment file to `.env`.

### Windows PowerShell

```powershell
Copy-Item .env.example .env

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

```powershell
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

Currency exchange API configuration should also be stored in environment variables according to the configured external provider.

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

### Authentication Endpoints

| Method | Endpoint | Access |
|---|---|---|
| POST | `/auth/login` | Public |
| POST | `/auth/logout` | Authenticated |
| GET | `/auth/me` | Authenticated |
| PUT | `/auth/password` | Authenticated |
| POST | `/auth/forgot-password` | Public |
| POST | `/auth/reset-password` | Public |

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

The API is organized around the application's business modules.

---

# 🏢 Departments & Job Titles

| Method | Endpoint | Access |
|---|---|---|
| GET | `/departments` | Authenticated |
| POST | `/departments` | HR Admin |
| GET | `/departments/{id}` | Authenticated |
| PUT | `/departments/{id}` | HR Admin |
| DELETE | `/departments/{id}` | HR Admin |
| POST | `/departments/{id}/restore` | HR Admin |
| PUT | `/departments/{id}/assign-manager` | Authorized users |
| GET | `/job-titles` | Authenticated |
| POST | `/job-titles` | HR Admin |
| PUT | `/job-titles/{id}` | HR Admin |
| DELETE | `/job-titles/{id}` | HR Admin |

---

# 👤 Employees

| Method | Endpoint | Access |
|---|---|---|
| GET | `/employees` | HR Admin / Manager |
| POST | `/employees` | HR Admin |
| GET | `/employees/{id}` | HR Admin / Manager |
| PUT | `/employees/{id}` | HR Admin |
| POST | `/employees/{id}/terminate` | HR Admin |
| GET | `/employees/{id}/salary-history` | HR Admin |
| POST | `/employees/{id}/documents` | HR Admin |
| GET | `/employees/{id}/documents` | HR Admin / Manager |
| GET | `/employees/me` | Employee |
| PUT | `/employees/me` | Employee |

---

# 🏖️ Leave Management

| Method | Endpoint | Access |
|---|---|---|
| GET | `/leave-types` | Authenticated |
| POST | `/leave-types` | HR Admin |
| PUT | `/leave-types/{id}` | HR Admin |
| GET | `/leave-requests` | Authenticated |
| POST | `/leave-requests` | Employee |
| GET | `/leave-requests/{id}` | Authenticated |
| POST | `/leave-requests/{id}/approve` | Manager / HR Admin |
| POST | `/leave-requests/{id}/reject` | Manager / HR Admin |
| DELETE | `/leave-requests/{id}` | Employee |
| GET | `/leave-requests/balance` | Employee |
| GET | `/employees/{id}/leave-balance` | HR Admin / Manager |

---

# 🕒 Attendance

| Method | Endpoint | Access |
|---|---|---|
| GET | `/attendance` | HR Admin / Manager |
| POST | `/attendance` | HR Admin |
| PUT | `/attendance/{id}` | HR Admin |
| GET | `/attendance/my` | Employee |
| GET | `/attendance/summary` | Manager / HR Admin |

---

# 💰 Payroll

| Method | Endpoint | Access |
|---|---|---|
| GET | `/salary-structures` | HR Admin |
| POST | `/salary-structures` | HR Admin |
| PUT | `/salary-structures/{id}` | HR Admin |
| GET | `/payroll-runs` | HR Admin |
| POST | `/payroll-runs` | HR Admin |
| POST | `/payroll-runs/{id}/process` | HR Admin |
| POST | `/payroll-runs/{id}/finalize` | HR Admin |
| GET | `/payroll-runs/{id}/payslips` | HR Admin |
| GET | `/payslips/{id}` | HR Admin / Employee |
| GET | `/payslips/my` | Employee |
| POST | `/payslips/{id}/deductions` | HR Admin |

---

# 📊 Performance

| Method | Endpoint | Access |
|---|---|---|
| GET | `/performance-cycles` | Authenticated |
| POST | `/performance-cycles` | HR Admin |
| POST | `/performance-cycles/{id}/activate` | HR Admin |
| POST | `/performance-cycles/{id}/close` | HR Admin |
| GET | `/performance-reviews` | HR Admin / Manager |
| POST | `/performance-reviews` | Manager |
| PUT | `/performance-reviews/{id}` | Manager |
| GET | `/employees/{id}/performance` | HR Admin / Manager |
| GET | `/performance-reviews/my` | Employee |

---

# 📈 Reports API

The Report module provides endpoints for viewing, generating, and exporting reports.

### Report Routes

| Method | Endpoint | Permission |
|---|---|---|
| POST | `/reports/payroll/generate/{run}` | `create.payroll.run` |
| GET | `/reports/{type}/export` | `export.report` |
| GET | `/reports/{type}` | `view.reports.all` / `report.view.department` |
| GET | `/reports/{type}/show` | `view.reports.all` / `report.view.department` |
| POST | `/reports/{type}/generate` | `create.report` |

### Available Report Types

Supported report types include:

```text
headcount
payroll-summary
leave-utilization
attendance-overview
turnover
performance-summary
```

### Example

```http
GET /api/v1/reports/headcount
Authorization: Bearer {token}
```

### Export Report

```http
GET /api/v1/reports/headcount/export
Authorization: Bearer {token}
```

The export endpoint generates the report as an Excel file for authorized users.

---

# 📐 Business Rules

## Employee

- A new employee receives a user account.
- Employee numbers are unique.
- An employee cannot be deleted as part of normal termination.
- Termination stores the date and reason.
- Terminated employees cannot log in.
- A job title must belong to the employee's department.
- Employee historical information remains available after termination.

---

## Leave

- Leave days are calculated automatically.
- Overlapping leave requests are not allowed.
- Employees cannot request more days than their available balance.
- Rejected leave does not reduce the balance.
- Sick leave exceeding two days requires a medical document.
- Employees without a manager are routed directly to HR Admin.
- Approved leave updates the employee's leave balance.

---

## Payroll

- Only one payroll run can exist for the same month.
- Active employees must have salary structures before finalization.
- Deductions can only be added before finalization.
- Finalized payslips are read-only.

---

## Performance

- Managers can evaluate employees within their permitted scope.
- Closed performance cycles cannot receive new evaluations.
- An employee cannot receive multiple evaluations from the same manager within the same cycle.

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

The employee hiring process dispatches an event after the employee has been successfully hired.

```text
Employee Module
      │
      ▼
EmployeeHired Event
      │
      ├──────────────► User Module
      │
      ├──────────────► Payroll Module
      │
      └──────────────► Notification Module
```

Each module handles its own responsibility without introducing unnecessary direct dependencies.

---

## Queued Operations

The system can use queued processing for operations such as:

- Welcome emails
- Leave notifications
- Payroll notifications
- Other asynchronous operations

---

## Scheduled Jobs

### Daily Job

Registers absence for employees who do not have attendance records.

### Monthly Job

Creates leave balances for active employees.

---

# ⚡ Caching

HRFlow uses caching for frequently accessed or expensive data.

Examples include:

| Data | Cache Duration |
|---|---:|
| Departments | 60 minutes |
| Job Titles | 60 minutes |
| Reports | 15 minutes |
| Employee Leave Balance | Immediate invalidation when changed |

Cache invalidation is performed when the underlying business data changes.

---

# 🧪 Testing

HRFlow follows an automated testing strategy.

## Feature Tests

Feature tests cover the main API endpoints and workflows.

Examples include:

- Authentication
- Employee management
- Leave management
- Payroll endpoints
- Performance endpoints
- Authorization
- Reporting

## Unit Tests

Unit tests cover important business logic such as:

- Payroll calculation
- Leave balance calculation
- Domain/business rules
- Currency conversion logic where applicable

The project targets a minimum test coverage of **70%**.

Run the test suite using:

```bash
php artisan test
```

---

---

# 🤖 AI Employee Assistant

HRFlow includes an AI-powered assistant designed to help employees interact with the HR system using natural language.

The assistant can understand employee questions and dynamically execute registered HR tools to retrieve relevant information from the system.

For example, an employee can ask questions such as:

- What is my remaining leave balance?
- How many days of annual leave do I have?
- What is my current HR information?
- What is my attendance information?

The AI assistant communicates with the available HR tools through a centralized tool registry.

### AI Request Flow

```text
Employee
    │
    ▼
AI Assistant
    │
    ▼
GeminiService
    │
    ▼
AiToolRegistry
    │
    ├──────────────► GetLeaveBalanceTool
    │
    ├──────────────► Employee Information Tool
    │
    ├──────────────► Attendance Tool
    │
    └──────────────► Other AI Tools
    │
    ▼
Tool Execution
    │
    ▼
Result
    │
    ▼
AI Response
    │
    ▼
Employee



# 🌍 Localization & Automatic Translation

HRFlow supports multilingual content and automatic field translation using the **TranslaGenius** Laravel package.

TranslaGenius provides automatic translation for configured Eloquent model fields and supports multiple languages. It also provides queue-based translation processing, allowing translation work to be handled asynchronously without blocking the main API request. :contentReference[oaicite:1]{index=1}

The package can automatically translate configured model attributes into the supported languages.

### Package

**TranslaGenius**

Repository:

https://github.com/YousefSaleh1/transla-genius

Install with:

```bash
composer require coding-partners/transla-genius


⚙️ Queue-Based Translation
php artisan queue:work


# 📮 Postman Collection

HRFlow is a **Backend API project only**.

There is no frontend application included in this repository.

API development, testing, and endpoint verification are performed using a Postman Collection.

The Postman Collection uses environment variables for the API URL and authentication tokens, allowing the same collection to be used for different users and environments.

## 📥 Download Postman Collection

The complete HRFlow Postman Collection is available in the repository:

[Download HRFlow Postman Collection](https://github.com/Ebla-a/HRFlow/blob/main/docs/HRFlow.postman_collection.json)

---

## 🔧 Postman Environment Variables

The collection uses the following variables:

```text
base_url
hr_admin
manager
employee### 📥 Download Postman Collection

The complete Postman API Collection is available here:

**[Download HRFlow Postman Collection](https://github.com/Ebla-a/HRFlow/blob/main/docs/HRFlow.postman_collection.json)**

---

## Postman Environment Variables

The Postman environment uses:

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

With Employee authentication:

```http
Authorization: Bearer {{employee}}
```

The same collection can therefore be used to verify role-based authorization without manually changing tokens in every request.

---

# 📦 API Response Standards

HRFlow follows a consistent JSON response structure for successful and failed API operations.

## Success Response

```json
{
    "status": true,
    "message": "Operation completed successfully.",
    "data": {},
    "meta": {}
}
```

The `meta` field is included when additional metadata is required, such as pagination or other response information.

---

## Error Response

```json
{
    "status": false,
    "message": "Something went wrong.",
    "errors": {}
}
```

This standardized response structure makes API consumption more predictable for clients and simplifies frontend/mobile integration if a client application is added in the future.

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
├── Organization/
│
├── Employee/
│
├── Leave/
│
├── Attendance/
│
├── Payroll/
│
├── Performance/
│
└── Report/
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
│
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
│
├── Routes/
└── Tests/
```

The exact contents of each module depend on its business responsibilities.

---

# 🔒 Security

HRFlow handles sensitive HR and payroll-related information, therefore authorization is applied at multiple levels.

Security mechanisms include:

- Laravel Sanctum
- Bearer token authentication
- Role-based access control
- Permission-based authorization
- Policies
- Middleware
- Form Request validation
- API Resources
- Employee ownership restrictions
- Department-level authorization
- Restricted payroll access
- Report permissions

### Examples

An employee cannot access another employee's private information.

A manager cannot access payroll data unless explicitly permitted.

Payroll operations are restricted to authorized HR users.

Employees can access their own payslips but not other employees' payslips.

Report access and Excel export are controlled using dedicated permissions.

---

# 🗺️ Development Phases

The original implementation plan is divided into seven phases:

| Phase | Days | Deliverables |
|---|---:|---|
| Phase 1 | 1–3 | Project setup, migrations, authentication, roles & policies |
| Phase 2 | 4–7 | Departments, job titles, employees, termination |
| Phase 3 | 8–12 | Leave types, requests, approval workflow, balances |
| Phase 4 | 13–17 | Salary structures, payroll runs, payslips, calculations |
| Phase 5 | 18–21 | Attendance and performance |
| Phase 6 | 22–23 | Queues, events, observers, jobs, caching |
| Phase 7 | 24–25 | Tests, Postman Collection, README, reporting |

---

# 📚 Documentation

The project documentation covers:

- Project architecture
- Installation
- Environment configuration
- Authentication
- Roles & permissions
- Employee lifecycle
- Organizational structure
- Leave workflows
- Attendance
- Payroll processing
- Currency exchange integration
- Reports
- Excel report export
- Events & listeners
- Background jobs
- Caching
- API endpoints
- Testing
- Postman API testing

---

# 👨‍💻 Development Team

## Team Members

| Role | Name |
|---|---|
| Team Leader | Ebla Zyab Ali |
| Assistant | Kinda Alabdullah |
| Database Specialist / Backend | Bedoor Ali Deep |
| Backend Developer | Shahd Al Esami |
| Backend Developer | Osama Mlikh |

---

# 🏆 Acknowledgments

## 🎉 Special Thanks

### Focal X Agency

For their commitment to student growth and learning opportunities.

---

## 👨‍🏫 Mentors

### Mr. Somar Kesen

- Support throughout development
- Technical guidance
- Concept clarification
- Inspirational mentorship

### Mr. Yousef Saleh

- Support throughout development
-  Technical guidance
- Concept clarification
- Inspirational mentorship

---

## 👩‍💼 Supervisors

### Mr. Bsher Al-Mahayni

- Technical support
- Continuous guidance throughout the project

---

## 🤝 Focal X Team

For building and supporting this educational journey.

---

## 👨‍💼 CEO & Founder

### Alaa Darwish

For leading and supporting the educational environment and providing opportunities for learning and professional development.

---

# 🎯 Project Goals

HRFlow aims to demonstrate a maintainable and production-oriented backend architecture through:

- Modular architecture
- SOLID principles
- Clean separation of responsibilities
- Secure authorization
- Permission-based access control
- Domain-oriented business logic
- Event-driven communication
- Cross-module communication
- Asynchronous processing
- Scheduled jobs
- Caching
- Automated testing
- API standards
- Payroll domain modeling
- External API integration
- Currency conversion
- HR reporting
- Excel report generation
- Maintainable HR workflows

---

# ⭐ HRFlow

### Human Resources Management System

**Manage employees. Automate HR operations. Process payroll. Generate reports.**

Built with **Laravel 13**, modern backend architecture, modular design, secure authorization, event-driven workflows, and maintainable business logic.

---
