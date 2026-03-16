<?php
//Note: I used a lot of notes here because it's my first time implementing AI API so I'll need the note for self revision.

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // I don't need to start the session everytime because I'm including db_connection which has the session_start() but I just do it just in case.
}
include("../config/db_connection.php");
include("../config/ai_keys.php");

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true); //* 1.user input
// $userMessage= $input['message'] ?? '';
$userMessage = isset($input['message']) ? $input['message'] : '';
//I'm running php 8+ but there's error in the IDE setting in IntelliJ Ultimate which makes it think I'm using php update that is older than 7.0
//and the ?? operator was only allowed in php 7.0 and newer so I use the isset as the ternary operator for compatibility purposes.

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Error: Message cannot be empty.']);
    exit();
}

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

/** * @var mysqli $conn */ //**2. Save user message to DB
if ($userId) {
    $stmt = $conn->prepare("INSERT INTO ai_chat (user_id, sender, message) VALUES(?,'user',?)");
    $stmt->bind_param('is', $userId, $userMessage);
    $stmt->execute();
}
$apiUrl = "https://api.groq.com/openai/v1/chat/completions";
//***3. Call Hugging Face API (model: Mistral-7B-Instruct) https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2
//$apiUrl = "https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.3";
//$apiUrl = "https://api-inference.huggingface.co/models/google/gemma-7b-it";
//$apiUrl = "https://api-inference.huggingface.co/models/microsoft/Phi-3-mini-4k-instruct";
//$apiUrl="https://api-inference.huggingface.co/models/tiiuae/falcon-7b-instruct";
//$apiUrl = "https://router.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2";
//$apiUrl = "https://router.huggingface.co/hf-inference/models/mistralai/Mistral-7B-Instruct-v0.2";
//$apiUrl = "https://router.huggingface.co/v1/chat/completions";


/* curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    $authHeader
]); */

//AI behavior
//$prompt = "<s>[INST] You Are Kali, an elite cybersecurity AI assistant for the Cyber Lens platform. You are helpful but strictly focused on cybersecurity, ethical hacking, and network defense. Keep answers technical, concise, professional, and match the user's enrergy. Answer questions unrelated to cybersecrity but remind the user that you are professionl cybersecurity assistant. \n\nUser Question: ". $userMessage . " [/INST]";
$prompt = "You are Kali, an elite cybersecurity AI assistant. Be technical and concise. Question: " . $userMessage . "\nAnswer:";

$system = <<<SYS
You are CyberBot, the friendly AI assistant inside CyberLens website.

Cyber Lens context (use this to answer questions about the site):
-Purpose: Cyber Lens is a community-driven threat intelligence web platform that bridges the gap between static learning resources and complex enterprise tools by providing simplified, real-time vulnerability intelligence and interactive analysis.
Main features:
1) Live Threat Intelligence dashboard (real-time CVE trends, prevalence, severity)
2) Clear severity classification (CVSS-style) with simplified explanations
3) Attack Knowledge Base (structured attack/vulnerability categories + related content)
4) Community collaboration (Q&A / discussions)
5)Research publishing + IEEE referencing validation 
-Target users: IT undergraduates/ junior analysts, developers, independent researchers
-Integrations: CVEdetails.com API for CVE statistics, severity and trends

Behavior: 
- If the user asks about CyberLens (features, how to use it, what it offers), answer as a product support using context above.
- If the user asks cybersecurity questions, answer clearly and practically with safe, defensive guidance.
-If the user asks for illegal/harmful hacking, refuse and provide safe alternatives (defensive guidance, learning resources, lab setup).
- Tone: friendly and human. Use short paragraphs + bullet points. Don't sound formal. Ask at most 1 follow-up question if needed.
SYS;

$data = [
    "model" => "llama-3.1-8b-instant",
    "messages" => [
        ["role" => "system", "content" => "$system"],
        ["role" => "user", "content" => $userMessage],
    ],
    "max_tokens" => 300,
    "temperature" => 0.8
];

/* $data = [
   'inputs' => $prompt,
    'parameters' => [
        'max_new_tokens' => 250,
        'temperature' => 0.7,
        'return_full_text' => false, //just the latest/new answer
    ]
]; */


//initialize cURL
$ch= curl_init();

curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
////
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
////
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json',
    'Authorization: Bearer ' . GROQ_API_KEY]);

// TEMP DEBUG
// echo json_encode(['debug_url' => $apiUrl]); exit();
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

//****4. process the AI response
$botReplay = "Error: System Offline.";

if ($httpCode == 200) {
    $response = json_decode($result, true);
    //Note: Hugging Face returns an array of objs
    if (isset($response['choices'][0]['message']['content'])) {
        $botReplay = trim($response['choices'][0]['message']['content']);
    } else {
        $botReplay = "Error: AI returned an empty response.";
    }
} else {
    //Also a Note: Fallback for debugging
   // $botReplay = "System Error (Code: $httpCode). Please try again later.";
        $botReplay = "System Error (Code: $httpCode)." . $result;
}

//the purpose of the following if-statement is to save the AI response to the DB
if ($userId) {
    $stmt = $conn->prepare("INSERT INTO ai_chat (user_id, sender, message) VALUES(?,'bot',?)");
    $stmt->bind_param('is', $userId, $botReplay);
    $stmt->execute();
}

//the following line of code is to show the AI response to the front-end
echo json_encode(['reply' => $botReplay]);
