# 🎫 Helpdesk Prototype

<p align="center">
Modern Helpdesk Ticket Management System built with <b>Laravel 11</b>, <b>Vue 3</b>, <b>Inertia.js</b>, <b>Tailwind CSS</b>, and <b>MySQL</b>.
</p>

## ✨ Overview

Helpdesk Prototype is a modern web-based ticket management system for internal IT support. It streamlines the entire support workflow, from ticket creation and assignment to collaboration, activity tracking, and resolution.

## 🚀 Features

- User Authentication (Laravel Breeze)
- Dashboard
- Ticket Management (CRUD)
- Ticket Assignment
- Priority & Status Management
- Categories
- Comments
- Attachments / Image Uploads
- Activity Logs
- Login Logs
- System Logs
- Responsive Interface

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11 |
| Frontend | Vue 3 |
| SPA | Inertia.js |
| Styling | Tailwind CSS |
| Database | MySQL |
| Build Tool | Vite |
| Language | PHP 8.2+ |

## 📂 Project Structure

```text
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
```

## ⚙️ Installation

```bash
git clone https://github.com/WidhiatAdiP/helpdesk_prototype.git
cd helpdesk_prototype
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`.

```bash
php artisan migrate
php artisan storage:link
php artisan serve
npm run dev
```

## 📋 Roadmap

- [x] Authentication
- [x] Ticket CRUD
- [x] Assignment
- [x] Activity Logs
- [x] Login Logs
- [x] Comments & Attachments
- [ ] Email Notifications
- [ ] Advanced Search
- [ ] Reports & Analytics
- [ ] Role & Permission Management
- [ ] Dark Mode

## 🤝 Contributing

Contributions, suggestions, and bug reports are welcome. Feel free to open an Issue or submit a Pull Request.

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**Widhiat Adi P**

GitHub: https://github.com/WidhiatAdiP

---

⭐ If you find this project useful, please consider giving it a Star.
