# HRFlow

### Human Resources Management System

HRFlow is a modular **Backend API** for Human Resources Management.

The system centralizes employee lifecycle management, organizational structure, leave management, attendance, payroll processing, performance reviews, notifications, reporting, and HR operations in one secure and maintainable backend.

HRFlow is designed around the complete employee lifecycle — from hiring a new employee and managing their salary structure to leave requests, attendance, payroll processing, performance evaluation, reporting, and termination.

> **Backend Only:** HRFlow is a backend API project and does not include a frontend application.

---

## 📋 Table of Contents

- [Project Overview](#-project-overview)
- [Key Features](#-key-features)
- [System Roles](#-system-roles)
- [Architecture](#-architecture)
- [Employee Hiring Workflow](#-employee-hiring-workflow)
- [External API Integration](#-external-api-integration)
- [Requirements](#️-requirements)
- [Installation & Setup](#-installation--setup)
- [Environment Configuration](#-environment-configuration)
- [Authentication](#-authentication)
- [Database Structure](#-database-structure)
- [API Documentation](#-api-documentation)
- [Business Rules](#-business-rules)
- [Leave Approval Workflow](#-leave-approval-workflow)
- [Payroll Processing](#-payroll-processing)
- [Reports & Excel Export](#-reports--excel-export)
- [Events, Listeners & Jobs](#-events-listeners--jobs)
- [Caching](#-caching)
- [Testing](#-testing)
- [Postman Collection](#-postman-collection)
- [Project Structure](#-project-structure)
- [Development Phases](#-development-phases)
- [Security](#-security)
- [API Standards](#-api-standards)
- [Development Team](#-development-team)
- [Acknowledgments](#-acknowledgments)
- [Project Goals](#-project-goals)

---

# 📌 Project Overview

In many organizations, HR operations are distributed across spreadsheets, messaging applications, emails, and manual payroll processes.

This makes employee information difficult to manage consistently and increases the possibility of errors in areas such as leave balances, attendance, salary calculations, and payroll.

HRFlow addresses this problem by providing a centralized backend system where HR operations are connected and managed through well-defined business workflows.

The system provides:

- Employee lifecycle management
- Organizational structure management
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
- Scheduled jobs
- HR reports
- Excel report exports
- Currency conversion through an external API
- Role-based authorization
- RESTful API
- Automated testing

---

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
- Restoring soft-deleted departments

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

### Automatic Absence Registration

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
- Currency conversion

Payroll runs follow the following lifecycle:

```text
Draft → Processing → Finalized
