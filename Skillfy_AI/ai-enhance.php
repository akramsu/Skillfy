<?php
// Include Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables from the current directory where .env is located
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get API key from environment variables
$apiKey = $_ENV['GEMINI_API_KEY'] ?? null;

// Debugging: Check if API key is available
if (!$apiKey) {
    echo json_encode(['error' => 'API key not found']);
    exit;
}

// Set headers for JSON response
header('Content-Type: application/json');

// Check if the script is being run via HTTP or CLI
if (php_sapi_name() === 'cli') {
    // Handle CLI input
    $argv = $GLOBALS['argv']; // Get the CLI arguments

    if (count($argv) < 3) {
        echo "Usage: php ai-enhance.php <text> <type>\n";
        exit;
    }

    // Get the text and type from CLI arguments
    $originalText = $argv[1];
    $type = $argv[2];
} else {
    // Handle HTTP request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    // Get the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if text is provided
    if (!isset($data['text']) || !isset($data['type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }

    // Get the text and type
    $originalText = $data['text'];
    $type = $data['type'];
}

// Check the prompt type
$prompt = '';
switch ($type) {
    case 'bio':
        $prompt = "As an expert resume writer, enhance this professional summary. Focus on:\n" .
                 "- Using powerful action verbs\n" .
                 "- Highlighting quantifiable achievements\n" .
                 "- Incorporating relevant industry keywords\n" .
                 "- Maintaining a confident yet professional tone\n" .
                 "- Emphasizing unique value proposition\n\n" .
                 "Original text:\n";
        break;
    case 'jobDescription':
        $prompt = "As an experienced career coach, enhance this job experience entry. Focus on:\n" .
                 "- Leading with strong action verbs\n" .
                 "- Quantifying achievements with metrics\n" .
                 "- Highlighting leadership and initiative\n" .
                 "- Demonstrating problem-solving abilities\n" .
                 "- Including relevant technical skills\n\n" .
                 "Original text:\n";
        break;
    case 'eduDescription':
        $prompt = "As an academic advisor, enhance this educational background. Focus on:\n" .
                 "- Highlighting academic achievements\n" .
                 "- Emphasizing relevant coursework\n" .
                 "- Showcasing research and projects\n" .
                 "- Including technical skills gained\n" .
                 "- Mentioning honors and awards\n\n" .
                 "Original text:\n";
        break;
    case 'projectDescription':
        $prompt = "As a technical project manager, enhance this project description. Focus on:\n" .
                 "- Technical challenges overcome\n" .
                 "- Technologies and methodologies used\n" .
                 "- Impact and measurable results\n" .
                 "- Team collaboration aspects\n" .
                 "- Innovation and problem-solving\n\n" .
                 "Original text:\n";
        break;
    case 'skills':
        $prompt = "As a technical recruiter, enhance this skills section. Focus on:\n" .
                 "- Using industry-standard terminology\n" .
                 "- Organizing skills by category\n" .
                 "- Highlighting proficiency levels\n" .
                 "- Including both technical and soft skills\n" .
                 "- Adding relevant certifications\n\n" .
                 "Original text:\n";
        break;
    default:
        $prompt = "As a professional content writer, enhance this text. Focus on:\n" .
                 "- Clarity and conciseness\n" .
                 "- Professional terminology\n" .
                 "- Impactful statements\n" .
                 "- Measurable outcomes\n" .
                 "- Industry relevance\n\n" .
                 "Original text:\n";
}

// If text is empty, provide a template based on type
if (empty(trim($originalText))) {
    switch ($type) {
        case 'bio':
            $enhancedText = "A results-driven professional with extensive experience in developing innovative solutions and leading cross-functional teams. Skilled in strategic planning, project management, and implementing cutting-edge technologies to drive business growth. Committed to delivering high-quality outcomes while fostering collaboration and continuous improvement.";
            break;
        case 'jobDescription':
            $enhancedText = "Led strategic initiatives that resulted in significant operational improvements and cost savings. Managed cross-functional teams to deliver projects on time and within budget. Implemented innovative solutions that enhanced efficiency and productivity by 30%. Collaborated with stakeholders to align business objectives with technical capabilities.";
            break;
        case 'eduDescription':
            $enhancedText = "Completed rigorous coursework with a focus on practical applications and industry-relevant skills. Participated in collaborative research projects and extracurricular activities that enhanced leadership and teamwork abilities. Received recognition for academic excellence and innovative thinking, graduating in the top 10% of the class.";
            break;
        case 'projectDescription':
            $enhancedText = "Developed a comprehensive solution that addressed key business challenges and delivered measurable results. Implemented cutting-edge technologies and best practices to ensure optimal performance and user experience. Collaborated with stakeholders to gather requirements and iterate on design and functionality, resulting in a 40% improvement in user satisfaction.";
            break;
        default:
            $enhancedText = "Professional content will appear here after enhancement.";
    }
    
    echo json_encode([
        'enhanced' => $enhancedText
    ]);
    exit;
}

// Prepare the request data for Gemini API
$requestData = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => $prompt . $originalText
                ]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 1024,  // Set output limit
    ]
];

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close cURL session
curl_close($ch);

// Check if the request was successful
if ($httpCode !== 200) {
    // If API call fails, use fallback enhancement
    $enhancedText = fallbackEnhancement($originalText, $type);
} else {
    // Parse the response
    $responseData = json_decode($response, true);
    
    // Extract the enhanced text from the response
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $enhancedText = $responseData['candidates'][0]['content']['parts'][0]['text'];
    } else {
        // If response format is unexpected, use fallback enhancement
        $enhancedText = fallbackEnhancement($originalText, $type);
    }
}

// Return the enhanced text
echo json_encode([
    'enhanced' => $enhancedText
]);

// Fallback enhancement logic
function fallbackEnhancement($text, $type) {
    $enhanced = $text;
    
    // Professional vocabulary improvements
    $replacements = [
        '/\bgood\b/i' => 'excellent',
        '/\bnice\b/i' => 'outstanding',
        '/\bdid\b/i' => 'executed',
        '/\bmade\b/i' => 'developed',
        '/\bhelped\b/i' => 'facilitated',
        '/\bworked on\b/i' => 'spearheaded',
        '/\bused\b/i' => 'utilized',
        '/\bimproved\b/i' => 'optimized',
        '/\bcreated\b/i' => 'designed and implemented',
        '/\bmanaged\b/i' => 'orchestrated',
        '/\bfixed\b/i' => 'resolved',
        '/\bstarted\b/i' => 'initiated',
        '/\bfinished\b/i' => 'successfully delivered',
        '/\btalked\b/i' => 'communicated',
        '/\bhelped with\b/i' => 'contributed to',
        '/\bresponsible for\b/i' => 'led',
        '/\bteam player\b/i' => 'collaborative professional'
    ];
    
    foreach ($replacements as $pattern => $replacement) {
        $enhanced = preg_replace($pattern, $replacement, $enhanced);
    }
    
    // Add type-specific enhancements
    switch ($type) {
        case 'bio':
            if (!stripos($enhanced, 'professional')) {
                $enhanced = "Accomplished professional " . lcfirst($enhanced);
            }
            break;
        case 'jobDescription':
            if (!stripos($enhanced, 'result')) {
                $enhanced .= ' Consistently delivered measurable results and exceeded expectations.';
            }
            break;
        case 'eduDescription':
            if (!stripos($enhanced, 'skills')) {
                $enhanced .= ' Developed strong analytical and problem-solving skills.';
            }
            break;
        case 'projectDescription':
            if (!stripos($enhanced, 'success')) {
                $enhanced .= ' Successfully delivered project objectives while maintaining high quality standards.';
            }
            break;
        case 'skills':
            if (!stripos($enhanced, 'proficient')) {
                $enhanced = "Proficient in " . lcfirst($enhanced);
            }
            break;
    }
    
    return $enhanced;
}
