<?php
/**
 * ============================================
 * AI-Solutions — Gemini Chat Handler
 * ============================================
 * Receives AJAX messages from the chatbot UI,
 * forwards them to the Google Gemini API with
 * a system prompt, and returns the response.
 * Maintains conversation history in session.
 * ============================================
 */
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Only accept POST requests
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
        'reply' => 'The AI assistant is not configured yet. Please set the GEMINI_API_KEY in the .env file. You can get a key from https://aistudio.google.com/app/apikey'
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

// ── Conversation History (Session-based) ────────────────────────────────────
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Build the contents array with history for multi-turn dialogue
$contents = [];

// Add conversation history (last 10 turns max to stay within token limits)
$history = array_slice($_SESSION['chat_history'], -20);
foreach ($history as $turn) {
    $contents[] = $turn;
}

// Add the new user message
$contents[] = [
    'role'  => 'user',
    'parts' => [['text' => $message]]
];

// ── Call Google Gemini API ───────────────────────────────────────────────────
$model    = 'gemini-2.5-flash';
$endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

$payload = [
    'system_instruction' => [
        'parts' => [['text' => $systemPrompt]]
    ],
    'contents'           => $contents,
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

// ── Update Session History ──────────────────────────────────────────────────
$_SESSION['chat_history'][] = [
    'role'  => 'user',
    'parts' => [['text' => $message]]
];
$_SESSION['chat_history'][] = [
    'role'  => 'model',
    'parts' => [['text' => $reply]]
];

echo json_encode(['reply' => $reply]);
