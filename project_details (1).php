-- Modify the project_checks table to include status and draft number
ALTER TABLE project_checks
ADD COLUMN status ENUM('checked', 'pending', 'rejected') NOT NULL DEFAULT 'pending',
ADD COLUMN draft_number INT NOT NULL DEFAULT 1;

-- Add a unique constraint to prevent duplicate checks
ALTER TABLE project_checks
ADD CONSTRAINT unique_check UNIQUE (project_id, user_id, draft_number);

-- Create a new table for chat messages
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Add a column to projects table to track the current draft number
ALTER TABLE projects
ADD COLUMN current_draft INT NOT NULL DEFAULT 1;