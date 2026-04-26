<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli('localhost', 'root', '', 'localhub');
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    die('Database connection failed. Please verify your MySQL setup.');
}

function lh_table_exists(mysqli $conn, string $dbName, string $tableName): bool
{
    $stmt = $conn->prepare('SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = ? AND table_name = ?');
    $stmt->bind_param('ss', $dbName, $tableName);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return (int) ($row['total'] ?? 0) > 0;
}

function lh_column_exists(mysqli $conn, string $dbName, string $tableName, string $columnName): bool
{
    $stmt = $conn->prepare('SELECT COUNT(*) AS total FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?');
    $stmt->bind_param('sss', $dbName, $tableName, $columnName);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return (int) ($row['total'] ?? 0) > 0;
}

function lh_try_exec(mysqli $conn, string $sql): void
{
    try {
        $conn->query($sql);
    } catch (mysqli_sql_exception $exception) {
        // Keep the app running even if a specific migration statement fails.
    }
}

function lh_ensure_schema(mysqli $conn): void
{
    $dbName = 'localhub';

    lh_try_exec(
        $conn,
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(80) NOT NULL,
            email VARCHAR(120) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('client','provider') NOT NULL DEFAULT 'client',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    );

    if (lh_table_exists($conn, $dbName, 'users')) {
        if (!lh_column_exists($conn, $dbName, 'users', 'name')) {
            lh_try_exec($conn, "ALTER TABLE users ADD COLUMN name VARCHAR(80) NOT NULL DEFAULT '' AFTER id");
        }
        if (!lh_column_exists($conn, $dbName, 'users', 'email')) {
            lh_try_exec($conn, "ALTER TABLE users ADD COLUMN email VARCHAR(120) NOT NULL DEFAULT '' AFTER name");
        }
        if (!lh_column_exists($conn, $dbName, 'users', 'password')) {
            lh_try_exec($conn, "ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '' AFTER email");
        }
        if (!lh_column_exists($conn, $dbName, 'users', 'role')) {
            lh_try_exec($conn, "ALTER TABLE users ADD COLUMN role ENUM('client','provider') NOT NULL DEFAULT 'client'");
        }
        if (!lh_column_exists($conn, $dbName, 'users', 'created_at')) {
            lh_try_exec($conn, 'ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        }

        if (lh_column_exists($conn, $dbName, 'users', 'fullname')) {
            lh_try_exec($conn, 'UPDATE users SET name = fullname WHERE (name IS NULL OR name = "") AND fullname IS NOT NULL AND fullname <> ""');
        }
        if (lh_column_exists($conn, $dbName, 'users', 'username')) {
            lh_try_exec($conn, 'UPDATE users SET name = username WHERE (name IS NULL OR name = "") AND username IS NOT NULL AND username <> ""');
        }
        lh_try_exec($conn, 'UPDATE users SET name = "User" WHERE name IS NULL OR name = ""');
    }

    lh_try_exec(
        $conn,
        "CREATE TABLE IF NOT EXISTS gigs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            title VARCHAR(120) NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            image VARCHAR(255) DEFAULT NULL,
            category VARCHAR(80) NOT NULL DEFAULT 'General',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    );

    if (lh_table_exists($conn, $dbName, 'gigs')) {
        if (!lh_column_exists($conn, $dbName, 'gigs', 'user_id')) {
            lh_try_exec($conn, 'ALTER TABLE gigs ADD COLUMN user_id INT NULL');
        }
        if (!lh_column_exists($conn, $dbName, 'gigs', 'category')) {
            lh_try_exec($conn, "ALTER TABLE gigs ADD COLUMN category VARCHAR(80) NOT NULL DEFAULT 'General'");
        }
        if (!lh_column_exists($conn, $dbName, 'gigs', 'created_at')) {
            lh_try_exec($conn, 'ALTER TABLE gigs ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        }
        if (lh_column_exists($conn, $dbName, 'gigs', 'provider_id')) {
            lh_try_exec($conn, 'UPDATE gigs SET user_id = provider_id WHERE (user_id IS NULL OR user_id = 0) AND provider_id IS NOT NULL');
        }
        lh_try_exec($conn, 'UPDATE gigs SET category = "General" WHERE category IS NULL OR category = ""');
    }

    lh_try_exec(
        $conn,
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            gig_id INT NOT NULL,
            status ENUM('pending','accepted','completed') NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    );

    lh_try_exec(
        $conn,
        "CREATE TABLE IF NOT EXISTS wishlists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            gig_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_gig (user_id, gig_id)
        )"
    );

    if (lh_table_exists($conn, $dbName, 'bookings') && lh_table_exists($conn, $dbName, 'orders')) {
        $count = $conn->query('SELECT COUNT(*) AS total FROM orders')->fetch_assoc();
        if ((int) ($count['total'] ?? 0) === 0 && lh_column_exists($conn, $dbName, 'bookings', 'client_id')) {
            lh_try_exec(
                $conn,
                "INSERT INTO orders (user_id, gig_id, status, created_at)
                 SELECT client_id, gig_id,
                 CASE
                    WHEN status IN ('pending','accepted','completed') THEN status
                    ELSE 'pending'
                 END,
                 NOW()
                 FROM bookings"
            );
        }
    }
}

lh_ensure_schema($conn);
