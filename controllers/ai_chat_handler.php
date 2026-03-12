<?php
session_start(); // I don't need to start the session everytime because I'm including db_connection which has the session_start() but I just do it just in case.
include("../config/db_connection.php");

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
// $userMessage= $input['message'] ?? '';
$userMessage = isset($input['message']) ? $input['message'] : '';
//I'm running php 8+ but there's error in the IDE setting in IntelliJ Ultimate which makes it think I'm using php update that is older than 7.0
//and the ?? operator was only allowed in php 7.0 and newer so I use the isset as the ternary operator for compatibility purposes.

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Error: Message cannot be empty.']);
    exit();
}

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

/** * @var mysqli $conn */
if ($userId) {
    $stmt = $conn->prepare("INSERT INTO ai_chat (user_id, sender, message) VALUES(?,'user',?)");
    $stmt->bind_param('is', $userId, $userMessage);
    $stmt->execute();
}

$apiUrl = "https://api-interface.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2";

$prompt = "<s>[INST] You Are Kali, an elite cybersecurity AI assistant for the Cyber Lens platform. You are helpful but strictly focused on cybersecurity, ethical hacking, and network defense. Keep answers technical, concise, professional, and match the user's enrergy. Answer questions unrelated to cybersecrity but remind the user that you are professionl cybersecurity assistant. \n\nUser Question: ". $userMessage . " [/INST]";

$data = [
   'input' => $prompt,
    'parameters' => [
        'max_new_token' => 250,
        'temperature' => 0.7,
        'return_full_text' => false,
    ]
];

$ch= curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_decode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . HUGGINGFACE_API_KEY]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

