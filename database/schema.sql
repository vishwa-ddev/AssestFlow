-- AssetFlow Database Schema
-- Run via database/install.php

CREATE DATABASE IF NOT EXISTS assetflow
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE assetflow;

-- Users
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(255) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    full_name   VARCHAR(150) NOT NULL,
    role        ENUM('employee', 'admin') NOT NULL DEFAULT 'employee',
    status      ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_email (email),
    INDEX idx_users_role (role),
    INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Master data
CREATE TABLE IF NOT EXISTS departments (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS asset_categories (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vendors (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assets registry
CREATE TABLE IF NOT EXISTS assets (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_code      VARCHAR(20) NOT NULL UNIQUE,
    name            VARCHAR(150) NOT NULL,
    asset_type      VARCHAR(50) NOT NULL,
    serial_number   VARCHAR(100) NULL,
    category_id     INT UNSIGNED NULL,
    department_id   INT UNSIGNED NULL,
    vendor_id       INT UNSIGNED NULL,
    purchase_date   DATE NULL,
    warranty_until  DATE NULL,
    location        VARCHAR(150) NULL,
    condition_note  VARCHAR(50) DEFAULT 'Good',
    qr_code         VARCHAR(255) NULL,
    photo_path      VARCHAR(255) NULL,
    status          ENUM('available', 'allocated', 'maintenance', 'inactive') NOT NULL DEFAULT 'available',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_assets_status (status),
    INDEX idx_assets_code (asset_code),
    INDEX idx_assets_category (category_id),
    INDEX idx_assets_department (department_id),
    FOREIGN KEY (category_id) REFERENCES asset_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Asset allocations
CREATE TABLE IF NOT EXISTS asset_allocations (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id             INT UNSIGNED NOT NULL,
    assigned_to          VARCHAR(150) NOT NULL,
    department           VARCHAR(100) NULL,
    status               ENUM('active', 'returned', 'overdue') NOT NULL DEFAULT 'active',
    expected_return_date DATE NULL,
    allocated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    INDEX idx_allocations_status (status),
    INDEX idx_allocations_asset (asset_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Allocation history timeline
CREATE TABLE IF NOT EXISTS allocation_history (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id       INT UNSIGNED NOT NULL,
    event_type     ENUM('allocated', 'returned', 'transferred') NOT NULL,
    employee_name  VARCHAR(150) NOT NULL,
    department     VARCHAR(100) NULL,
    condition_note VARCHAR(50) NULL,
    notes          TEXT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    INDEX idx_history_asset (asset_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookable resources
CREATE TABLE IF NOT EXISTS resources (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL UNIQUE,
    resource_type VARCHAR(50) DEFAULT 'room'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resource bookings
CREATE TABLE IF NOT EXISTS bookings (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource_id   INT UNSIGNED NULL,
    resource_name VARCHAR(150) NOT NULL,
    booked_by     VARCHAR(150) NOT NULL,
    status        ENUM('active', 'completed', 'cancelled') NOT NULL DEFAULT 'active',
    booking_date  DATE NOT NULL,
    start_time    TIME NOT NULL,
    end_time      TIME NOT NULL,
    start_date    DATE NULL,
    end_date      DATE NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE SET NULL,
    INDEX idx_bookings_status (status),
    INDEX idx_bookings_resource_date (resource_id, booking_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Asset transfers
CREATE TABLE IF NOT EXISTS transfers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id    INT UNSIGNED NOT NULL,
    from_user   VARCHAR(150) NOT NULL,
    to_user     VARCHAR(150) NOT NULL,
    reason      TEXT NULL,
    status      ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    INDEX idx_transfers_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Maintenance requests
CREATE TABLE IF NOT EXISTS maintenance_requests (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id           INT UNSIGNED NOT NULL,
    title              VARCHAR(150) NOT NULL,
    issue_description  TEXT NOT NULL,
    stage              ENUM('pending', 'approved', 'technician_assigned', 'in_progress', 'resolved') DEFAULT 'pending',
    technician_name    VARCHAR(150) NULL,
    resolved_at        DATE NULL,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    INDEX idx_maintenance_stage (stage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit cycles
CREATE TABLE IF NOT EXISTS audit_cycles (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(150) NOT NULL,
    department VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date   DATE NOT NULL,
    status     ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_auditors (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    audit_id     INT UNSIGNED NOT NULL,
    auditor_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (audit_id) REFERENCES audit_cycles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_checklist (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    audit_id          INT UNSIGNED NOT NULL,
    asset_id          INT UNSIGNED NOT NULL,
    expected_location VARCHAR(150) NOT NULL,
    verification      ENUM('verified', 'missing', 'damaged', 'pending') DEFAULT 'pending',
    FOREIGN KEY (audit_id) REFERENCES audit_cycles(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity log
CREATE TABLE IF NOT EXISTS activity_log (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_type VARCHAR(50) NOT NULL,
    message       VARCHAR(255) NOT NULL,
    icon          VARCHAR(50) NOT NULL DEFAULT 'bi-circle-fill',
    badge         VARCHAR(50) NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_created (created_at),
    INDEX idx_activity_type (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NULL,
    category   ENUM('alert', 'approval', 'booking', 'general') DEFAULT 'general',
    message    VARCHAR(255) NOT NULL,
    icon       VARCHAR(50) DEFAULT 'bi-bell',
    badge      VARCHAR(50) NULL,
    is_read    TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_notifications_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
