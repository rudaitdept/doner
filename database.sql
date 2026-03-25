CREATE DATABASE donation_crm;
USE donation_crm;

CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),email VARCHAR(100),password VARCHAR(255),role VARCHAR(20));
CREATE TABLE donations(id INT AUTO_INCREMENT PRIMARY KEY,donor_name VARCHAR(100),amount DECIMAL(10,2),source VARCHAR(100),date DATE,status VARCHAR(20) DEFAULT 'pending',added_by INT);
CREATE TABLE expenses(id INT AUTO_INCREMENT PRIMARY KEY,beneficiary_name VARCHAR(100),amount DECIMAL(10,2),purpose TEXT,date DATE,status VARCHAR(20) DEFAULT 'pending',added_by INT);
