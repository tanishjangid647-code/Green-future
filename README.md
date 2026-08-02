# Green Future – Smart Tree Plantation Campaign Management System

Green Future is a full-stack, industry-grade Tree Plantation & Reforestation Campaign Management Web Application built with PHP 8+, MySQL, Bootstrap 5, ES6 JS, Chart.js, AOS animations, and SweetAlert2.

## Features Overview

1. **Multi-Role Access**:
   - **Admin**: Executive dashboard with Chart.js analytics, Campaign CRUD, Tree registry, User moderation, Certificates issuance, Report printing, DB backup.
   - **Volunteer**: Assigned tree monitoring, GPS inspection logs, height measurement photo logs, health status updates.
   - **Registered User**: Booking plantation drives, personal adopted trees timeline, certificates download, carbon footprint calculator, wishlist.
   - **Visitor**: Landing page, active campaigns search/filter, public tree tracking, AI species recommender, leaderboard, gallery, blog, contact & FAQ.

2. **Advanced Innovations**:
   - **AI Species Recommender Engine**: Recommends indigenous tree species based on soil type, city microclimate, and plantation goals.
   - **AI Eco Assistant Chatbot**: Interactive floating chatbot for tree planting and campaign queries.
   - **Carbon Footprint & Offset Calculator**: Calculates individual CO₂ footprint and trees required to achieve carbon neutrality.
   - **QR Code Verification System**: Real-time tree tracking and official certificate authenticity verification.
   - **Multi-language UI**: Live English & Hindi language switcher.
   - **Dark Mode & Light Mode**: Smooth theme toggling saved in local storage.

---

## XAMPP Deployment & Installation Instructions

### Step 1: Place Files in XAMPP
Copy the entire `GreenFuture` folder into your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\GreenFuture
```

### Step 2: Import Database
1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Open your browser and go to `http://localhost/phpmyadmin/`.
3. Create a new database named `green_future`.
4. Click on the **Import** tab, choose the file `database/schema.sql` inside the project folder, and click **Import**.

### Step 3: Launch the Application
Open your browser and visit:
```
http://localhost/GreenFuture/
```

---

## Default Login Credentials

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@greenfuture.org` | `Admin@123` |
| **Volunteer** | `volunteer@greenfuture.org` | `Volunteer@123` |
| **User** | `user@greenfuture.org` | `User@123` |

---
&copy; Green Future Foundation. Production-Ready System.
