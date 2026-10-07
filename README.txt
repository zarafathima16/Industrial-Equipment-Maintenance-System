INDUSCARE - INDUSTRIAL EQUIPMENT MAINTENANCE MANAGEMENT SYSTEM
STAGE 2: PHP + MySQL

Requirements:
- XAMPP 8.2.x
- Apache running
- MySQL/MariaDB running
- Database: industrial_maintenance
- Tables: equipment, maintenance

SETUP:
1. Copy this entire folder to C:\xampp\htdocs\
2. The final path should be:
   C:\xampp\htdocs\IndustrialEquipmentMaintenance_Stage2\
3. Start Apache and MySQL in XAMPP.
4. You already created the database and tables in phpMyAdmin.
5. Open Chrome and visit:
   http://localhost/IndustrialEquipmentMaintenance_Stage2/

If you want sample records, use sample_data.sql in phpMyAdmin > industrial_maintenance > SQL.

IMPORTANT:
- db.php uses the default XAMPP root account with an empty password.
- If your MySQL root account has a password, edit db.php and set $password.
- This Stage 2 version stores equipment and maintenance records in MySQL.
