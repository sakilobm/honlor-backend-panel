# Honlor Admin Workspace 🛡️

A premium, high-performance admin dashboard built specifically for **Honlor**, a privacy-first real-time messaging platform. 

This repository contains the custom PHP MVC backend (Aether Catalyst) and the Vanilla JS / Tailwind CSS frontend used to manage the Honlor ecosystem. It provides system administrators with the tools needed for real-time user moderation, channel orchestration, ad campaign management, and server health monitoring.

## 💎 Design Philosophy: Soul vs. Application

This project follows a strict **Separation of Concerns** to ensure high maintainability and easy scalability:

- **The Soul (`htdocs/libs/src/`)**: The immutable framework engine. It handles database connections, session security, and core API routing. It is designed to be the "Engine" that remains constant across different projects.
- **The Application (`htdocs/libs/app/`)**: The project-specific functions. This is where the website's unique logic lives, such as managing Honlor’s Ads, Channels, and Messages.
- **The Bridge (`htdocs/libs/api/`)**: REST controllers that connect the frontend UI to the backend logic.

## 🚀 Key Features

- **User Moderation**: Track, block/unblock, and manage user profiles.
- **Channel Orchestration**: Manage messaging channels and group interactions.
- **Ad Campaign Management**: Create and monitor ad performance within the platform.
- **Server Health Dashboard**: Real-time monitoring of integrated services:
  - **Supabase**: Backend data and real-time listeners.
  - **Firebase FCM**: Global push notifications.
  - **Tinode**: Real-time messaging server.
- **Gamified UI**: Neon-themed, high-performance admin console with GSAP animations.

## 🛠️ Technology Stack

- **Backend**: PHP 8.1+ (Custom MVC - Aether Catalyst)
- **Database**: MySQL (PDO-based ORM with Transactions)
- **Messaging**: RabbitMQ (php-amqplib)
- **Frontend**: 
  - Vanilla JavaScript (Modern ES6+)
  - Tailwind CSS / Vanilla CSS
  - GSAP (High-performance animations)
  - ToastV3 notification system
- **Integrations**: Supabase, Firebase FCM, Tinode, Carbon (Date/Time)

## 📦 Getting Started

### 1. Installation
Clone the repository and install dependencies:
```bash
composer install
```

### 2. Configuration
Copy the template configuration file and update it with your credentials:
```bash
cp project/config.json.template project/config.json
```
Alternatively, use the `.env` file in the root for environment-specific secrets.

### 3. Database Setup
Import the core schema located in `htdocs/db/` to your MySQL instance.

## 📂 Detailed Folder Structure

### Root Directory (`/`)
- `htdocs/`: **The Public Web Root**. This is the only folder exposed to the web server.
- `project/`: **The Development Workspace**. Houses source frontend assets (Uncompiled JS/CSS) and build configurations (Grunt/NPM).
- `tests/`: **Quality Assurance**. Contains Unit (Model logic) and Integration (API endpoints) tests to ensure framework stability.

### Web Root (`htdocs/`)
- `libs/`:
  - `src/`: **The Core Framework (The Soul)**. Contains foundational classes: `Session`, `Database`, `WebAPI`, `REST`. Must NOT contain project-specific logic.
  - `app/`: **The Project Heart**. Contains domain-specific models like `Ad`, `Channel`, `Message`, and `DashboardStats`.
  - `api/`: **Local API Endpoints**. Project-unique functions triggered by frontend requests.
  - `traits/`: Reusable structural logic (e.g., `SQLGetterSetter`).
- `_templates/`: **The Face**. Pure UI logic and display components.
- `assets/`: Publicly served static files (css, js, img).
- `db/`: SQL migration files and database schemas.