use winkel;

create table product (
    id int auto_increment primary key,
    code varchar(100) not null unique,
    omschrijving text not null,
    foto varchar(255),
    prijsPerStuk decimal(10,2) not null
);