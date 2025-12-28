# 🎓 Brac University Blogger

<div align="center">
  <h1>📢 Connect. Share. Inspire.</h1>
  <p><b>The official social blogging platform for the Brac University Community.</b></p>
  
  <p>
    <a href="https://brac-university-blogger.onrender.com">🔴 <b>Live Demo</b></a>
    &nbsp;|&nbsp;
    <a href="#-features">🚀 <b>Features</b></a>
    &nbsp;|&nbsp;
    <a href="#-installation">⚙️ <b>Installation</b></a>
  </p>
</div>

---

## 📖 Overview

**Brac University Blogger** is a dynamic social platform tailored for students, faculty, and staff of Brac University. It creates a vibrant digital space to share updates, express ideas through blogs, follow interesting personalities, and engage with the community through real-time interactions.

Whether you want to share your latest research, discuss campus trends, or just find like-minded peers, this platform connects you to the heartbeat of the campus.

## 🚀 Key Features

### ✍️ **Dynamic Blogging**
- **Create & Edit**: Share your stories with rich text titles and descriptions.
- **Categorization**: Tag your posts for better discoverability.
- **Quick Posts**: Share status updates instantly.

### 💬 **Social Engagement**
- **Like & Dislike**: React to posts to show your appreciation or opinion.
- **Threaded Comments**: Engage in deep discussions with nested replies.
- **Real-time Updates**: Experience a lively community feed.

### 🔍 **Discovery & Networking**
- **Explore Page**: Discover trending content across the platform.
- **Who to Follow**: Get smart suggestions on interesting profiles to follow.
- **Interactive Search**: Find users and posts effortlessly.

### 👤 **User Profiles**
- **Personalized Space**: Showcase your blogs, followers, and following lists.
- **Activity Tracking**: Keep track of your interactions and history.

### 🌐 **Responsive Design**
- Fully optimized for **Desktop**, **Tablet**, and **Mobile** viewing.

---

## 🛠️ Tech Stack

<div align="center">

| Component | Technology |
| :--- | :--- |
| **Frontend** | ![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=flat&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=flat&logo=css3&logoColor=white) ![Bootstrap](https://img.shields.io/badge/bootstrap-%23563D7C.svg?style=flat&logo=bootstrap&logoColor=white) |
| **Backend** | ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=flat&logo=php&logoColor=white) |
| **Database** | ![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=flat&logo=mysql&logoColor=white) |
| **Deployment** | ![Render](https://img.shields.io/badge/Render-%2346E3B7.svg?style=flat&logo=render&logoColor=white) |

</div>

---

## ⚙️ Installation & Setup

Want to run this locally? Follow these simple steps:

### Prerequisites
- **PHP** (via XAMPP, WAMP, or standalone)
- **MySQL** Server
- **Git**

### Steps

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/SwapnilSaha5768/Brac-University-Blogger.git
    cd Brac University Blogger
    ```

2.  **Database Configuration**
    - Open your MySQL tool (e.g., phpMyAdmin).
    - Create a new database named `bracuniversityblogger`.
    - Import the `Frontend/setup.sql` file located in the project directory.

3.  **Environment Setup**
    - The application looks for environment variables but defaults to standard local settings.
    - **Default Config**:
      - Host: `localhost`
      - User: `root`
      - Password: `""` (empty)
    - *Optional*: To customize, edit `Frontend/includes/database.php` or set `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` in your environment.

4.  **Run the Application**
    - **Using PHP Built-in Server**:
      ```bash
      cd Frontend
      php -S localhost:8000
      ```
    - Visit `http://localhost:8000` in your browser.

---

## 🔐 Getting Started
1.  Navigate to the **Registration** page.
2.  Create an account with your details.
3.  Login and start **Blogging**!

## 🤝 Contributing
Contributions are always welcome!
1.  Fork the repository.
2.  Create your feature branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

---

<div align="center">
  Made with ❤️ for the <b>Brac University</b> Community
</div>
