create database orders;
create table orders
(
    id  int auto_increment    primary key,
    cash decimal(10,2) not null,
    created_at  datetime default CURRENT_TIMESTAMP not null
);
create table operation
(
    id  int auto_increment    primary key,
    id_order  int not null,
    issuer_name  varchar(255) not null,
    type      enum("BUY","SELL"),
    shares  int(10) not null,
    price decimal(8,2) not null,
    created_at  datetime default CURRENT_TIMESTAMP not null,
    FOREIGN KEY (id_order) REFERENCES orders(id)
);