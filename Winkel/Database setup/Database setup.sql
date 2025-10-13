DROP DATABASE IF EXISTS winkel;
CREATE DATABASE winkel;
use winkel;

create table product (
    id int auto_increment primary key,
    code varchar(100) not null unique,
    omschrijving text not null,
    foto varchar(255),
    prijsPerStuk decimal(10,2) not null
);

create table users (
    id int auto_increment primary key,
    username varchar(255),
    email varchar(255),
    password varchar(255)
);

-- Insert dummy users
-- admin = password: 123
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@admin.com', '$2y$10$/JqDb7hNr2d762CsnG7ByO4EKIk6ZXIXrmkMq.UWcErUlM0vjxADy');


-- Insert dummy products
INSERT INTO product (code, omschrijving, foto, prijsPerStuk) VALUES
('LAPTOP001', 'Gaming Laptop 15 inch met NVIDIA RTX grafische kaart, perfect voor gamen en werken', 'uploads/laptop.png', 899.99),
('LAPTOP001', 'Gaming Laptop 15 inch met NVIDIA RTX grafische kaart, perfect voor gamen en werken', 'uploads/laptop.png', 899.99);