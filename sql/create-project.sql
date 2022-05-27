CREATE DATABASE IF NOT EXISTS `voting_system`;

CREATE TABLE users (
	id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(300) UNIQUE NOT NULL,
    username VARCHAR(300) UNIQUE NOT NULL,
    nic_number VARCHAR(300) UNIQUE NOT NULL,
    `password` VARCHAR(400) NOT NULL,
    profile_photo VARCHAR(300),
    website VARCHAR(300) NULL,
    twitter_username VARCHAR(300) NULL,
    `role` VARCHAR(20) DEFAULT 'voter',
    age_range ENUM('11-20', '21-30', '31-40', '41-50', '51-60', '61-70', '71-80') NOT NULL,
    gender ENUM('male', 'female', 'other'),
    profile_last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL
);

CREATE TABLE polls (
	id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(300),
    `description` VARCHAR(3000),
    owner_id INT REFERENCES users (id) ON DELETE SET NULL,
    end_date DATE NULL,
    `private` BOOLEAN DEFAULT FALSE NOT NULL,
    results_visibility ENUM('public', 'private') DEFAULT 'public'
);

CREATE TABLE candidates (
	id INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(300) NOT NULL,
    poll_id INT REFERENCES poll (id) ON DELETE CASCADE
);

CREATE TABLE votes (
	id INT PRIMARY KEY AUTO_INCREMENT,
    poll_id INT REFERENCES polls (id) ON DELETE CASCADE,
    candidate_id INT REFERENCES cadidates (id) ON DELETE CASCADE,
    user_id INT REFERENCES users (id) ON DELETE CASCADE,
    UNIQUE KEY `unique_vote` (candidate_id, user_id)
);
