<?php
/**
 * ============================================
 * AI-Solutions — Gemini Chat Handler
 * ============================================
 * Receives AJAX messages from the chatbot UI,
 * stores/retrieves messages from SQLite, forwards
 * to Gemini API, and links guest chats upon login.
 * ============================================
 */
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Ensure unique chat session ID exists
if (empty($_SESSION['chat_session_id'])) {
    $_SESSION['chat_session_id'] = bin2hex(random_bytes(16));
}
$sessionId = $_SESSION['chat_session_id'];
$userId = isLoggedIn() ? $_SESSION['user_id'] : null;

// Link guest chats if logged in
if ($userId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE chat_messages SET user_id = :user_id WHERE session_id = :session_id AND user_id IS NULL");
        $stmt->execute([':user_id' => $userId, ':session_id' => $sessionId]);
    } catch (PDOException $e) {
        error_log('Error linking guest messages: ' . $e->getMessage());
    }
}

// ── GET Request: Fetch Chat History ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $db = getDB();
        if ($userId) {
            $stmt = $db->prepare("SELECT sender, message, created_at FROM chat_messages WHERE user_id = :user_id ORDER BY id ASC");
            $stmt->execute([':user_id' => $userId]);
        } else {
            $stmt = $db->prepare("SELECT sender, message, created_at FROM chat_messages WHERE session_id = :session_id AND user_id IS NULL ORDER BY id ASC");
            $stmt->execute([':session_id' => $sessionId]);
        }
        $messages = $stmt->fetchAll();
        echo json_encode(['messages' => $messages]);
    } catch (PDOException $e) {
        error_log('Error fetching chat history: ' . $e->getMessage());
        echo json_encode(['messages' => []]);
    }
    exit;
}

// Only accept POST requests for sending messages
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['reply' => 'Method not allowed.']);
    exit;
}

// ── Parse Incoming JSON ─────────────────────────────────────────────────────
$input   = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');

if ($message === '') {
    echo json_encode(['reply' => 'Please enter a message.']);
    exit;
}

// ── API Key Check ───────────────────────────────────────────────────────────
$apiKey = env('GEMINI_API_KEY', '');
if ($apiKey === '' || $apiKey === 'your_gemini_api_key_here') {
    echo json_encode([
        'reply' => 'The AI assistant is not configured yet. Please set the GEMINI_API_KEY in the .env file.'
    ]);
    exit;
}

// ── System Prompt (Company Context) ─────────────────────────────────────────
$systemPrompt = <<<PROMPT
You are the AI-Solutions professional consultant chatbot. You represent AI-Solutions, a forward-thinking startup that leverages artificial intelligence to provide:

1. **Custom AI Software** — Machine learning pipelines, NLP, and computer vision.
2. **Virtual Assistants** — Intelligent conversational agents for customer support and operations.
3. **Rapid Prototyping** — Concept to clickable prototype in days.
4. **Data Analytics** — Dashboards, predictive models, and automated reporting.
5. **API Integration** — Connecting AI into existing tech stacks.
6. **AI Consulting** — Strategy, model selection, and implementation guidance.

Your behaviour rules:
- Be professional, friendly, and concise.
- Answer questions about AI-Solutions' services, pricing approach, and capabilities.
- If asked about pricing, explain that AI-Solutions offers custom quotes based on project scope and invite them to fill out the Contact form.
- If asked something unrelated to AI or the company, politely redirect the conversation.
- Never reveal that you are a language model or discuss your internals.
- Respond in the same language the user writes in.
PROMPT;

// ── Save User Message to DB ─────────────────────────────────────────────────
try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO chat_messages (session_id, user_id, sender, message) VALUES (:session_id, :user_id, 'user', :message)");
    $stmt->execute([
        ':session_id' => $sessionId,
        ':user_id'    => $userId,
        ':message'    => $message
    ]);
} catch (PDOException $e) {
    error_log('Error saving user message: ' . $e->getMessage());
}

// ── Load Conversation History for Gemini ─────────────────────────────────────
$historyLimit = 16; // last 16 messages (8 turns) to build context
$history = [];
try {
    $db = getDB();
    if ($userId) {
        $stmt = $db->prepare("SELECT sender, message FROM chat_messages WHERE user_id = :user_id ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    } else {
        $stmt = $db->prepare("SELECT sender, message FROM chat_messages WHERE session_id = :session_id AND user_id IS NULL ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':session_id', $sessionId, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $historyLimit, PDO::PARAM_INT);
    $stmt->execute();
    $rawHistory = array_reverse($stmt->fetchAll());
    
    foreach ($rawHistory as $row) {
        $history[] = [
            'role'  => $row['sender'] === 'user' ? 'user' : 'model',
            'parts' => [['text' => $row['message']]]
        ];
    }
} catch (PDOException $e) {
    error_log('Error building chat history context: ' . $e->getMessage());
    // Fallback to current message only
    $history = [
        [
            'role'  => 'user',
            'parts' => [['text' => $message]]
        ]
    ];
}

// ── Call Google Gemini API ───────────────────────────────────────────────────
$model    = 'gemini-2.5-flash';
$endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

$payload = [
    'system_instruction' => [
        'parts' => [['text' => $systemPrompt]]
    ],
    'contents'           => $history,
    'generationConfig'   => [
        'temperature'     => 0.7,
        'topP'            => 0.9,
        'maxOutputTokens' => 512,
    ],
];

$ch = curl_init($endpoint);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

// ── Process Response ────────────────────────────────────────────────────────
if ($curlErr) {
    echo json_encode(['reply' => 'Sorry, I\'m having trouble connecting right now. Please try again later.']);
    exit;
}

if ($httpCode !== 200) {
    $errorData = json_decode($response, true);
    $errorMsg  = $errorData['error']['message'] ?? 'Unknown API error.';
    error_log("Gemini API error ($httpCode): $errorMsg");
    echo json_encode(['reply' => 'I encountered an issue processing your request. Please try again shortly.']);
    exit;
}

$data  = json_decode($response, true);
$reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'I\'m sorry, I couldn\'t generate a response.';

// ── Save Bot Reply to DB ────────────────────────────────────────────────────
try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO chat_messages (session_id, user_id, sender, message) VALUES (:session_id, :user_id, 'bot', :message)");
    $stmt->execute([
        ':session_id' => $sessionId,
        ':user_id'    => $userId,
        ':message'    => $reply
    ]);
} catch (PDOException $e) {
    error_log('Error saving bot reply: ' . $e->getMessage());
}

echo json_encode(['reply' => $reply]);
