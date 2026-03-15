
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    img VARCHAR(255) NOT NULL,
    available TINYINT(1) DEFAULT 1
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

INSERT IGNORE INTO rooms (room_no) VALUES (2010), (2011),(2012);