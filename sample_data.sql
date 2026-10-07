USE industrial_maintenance;
INSERT IGNORE INTO equipment (equipment_id,equipment_name,department,status,last_maintenance,next_maintenance) VALUES
('EQ-001','CNC Machine','Manufacturing','Operational','2026-09-01','2026-10-15'),
('EQ-002','Hydraulic Press','Production','Under Maintenance','2026-08-20','2026-10-10'),
('EQ-003','Conveyor System','Packaging','Operational','2026-09-10','2026-11-05'),
('EQ-004','Industrial Boiler','Production','Critical','2026-08-05','2026-10-08'),
('EQ-005','Air Compressor','Maintenance','Operational','2026-09-15','2026-11-15');
INSERT INTO maintenance (equipment_id,maintenance_type,scheduled_date,technician,description) SELECT 'EQ-001','Preventive Maintenance','2026-10-15','Arun','Lubrication and inspection' WHERE EXISTS (SELECT 1 FROM equipment WHERE equipment_id='EQ-001') AND NOT EXISTS (SELECT 1 FROM maintenance WHERE equipment_id='EQ-001' AND scheduled_date='2026-10-15');
INSERT INTO maintenance (equipment_id,maintenance_type,scheduled_date,technician,description) SELECT 'EQ-002','Corrective Maintenance','2026-10-10','Kavin','Hydraulic pressure check' WHERE EXISTS (SELECT 1 FROM equipment WHERE equipment_id='EQ-002') AND NOT EXISTS (SELECT 1 FROM maintenance WHERE equipment_id='EQ-002' AND scheduled_date='2026-10-10');
INSERT INTO maintenance (equipment_id,maintenance_type,scheduled_date,technician,description) SELECT 'EQ-004','Safety Inspection','2026-10-08','Priya','Boiler safety and valve inspection' WHERE EXISTS (SELECT 1 FROM equipment WHERE equipment_id='EQ-004') AND NOT EXISTS (SELECT 1 FROM maintenance WHERE equipment_id='EQ-004' AND scheduled_date='2026-10-08');
