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
-- admin = password: admin123
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@admin.com', '$2y$13$3tPsRQp5.Pv0hqLPExz6zerv7kF49QFqNdRjrgP9XgucwPqG64OVm');


-- Insert dummy products
INSERT INTO product (code, omschrijving, foto, prijsPerStuk) VALUES
('LAPTOP001', 'Gaming Laptop 15 inch met NVIDIA RTX grafische kaart, perfect voor gamen en werken', 'uploads/laptop.png', 899.99);