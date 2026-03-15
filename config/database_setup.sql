
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    img VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS rooms (
    room_no INT PRIMARY KEY
);


CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    status ENUM('processing', 'out for delivery', 'delivered') DEFAULT 'processing',
    notes TEXT,
    room_no INT,
    total_price DECIMAL(10,2) DEFAULT 0.00
);


CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) DEFAULT 0.00, 
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);


INSERT IGNORE INTO products (id, name, price, img) VALUES 
(1, 'Espresso Coffee', 25.00, 'coffee.png'),
(2, 'Lipton Tea', 10.00, 'tea.png'),
(3, 'Cold Cola', 15.00, 'cola.png'),
(4, 'Ice Coffee', 45.00, 'ice-coffee.png'),
(5, 'Fresh Orange', 30.00, 'orange.png'),
(6, 'Caramel Frappe', 60.00, 'frappe.png'),
(7, 'Hot Chocolate', 40.00, 'hot-chocolate.png'),
(8, 'Turkish Coffee', 20.00, 'turkish.png'),
(9, 'Mango Smoothie', 35.00, 'mango.png');

INSERT IGNORE INTO rooms (room_no) VALUES (2010), (2011), (2012);