<?php
include '../config/db_connection.php';

header('Content-Type: application/json');

if(!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    echo json_encode(["ok" => false, "error" => "login_required"]);
    exit();
}

$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

$targetType = isset($input['targetType']) ? $input['targetType'] : '';
$targetId = isset($input['targetId']) ? intval($input['targetId']) : 0;
$vote = isset($input['vote']) ? intval($input['vote']) : 0;

if($userId <= 0) {
    echo json_encode(["ok" => false, "error" => "login_required"]);
    exit();
}

if (($targetType !== 'post' && $targetType !== 'comment') || $targetId <=0 || ($vote !== 1 && $vote !== -1)) {
    echo json_encode(["ok" => false, "error" => "bad_request"]);
    exit();
}

/** @var mysqli $conn */
$stmt = $conn->prepare("SELECT vote FROM community_votes WHERE user_id=? AND target_type=? AND target_id=?");
if (!$stmt) {
    echo json_encode(["ok" => false, "error" => "db_prepare"]);
    exit();
}

$stmt->bind_param("isi", $userId, $targetType, $targetId);
$stmt->execute();
$res = $stmt->get_result();

if($row = $res->fetch_assoc()) {
    $existing = intval($row['vote']);

    if ($existing === $vote) {
        $del = $conn->prepare("DELETE FROM community_votes WHERE user_id=? AND target_type=? AND target_id=?");
        if (!$del) {
            echo json_encode(["ok" => false, "error" => "db_prepare"]);
            exit();
        }
        $del->bind_param("isi", $userId, $targetType, $targetId);
        $del->execute();
    } else {
        $upd = $conn->prepare("UPDATE community_votes SET vote=? WHERE user_id=? AND target_type=? AND target_id=?");
        if (!$upd) {
            echo json_encode(["ok" => false, "error" => "db_prepare"]);
            exit();
        }
        $upd->bind_param("iisi", $vote, $userId, $targetType, $targetId);
        $upd->execute();
    }
} else {
    $ins = $conn->prepare("INSERT INTO community_votes (user_id, target_type, target_id, vote) VALUES (?, ?, ?, ?)");
    if (!$ins) {
        echo json_encode(["ok" => false, "error" => "db_prepare"]);
        exit();
    }
    $ins->bind_param("isii", $userId, $targetType, $targetId, $vote);
    $ins->execute();
}

$stmt2 = $conn->prepare("SELECT COALESCE(SUM(vote),0) AS score FROM community_votes WHERE target_type=? AND target_id=?");
if (!$stmt2) {
    echo json_encode(["ok" => false, "error" => "db_prepare"]);
    exit();
}
$stmt2->bind_param("si", $targetType, $targetId);
$stmt2->execute();
$row2 = $stmt2->get_result()->fetch_assoc();
$score = $row2 ? intval($row2['score']) : 0;

echo json_encode(["ok" => true, "score" => $score]);
exit();