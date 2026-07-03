CREATE DATABASE IF NOT EXISTS eco_bulk_marketplace;

USE eco_bulk_marketplace;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_details;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS group_members;
DROP TABLE IF EXISTS bulk_groups;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS administrators;

CREATE TABLE administrators (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    eco_rating ENUM('Low','Medium','High') DEFAULT 'Medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id)
    REFERENCES categories(category_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

CREATE TABLE bulk_groups (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    created_by INT NOT NULL,
    target_quantity INT NOT NULL,
    ordered_quantity INT DEFAULT 0,    
    current_members INT DEFAULT 1,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    status ENUM('Open','Completed','Cancelled') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id)
    REFERENCES products(product_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    FOREIGN KEY (created_by)
    REFERENCES customers(customer_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE group_members (
    membership_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    customer_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(group_id, customer_id),

    FOREIGN KEY (group_id)
    REFERENCES bulk_groups(group_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

    FOREIGN KEY (customer_id)
    REFERENCES customers(customer_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    group_id INT NULL,
    total_amount DECIMAL(10,2) DEFAULT 0,
    order_status ENUM('Pending','Confirmed','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id)
    REFERENCES customers(customer_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

    FOREIGN KEY (group_id)
    REFERENCES bulk_groups(group_id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);

CREATE TABLE order_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id)
    REFERENCES orders(order_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

    FOREIGN KEY (product_id)
    REFERENCES products(product_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL UNIQUE,
    payment_method ENUM('Cash','Card','Bank Transfer') DEFAULT 'Cash',
    payment_status ENUM('Pending','Paid','Failed') DEFAULT 'Pending',
    paid_amount DECIMAL(10,2) DEFAULT 0,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id)
    REFERENCES orders(order_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

INSERT INTO administrators (full_name, email, password)
VALUES
('System Administrator', 'admin@ecobulk.com', 'admin123');

INSERT INTO customers (full_name, email, password, phone, address)
VALUES
('Ali Khan', 'ali@gmail.com', '12345', '03001234567', 'Karachi'),
('Sara Ahmed', 'sara@gmail.com', '12345', '03111234567', 'Lahore'),
('Usman Raza', 'usman@gmail.com', '12345', '03211234567', 'Islamabad');

INSERT INTO categories (category_name, description)
VALUES
('Reusable Items', 'Products that can be reused multiple times'),
('Organic Products', 'Naturally produced eco-friendly items'),
('Biodegradable Products', 'Products that decompose naturally'),
('Recycled Products', 'Products made from recycled material');

INSERT INTO products (category_id, product_name, description, price, stock_quantity, eco_rating)
VALUES
(1, 'Reusable Water Bottle', 'Eco-friendly stainless steel bottle', 1200.00, 100, 'High'),
(1, 'Cloth Shopping Bag', 'Reusable cloth bag for groceries', 350.00, 200, 'High'),
(2, 'Organic Soap Pack', 'Chemical-free organic soap', 500.00, 150, 'Medium'),
(3, 'Biodegradable Plates', 'Disposable plates made from natural material', 800.00, 120, 'High'),
(4, 'Recycled Notebook', 'Notebook made from recycled paper', 250.00, 300, 'Medium');

INSERT INTO bulk_groups (product_id, created_by, target_quantity, current_members, discount_percentage, status)
VALUES
(1, 1, 10, 1, 10.00, 'Open'),
(2, 2, 20, 1, 15.00, 'Open'),
(4, 3, 15, 1, 12.00, 'Open');

INSERT INTO group_members (group_id, customer_id)
VALUES
(1, 1),
(2, 2),
(3, 3);

CREATE VIEW customer_order_summary AS
SELECT
    c.customer_id,
    c.full_name,
    c.email,
    o.order_id,
    o.total_amount,
    o.order_status,
    o.order_date
FROM customers c
JOIN orders o
ON c.customer_id = o.customer_id;

CREATE VIEW product_inventory_status AS
SELECT
    p.product_id,
    p.product_name,
    c.category_name,
    p.price,
    p.stock_quantity,

    CASE
        WHEN p.stock_quantity = 0 THEN 'Out of Stock'
        WHEN p.stock_quantity < 20 THEN 'Low Stock'
        ELSE 'Available'
    END AS inventory_status

FROM products p
JOIN categories c
ON p.category_id = c.category_id;

CREATE VIEW bulk_group_participation AS
SELECT
    bg.group_id,
    p.product_name,
    bg.target_quantity,
    bg.current_members,
    bg.discount_percentage,
    bg.status,
    c.full_name AS created_by

FROM bulk_groups bg
JOIN products p
ON bg.product_id = p.product_id
JOIN customers c
ON bg.created_by = c.customer_id;

DELIMITER $$

CREATE TRIGGER reduce_stock_after_order
AFTER INSERT ON order_details
FOR EACH ROW
BEGIN
    UPDATE products
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE product_id = NEW.product_id;
END $$

CREATE TRIGGER update_group_members_after_join
AFTER INSERT ON group_members
FOR EACH ROW
BEGIN
    UPDATE bulk_groups
    SET current_members = (
        SELECT COUNT(*)
        FROM group_members
        WHERE group_id = NEW.group_id
    )
    WHERE group_id = NEW.group_id;
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER restore_stock_after_order_cancel
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_status = 'Cancelled' AND OLD.order_status <> 'Cancelled' THEN

        UPDATE products p
        JOIN order_details od 
        ON p.product_id = od.product_id

        SET p.stock_quantity = p.stock_quantity + od.quantity

        WHERE od.order_id = NEW.order_id;

    END IF;
END $$

DELIMITER ;
DELIMITER $$

CREATE TRIGGER update_bulk_group_after_order
AFTER INSERT ON order_details
FOR EACH ROW
BEGIN
    UPDATE bulk_groups bg
    JOIN orders o ON bg.group_id = o.group_id
    SET 
        bg.ordered_quantity = bg.ordered_quantity + NEW.quantity,
        bg.status = CASE
            WHEN bg.ordered_quantity + NEW.quantity >= bg.target_quantity THEN 'Completed'
            ELSE bg.status
        END
    WHERE o.order_id = NEW.order_id
    AND o.group_id IS NOT NULL;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE join_bulk_group(
    IN p_group_id INT,
    IN p_customer_id INT
)
BEGIN
    DECLARE group_status VARCHAR(20);

    START TRANSACTION;

    SELECT status
    INTO group_status
    FROM bulk_groups
    WHERE group_id = p_group_id;

    IF group_status = 'Open' THEN

        INSERT INTO group_members(group_id, customer_id)
        VALUES(p_group_id, p_customer_id);

        COMMIT;

    ELSE

        ROLLBACK;

    END IF;
END $$

CREATE PROCEDURE place_order(
    IN p_customer_id INT,
    IN p_product_id INT,
    IN p_quantity INT,
    IN p_group_id INT,
    IN p_payment_method VARCHAR(30)
)
BEGIN
    DECLARE product_price DECIMAL(10,2);
    DECLARE product_stock INT;
    DECLARE discount DECIMAL(5,2);
    DECLARE final_total DECIMAL(10,2);
    DECLARE new_order_id INT;

    START TRANSACTION;

    SELECT price, stock_quantity
    INTO product_price, product_stock
    FROM products
    WHERE product_id = p_product_id;

    IF product_stock >= p_quantity THEN

        IF p_group_id IS NOT NULL THEN

            SELECT discount_percentage
            INTO discount
            FROM bulk_groups
            WHERE group_id = p_group_id;

        ELSE

            SET discount = 0;

        END IF;

        SET final_total =
            (product_price * p_quantity)
            -
            ((product_price * p_quantity) * discount / 100);

        INSERT INTO orders(
            customer_id,
            group_id,
            total_amount,
            order_status
        )
        VALUES(
            p_customer_id,
            p_group_id,
            final_total,
            'Confirmed'
        );

        SET new_order_id = LAST_INSERT_ID();

        INSERT INTO order_details(
            order_id,
            product_id,
            quantity,
            unit_price
        )
        VALUES(
            new_order_id,
            p_product_id,
            p_quantity,
            product_price
        );

        INSERT INTO payments(
            order_id,
            payment_method,
            payment_status,
            paid_amount
        )
        VALUES(
            new_order_id,
            p_payment_method,
            'Paid',
            final_total
        );

        COMMIT;

    ELSE

        ROLLBACK;

    END IF;
END $$

DELIMITER ;