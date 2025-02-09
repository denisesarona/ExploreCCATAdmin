# Interactive 3D Map - Admin Panel  

## Overview 
The **Interactive 3D Map - Admin Panel** is a web-based management system designed to help administrators efficiently oversee and update campus/building data. The system allows users to manage buildings, faculty information, and facilities, all while integrating with a **3D interactive map** powered by **Unreal Engine**.  

## Features  

### Admin Features: 
✔ **User Management**  
   - Add, update, or remove users (Faculty, Building Management).  
   - Assign roles and access permissions.  

✔ **Building Management**  
   - Update building details.  

✔ **Interactive 3D Map Integration**  
   - Link database information with the **Unreal Engine** map.  
   - Display real-time updates on building status and locations.  

---

## **Installation Guide**  

### **1. Clone the Repository**  
```bash
git clone https://github.com/yourusername/interactive-3d-map.git
```  

### **2. Move Files to XAMPP htdocs**  
- Copy the cloned folder into the **XAMPP htdocs directory**:  
  - **Windows**: `C:\xampp\htdocs\interactive-3d-map`  
  - **macOS/Linux**: `/Applications/XAMPP/htdocs/interactive-3d-map/`  

### **3. Database Setup**  
1. Start **XAMPP** and enable **Apache** and **MySQL**.  
2. Open **phpMyAdmin** (`http://localhost/phpmyadmin/`).  
3. Create a new database (e.g., `3d_map_db`).  
4. Import the provided SQL file located in `database/3d_map_db.sql`.  

### **4. Configure the Backend**  
- Open `config.php` and update database credentials:  
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');  // Change if necessary
  define('DB_PASS', '');      // Set password if applicable
  define('DB_NAME', '3d_map_db');
  ```  

### **5. Increase MySQL Max Connections (Optional but Recommended)**  
To support multiple users efficiently, update the **max_connections** setting:  

- Open the **MySQL configuration file (`my.ini` or `my.cnf`)**:  
  - **Windows**: `C:\xampp\mysql\bin\my.ini`  
  - **macOS/Linux**: `/Applications/XAMPP/xamppfiles/etc/my.cnf`  

- Find the `[mysqld]` section and add or modify this line:  
  ```ini
  max_connections = 1000
  ```  
- Save the file and restart **MySQL** in the XAMPP Control Panel.  

### **6. Run the Project**  
- Open a browser and go to:  
  ```http
  http://localhost/interactive-3d-map/
  ```  
- Log in using admin credentials (default credentials can be found in `database/initial_data.sql`).  

---

## **Technology Stack**  

| **Component**      | **Technology Used** |
|-------------------|--------------------|
| **Frontend**      | PHP, HTML, CSS |
| **Backend**       | PHP, MySQL |
| **Database**      | MySQL (via XAMPP) |
| **Server**        | XAMPP (Apache + MySQL) |
| **3D Map Engine** | Unreal Engine |

---

## **Troubleshooting**  

### **1. Database Connection Issues**  
- Ensure MySQL is running in **XAMPP Control Panel**.  
- Double-check `config.php` for correct database credentials.  

### **2. Apache Server Not Running**  
- Port 80 may be in use. Change Apache’s port in **XAMPP > Config > httpd.conf**.  

### **3. 3D Map Not Opening**  
- Ensure Unreal Engine is properly linked to the database.  
- Restart the system to refresh cached data.  
