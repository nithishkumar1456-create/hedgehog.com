# 🦔 Hedgehog.com

A scalable full-stack web application built using **PHP, MongoDB Atlas, MySQL (Railway), Redis (Upstash), Docker, and Render**, designed for performance, scalability, and cloud-native deployment.

---

## 📌 Overview

Hedgehog.com is a modern multi-database web platform:

- **MySQL (Railway)** → Authentication & structured data  
- **MongoDB Atlas** → Flexible document storage  
- **Upstash Redis** → Caching layer  
- **Docker** → Containerized environment  
- **Render** → Cloud deployment  

---

## 🚀 Features

- 🔐 Secure Authentication (MySQL - Railway)  
- 📦 NoSQL Storage (MongoDB Atlas)  
- ⚡ Redis Caching (Upstash)  
- 🌐 RESTful API (PHP Backend)  
- 🐳 Dockerized Setup  
- ☁️ Deployment on Render  
- 🎨 Responsive Frontend  

---

## 🛠️ Tech Stack

### Frontend
- HTML5  
- CSS3  
- JavaScript  

### Backend
- PHP  

### Databases & Services
- MySQL (Railway)  
- MongoDB Atlas  
- Upstash Redis  

### DevOps & Tools
- Docker  
- Render  
- VS Code  

---

Client (Browser)
↓
Frontend (HTML/CSS/JS)
↓
PHP Backend (Dockerized)
↓
| MySQL (Railway) → Authentication |
| MongoDB Atlas → Application Data |
| Redis (Upstash) → Cache Layer |


## 📂 Project Structure

hedgehog/
│
├── frontend/
│ ├── index.html
│ ├── css/
│ ├── js/
│
├── backend/
│ ├── api/
│ ├── config/
│ │ ├── mysql.php
│ │ ├── mongodb.php
│ │ ├── redis.php
│
├── docker/
│ ├── Dockerfile
│ ├── docker-compose.yml
│
├── .env
└── README.md

☁️ Deployment (Render)
Steps
Push project to GitHub
Go to Render
Create New Web Service
Connect repository
Select Docker environment
Add environment variables
Deploy 🚀

🌍 Live URL
https://your-app-name.onrender.com


## 🧱 Architecture
