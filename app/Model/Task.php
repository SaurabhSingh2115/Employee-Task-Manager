<?php 

function insert_task($conn, $data){
    $sql = "INSERT INTO tasks (title, description, assigned_to, due_date, file_path) VALUES(?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

function get_all_tasks($conn){
	$sql = "SELECT * FROM tasks ORDER BY id DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	if($stmt->rowCount() > 0){
		$tasks = $stmt->fetchAll();
	}else $tasks = 0;

	return $tasks;
}
function get_all_tasks_due_today($conn){
	$sql = "SELECT * FROM tasks WHERE due_date = CURDATE() AND status != 'completed' ORDER BY id DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	if($stmt->rowCount() > 0){
		$tasks = $stmt->fetchAll();
	}else $tasks = 0;

	return $tasks;
}
function count_tasks_due_today($conn){
	$sql = "SELECT id FROM tasks WHERE due_date = CURDATE() AND status != 'completed'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	return $stmt->rowCount();
}

function get_all_tasks_overdue($conn){
	$sql = "SELECT * FROM tasks WHERE due_date < CURDATE() AND status != 'completed' ORDER BY id DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	if($stmt->rowCount() > 0){
		$tasks = $stmt->fetchAll();
	}else $tasks = 0;

	return $tasks;
}
function count_tasks_overdue($conn){
	$sql = "SELECT id FROM tasks WHERE due_date < CURDATE() AND status != 'completed'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	return $stmt->rowCount();
}


function get_all_tasks_NoDeadline($conn){
	$sql = "SELECT * FROM tasks WHERE status != 'completed' AND due_date IS NULL OR due_date = '0000-00-00' ORDER BY id DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	if($stmt->rowCount() > 0){
		$tasks = $stmt->fetchAll();
	}else $tasks = 0;

	return $tasks;
}
function count_tasks_NoDeadline($conn){
	$sql = "SELECT id FROM tasks WHERE status != 'completed' AND due_date IS NULL OR due_date = '0000-00-00'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	return $stmt->rowCount();
}



function delete_task($conn, $data){
	$sql = "DELETE FROM tasks WHERE id=? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute($data);
}


function get_task_by_id($conn, $id){
	$sql = "SELECT * FROM tasks WHERE id =? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	if($stmt->rowCount() > 0){
		$task = $stmt->fetch();
	}else $task = 0;

	return $task;
}
function count_tasks($conn){
	$sql = "SELECT id FROM tasks";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	return $stmt->rowCount();
}

function update_task($conn, $data){
	$sql = "UPDATE tasks SET title=?, description=?, assigned_to=?, due_date=? WHERE id=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute($data);
}

function update_task_status($conn, $data){
	$sql = "UPDATE tasks SET status=? WHERE id=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute($data);
}


function get_all_tasks_by_id($conn, $id){
	$sql = "SELECT * FROM tasks WHERE assigned_to=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	if($stmt->rowCount() > 0){
		$tasks = $stmt->fetchAll();
	}else $tasks = 0;

	return $tasks;
}



function count_pending_tasks($conn){
	$sql = "SELECT id FROM tasks WHERE status = 'pending'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	return $stmt->rowCount();
}

function count_in_progress_tasks($conn){
	$sql = "SELECT id FROM tasks WHERE status = 'in_progress'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	return $stmt->rowCount();
}

function count_completed_tasks($conn){
	$sql = "SELECT id FROM tasks WHERE status = 'completed'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([]);

	return $stmt->rowCount();
}


function count_my_tasks($conn, $id){
	$sql = "SELECT id FROM tasks WHERE assigned_to=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	return $stmt->rowCount();
}

function count_my_tasks_overdue($conn, $id){
	$sql = "SELECT id FROM tasks WHERE due_date < CURDATE() AND status != 'completed' AND assigned_to=? AND due_date != '0000-00-00'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	return $stmt->rowCount();
}

function count_my_tasks_NoDeadline($conn, $id){
	$sql = "SELECT id FROM tasks WHERE assigned_to=? AND status != 'completed' AND due_date IS NULL OR due_date = '0000-00-00'";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	return $stmt->rowCount();
}

function count_my_pending_tasks($conn, $id){
	$sql = "SELECT id FROM tasks WHERE status = 'pending' AND assigned_to=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	return $stmt->rowCount();
}

function count_my_in_progress_tasks($conn, $id){
	$sql = "SELECT id FROM tasks WHERE status = 'in_progress' AND assigned_to=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	return $stmt->rowCount();
}

function count_my_completed_tasks($conn, $id){
	$sql = "SELECT id FROM tasks WHERE status = 'completed' AND assigned_to=?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	return $stmt->rowCount();
}

function calculate_user_points($conn) {
    $query = "
        SELECT 
            u.id AS user_id, 
            u.full_name, 
            IFNULL(SUM(
                CASE 
                    WHEN t.status = 'completed' THEN 
                        CASE 
                            WHEN CURRENT_DATE <= t.due_date THEN 10
                            ELSE -5
                        END
                    ELSE 0
                END
            ), 0) AS points
        FROM users u
        LEFT JOIN tasks t ON u.id = t.assigned_to
        WHERE 
            u.role = 'employee' -- Only include employees
        GROUP BY u.id, u.full_name
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $user_points = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update database and trigger notifications if needed
    foreach ($user_points as $user) {
        $current_points = $user['points'];

        // Update user points in the database
        update_user_points($conn, $user['user_id'], $current_points);

        // Send notification if points fall below 0
        if ($current_points < 0) {
            create_performance_notification($conn, $user['user_id'], $current_points);
        }
    }
    
    return $user_points;
}

function update_user_points($conn, $user_id, $points) {
    $query = "UPDATE users SET points = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$points, $user_id]);
}

function create_performance_notification($conn, $user_id, $points) {
    // Check if a notification for low points exists when points are below 0
    $check_query = "SELECT COUNT(*) as count FROM notifications 
                    WHERE recipient = ? 
                    AND type = 'performance_warning' 
                    AND DATE(date) = CURRENT_DATE";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->execute([$user_id]);
    $existing_notification = $check_stmt->fetch(PDO::FETCH_ASSOC);

    // Create notification only if no similar notification exists today
    if ($existing_notification['count'] == 0) {
        $message = "Performance Alert: Your current performance points are {$points}. Your performance has been underwhelming. Please improve your task completion rate.";
        
        $insert_query = "INSERT INTO notifications 
                         (recipient, message, type, date) 
                         VALUES (?, ?, 'performance_warning', CURRENT_TIMESTAMP)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->execute([$user_id, $message]);
    }
}

function mark_task_completed($conn, $task_id) {
    // Fetch the task details
    $query = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        $due_date = new DateTime($task['due_date']);
        $current_date = new DateTime();

        // Update task status 
        $query = "UPDATE tasks SET status = 'completed' WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$task_id]);

        // Calculate points
        $points = ($current_date <= $due_date) ? 10 : -5;

        // Fetch current user points
        $query = "SELECT points FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$task['assigned_to']]);
        $current_points = $stmt->fetchColumn();

        // Update points (allow below 0)
        $new_points = $current_points + $points;

        // Update user points in database
        $query = "UPDATE users SET points = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$new_points, $task['assigned_to']]);

        // Trigger notification if points drop below 0
        if ($new_points < 0 && $current_points >= 0) { 
            // Notify only if this update caused points to drop below 0
            create_performance_notification($conn, $task['assigned_to'], $new_points);
        }
    }
}
