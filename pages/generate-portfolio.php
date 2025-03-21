<?php
// Set error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug log function
function debug_log($message) {
    $log_file = 'debug_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "$timestamp: $message\n", FILE_APPEND);
}

// Create uploads directory structure with proper permissions
$uploadsDir = 'uploads/';
$profileDir = $uploadsDir . 'profile/';
$projectsDir = $uploadsDir . 'projects/';

// Ensure directories exist with proper permissions
if (!file_exists($uploadsDir)) {
    if (!mkdir($uploadsDir, 0777, true)) {
        debug_log("ERROR: Failed to create uploads directory");
    } else {
        chmod($uploadsDir, 0777);
        debug_log("Created uploads directory with full permissions");
    }
}

if (!file_exists($profileDir)) {
    if (!mkdir($profileDir, 0777, true)) {
        debug_log("ERROR: Failed to create profile directory");
    } else {
        chmod($profileDir, 0777);
        debug_log("Created profile directory with full permissions");
    }
}

if (!file_exists($projectsDir)) {
    if (!mkdir($projectsDir, 0777, true)) {
        debug_log("ERROR: Failed to create projects directory");
    } else {
        chmod($projectsDir, 0777);
        debug_log("Created projects directory with full permissions");
    }
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log the form submission
    debug_log("Form submitted. Processing data...");
    debug_log("POST data: " . print_r($_POST, true));
    debug_log("FILES data: " . print_r($_FILES, true));
    
    // Get form data
    $template = $_POST['template'] ?? 'modern';
    $fullName = $_POST['fullName'] ?? '';
    $jobTitle = $_POST['jobTitle'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';
    $website = $_POST['website'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    $github = $_POST['github'] ?? '';
    
    // Process skills
    $skills = [];
    if (isset($_POST['skills']) && is_array($_POST['skills'])) {
        foreach ($_POST['skills'] as $skill) {
            if (!empty($skill['name'])) {
                $skills[] = $skill;
            }
        }
    }
    
    // Process experience
    $experience = [];
    if (isset($_POST['experience']) && is_array($_POST['experience'])) {
        foreach ($_POST['experience'] as $exp) {
            if (!empty($exp['title']) && !empty($exp['company'])) {
                $experience[] = $exp;
            }
        }
    }
    
    // Process education
    $education = [];
    if (isset($_POST['education']) && is_array($_POST['education'])) {
        foreach ($_POST['education'] as $edu) {
            if (!empty($edu['degree']) && !empty($edu['school'])) {
                $education[] = $edu;
            }
        }
    }
    
    // Process projects
    $projects = [];
    if (isset($_POST['projects']) && is_array($_POST['projects'])) {
        foreach ($_POST['projects'] as $index => $proj) {
            if (!empty($proj['title'])) {
                // Handle project image upload
                $projectImage = '';
                
                debug_log("Processing project image for index $index");
                
                // Check if there's a project image file
                if (isset($_FILES['projects']) && 
                    isset($_FILES['projects']['name'][$index]['image']) && 
                    !empty($_FILES['projects']['name'][$index]['image'])) {
                    
                    debug_log("Project image found: " . $_FILES['projects']['name'][$index]['image']);
                    debug_log("Project image error: " . $_FILES['projects']['error'][$index]['image']);
                    debug_log("Project image tmp_name: " . $_FILES['projects']['tmp_name'][$index]['image']);
                    
                    $projectImageTmp = $_FILES['projects']['tmp_name'][$index]['image'];
                    $projectImageName = basename($_FILES['projects']['name'][$index]['image']);
                    $projectImageName = uniqid() . '_' . $projectImageName;
                    $projectImagePath = $projectsDir . $projectImageName;
                    
                    debug_log("Attempting to upload project image to: $projectImagePath");
                    
                    // Ensure the tmp file exists and is readable
                    if (file_exists($projectImageTmp) && is_readable($projectImageTmp)) {
                        debug_log("Temporary file exists and is readable");
                        
                        // Try to move the uploaded file
                        if (move_uploaded_file($projectImageTmp, $projectImagePath)) {
                            debug_log("Successfully moved uploaded file to: $projectImagePath");
                            
                            // Verify the file was actually created
                            if (file_exists($projectImagePath)) {
                                debug_log("Verified file exists at: $projectImagePath");
                                $projectImage = $projectImagePath;
                                
                                // Set proper permissions
                                chmod($projectImagePath, 0644);
                                debug_log("Set permissions on file");
                            } else {
                                debug_log("ERROR: File does not exist after move_uploaded_file: $projectImagePath");
                            }
                        } else {
                            debug_log("ERROR: Failed to move uploaded file. Error: " . error_get_last()['message']);
                            
                            // Try a direct copy as fallback
                            if (copy($projectImageTmp, $projectImagePath)) {
                                debug_log("Successfully copied file as fallback");
                                $projectImage = $projectImagePath;
                                chmod($projectImagePath, 0644);
                            } else {
                                debug_log("ERROR: Failed to copy file as fallback. Error: " . error_get_last()['message']);
                            }
                        }
                    } else {
                        debug_log("ERROR: Temporary file does not exist or is not readable: $projectImageTmp");
                    }
                } else {
                    debug_log("No valid project image file found for project $index");
                }
                
                $proj['image'] = $projectImage;
                $projects[] = $proj;
            }
        }
    }
    
    // Handle profile image upload
    $profileImage = '';
    debug_log("Processing profile image");
    
    if (isset($_FILES['profileImage']) && 
        !empty($_FILES['profileImage']['name'])) {
        
        debug_log("Profile image found: " . $_FILES['profileImage']['name']);
        debug_log("Profile image error: " . $_FILES['profileImage']['error']);
        debug_log("Profile image tmp_name: " . $_FILES['profileImage']['tmp_name']);
        
        $profileImageTmp = $_FILES['profileImage']['tmp_name'];
        $profileImageName = basename($_FILES['profileImage']['name']);
        $profileImageName = uniqid() . '_' . $profileImageName;
        $profileImagePath = $profileDir . $profileImageName;
        
        debug_log("Attempting to upload profile image to: $profileImagePath");
        
        // Ensure the tmp file exists and is readable
        if (file_exists($profileImageTmp) && is_readable($profileImageTmp)) {
            debug_log("Temporary file exists and is readable");
            
            // Try to move the uploaded file
            if (move_uploaded_file($profileImageTmp, $profileImagePath)) {
                debug_log("Successfully moved uploaded file to: $profileImagePath");
                
                // Verify the file was actually created
                if (file_exists($profileImagePath)) {
                    debug_log("Verified file exists at: $profileImagePath");
                    $profileImage = $profileImagePath;
                    
                    // Set proper permissions
                    chmod($profileImagePath, 0644);
                    debug_log("Set permissions on file");
                } else {
                    debug_log("ERROR: File does not exist after move_uploaded_file: $profileImagePath");
                }
            } else {
                debug_log("ERROR: Failed to move uploaded file. Error: " . error_get_last()['message']);
                
                // Try a direct copy as fallback
                if (copy($profileImageTmp, $profileImagePath)) {
                    debug_log("Successfully copied file as fallback");
                    $profileImage = $profileImagePath;
                    chmod($profileImagePath, 0644);
                } else {
                    debug_log("ERROR: Failed to copy file as fallback. Error: " . error_get_last()['message']);
                }
            }
        } else {
            debug_log("ERROR: Temporary file does not exist or is not readable: $profileImageTmp");
        }
    } else {
        debug_log("No valid profile image file found");
    }
    
    // Generate the portfolio based on the selected template
    $portfolioHtml = generatePortfolio($template, [
        'fullName' => $fullName,
        'jobTitle' => $jobTitle,
        'email' => $email,
        'phone' => $phone,
        'location' => $location,
        'website' => $website,
        'bio' => $bio,
        'linkedin' => $linkedin,
        'github' => $github,
        'profileImage' => $profileImage,
        'skills' => $skills,
        'experience' => $experience,
        'education' => $education,
        'projects' => $projects
    ]);
    
    // Generate a unique filename for the portfolio
    $portfolioFilename = 'portfolio_' . uniqid() . '.html';
    $portfolioPath = 'downloads/' . $portfolioFilename;
    
    // Create downloads directory if it doesn't exist
    if (!file_exists('downloads/')) {
        mkdir('downloads/', 0777, true);
        chmod('downloads/', 0777);
        debug_log("Created downloads directory with full permissions");
    }
    
    // Save the portfolio HTML to a file
    file_put_contents($portfolioPath, $portfolioHtml);
    debug_log("Portfolio HTML saved to: $portfolioPath");
    
    // If download was requested, redirect to download script
    if (isset($_POST['download']) && $_POST['download'] === '1') {
        debug_log("Download requested. Redirecting to download-portfolio.php");
        header('Location: download-portfolio.php?file=' . $portfolioFilename);
        exit;
    }
    
    // Display the portfolio
    echo $portfolioHtml;
    exit;
}

/**
 * Generate the portfolio HTML based on the template and data
 */
function generatePortfolio($template, $data) {
    // Extract data for easier access
    extract($data);
    
    debug_log("Generating portfolio with template: $template");
    debug_log("Profile image path: $profileImage");
    
    // Start building the HTML
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($fullName) . ' - Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
';
    
    // Add template-specific styles
    if ($template === 'modern') {
        $html .= '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">';
        $html .= generateModernTemplate($data);
    } elseif ($template === 'executive') {
        $html .= '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">';
        $html .= generateExecutiveTemplate($data);
    } elseif ($template === 'creative-tech') {
        $html .= '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">';
        $html .= generateCreativeTechTemplate($data);
    } else {
        // Default to modern template
        $html .= '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">';
        $html .= generateModernTemplate($data);
    }
    
    return $html;
}

/**
 * Generate the Modern template
 */
function generateModernTemplate($data) {
    // Extract data for easier access
    extract($data);
    
    debug_log("Generating Modern template");
    
    // Start building the template-specific HTML
    $html = '
    <style>
        /* Modern template styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Inter", sans-serif;
            color: #333;
            line-height: 1.6;
            background-color: #f8fafc;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #0ea5e9, #14b8a6);
            color: white;
            padding: 8rem 0 4rem;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z\' fill=\'%23ffffff\' fill-opacity=\'0.1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E");
            opacity: 0.5;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .profile-image-container {
            flex-shrink: 0;
            margin-right: 3rem;
            position: relative;
        }
        
        .profile-image {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #64748b;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .name {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: white;
        }
        
        .job-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1.5rem;
        }
        
        .contact-items {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .contact-item {
            display: flex;
            align-items: center;
        }
        
        .contact-icon {
            margin-right: 0.5rem;
        }
        
        .bio {
            max-width: 700px;
            font-size: 1.125rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.9);
        }
        
        /* Main Content */
        .main-content {
            padding: 4rem 0;
        }
        
        .section {
            margin-bottom: 4rem;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 2.5rem;
        }
        
        .section-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0ea5e9;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }
        
        .section-title::before {
            content: "";
            display: block;
            width: 2rem;
            height: 0.25rem;
            background: linear-gradient(to right, #0ea5e9, #14b8a6);
            border-radius: 1rem;
            margin-right: 1rem;
        }
        
        /* Skills */
        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .skill-item {
            background: linear-gradient(135deg, #0ea5e9, #14b8a6);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 2rem;
            font-weight: 500;
            font-size: 0.95rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Experience & Education */
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            padding-bottom: 2rem;
            border-left: 2px solid #e5e7eb;
        }
        
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        
        .timeline-item::before {
            content: "";
            position: absolute;
            left: -0.5rem;
            top: 0;
            width: 1rem;
            height: 1rem;
            background: linear-gradient(135deg, #0ea5e9, #14b8a6);
            border-radius: 50%;
        }
        
        .timeline-header {
            margin-bottom: 0.75rem;
        }
        
        .timeline-title {
            font-weight: 700;
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        
        .timeline-subtitle {
            color: #0ea5e9;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .timeline-date {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }
        
        /* Projects */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .project-card {
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s;
        }
        
        .project-card:hover {
            transform: translateY(-8px);
        }
        
        .project-image {
            width: 100%;
            height: 200px;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 1.5rem;
        }
        
        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .project-content {
            padding: 1.5rem;
        }
        
        .project-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: #1e293b;
        }
        
        .project-description {
            color: #475569;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        
        .project-tech {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .tech-tag {
            background-color: #e0f2fe;
            color: #0ea5e9;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .project-link {
            display: inline-block;
            background: linear-gradient(135deg, #0ea5e9, #14b8a6);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: opacity 0.2s;
        }
        
        .project-link:hover {
            opacity: 0.9;
        }
        
        /* Footer */
        .footer {
            background-color: #1e293b;
            color: white;
            padding: 3rem 0;
            text-align: center;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            font-size: 1.25rem;
            transition: background-color 0.2s;
        }
        
        .social-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .copyright {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-image-container {
                margin-right: 0;
                margin-bottom: 2rem;
            }
            
            .contact-items {
                justify-content: center;
            }
            
            .projects-grid {
                grid-template-columns: 1fr;
            }
            
            .name {
                font-size: 2.5rem;
            }
            
            .section {
                padding: 1.5rem;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .section-title::before {
                width: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 1rem;
            }
            
            .header {
                padding: 6rem 0 3rem;
            }
            
            .profile-image {
                width: 150px;
                height: 150px;
            }
            
            .name {
                font-size: 2rem;
            }
            
            .job-title {
                font-size: 1.25rem;
            }
            
            .bio {
                font-size: 1rem;
            }
            
            .section {
                padding: 1.25rem;
            }
            
            .timeline-item {
                padding-left: 1.5rem;
            }
            
            .timeline-title {
                font-size: 1.125rem;
            }
            
            .timeline-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
            ';
    
    // Add profile image if available
    if (!empty($profileImage) && file_exists($profileImage)) {
        debug_log("Adding profile image to HTML: $profileImage");
        $html .= '<div class="profile-image-container">
                    <img src="' . htmlspecialchars($profileImage) . '" alt="' . htmlspecialchars($fullName) . '" class="profile-image">
                </div>';
    } else {
        debug_log("Using initial for profile image");
        $initial = !empty($fullName) ? substr($fullName, 0, 1) : 'P';
        $html .= '<div class="profile-image-container">
                    <div class="profile-image">' . htmlspecialchars($initial) . '</div>
                </div>';
    }
    
    $html .= '<div class="profile-info">
                    <h1 class="name">' . htmlspecialchars($fullName) . '</h1>
                    <h2 class="job-title">' . htmlspecialchars($jobTitle) . '</h2>
                    <p class="bio">' . nl2br(htmlspecialchars($bio)) . '</p>
                    <div class="contact-items">';
    
    if (!empty($email)) {
        $html .= '<div class="contact-item">
                    <span class="contact-icon">📧</span>
                    <a href="mailto:' . htmlspecialchars($email) . '" style="color: inherit; text-decoration: none;">' . htmlspecialchars($email) . '</a>
                </div>';
    }
    
    if (!empty($phone)) {
        $html .= '<div class="contact-item">
                    <span class="contact-icon">📱</span>
                    <a href="tel:' . htmlspecialchars($phone) . '" style="color: inherit; text-decoration: none;">' . htmlspecialchars($phone) . '</a>
                </div>';
    }
    
    if (!empty($location)) {
        $html .= '<div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <span>' . htmlspecialchars($location) . '</span>
                </div>';
    }
    
    if (!empty($website)) {
        $html .= '<div class="contact-item">
                    <span class="contact-icon">🌐</span>
                    <a href="' . htmlspecialchars($website) . '" target="_blank" style="color: inherit; text-decoration: none;">' . htmlspecialchars($website) . '</a>
                </div>';
    }
    
    $html .= '</div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">';
    
    // Skills Section
    if (!empty($skills)) {
        $html .= '<section class="section">
                <h2 class="section-title">Skills</h2>
                <div class="skills-container">';
        
        foreach ($skills as $skill) {
            $skillName = htmlspecialchars($skill['name']);
            $skillLevel = isset($skill['level']) ? htmlspecialchars($skill['level']) : '';
            
            $html .= '<div class="skill-item">' . $skillName . ($skillLevel ? ' - ' . $skillLevel : '') . '</div>';
        }
        
        $html .= '</div>
            </section>';
    }
    
    // Experience Section
    if (!empty($experience)) {
        $html .= '<section class="section">
                <h2 class="section-title">Experience</h2>';
        
        foreach ($experience as $exp) {
            $html .= '<div class="timeline-item">
                    <div class="timeline-header">
                        <h3 class="timeline-title">' . htmlspecialchars($exp['title']) . '</h3>
                        <h4 class="timeline-subtitle">' . htmlspecialchars($exp['company']) . '</h4>
                        <div class="timeline-date">';
            
            if (!empty($exp['startDate'])) {
                $html .= htmlspecialchars($exp['startDate']);
            }
            
            if (!empty($exp['startDate']) && !empty($exp['endDate'])) {
                $html .= ' - ';
            }
            
            if (!empty($exp['endDate'])) {
                $html .= htmlspecialchars($exp['endDate']);
            }
            
            $html .= '</div>
                    </div>';
            
            if (!empty($exp['description'])) {
                $html .= '<p>' . nl2br(htmlspecialchars($exp['description'])) . '</p>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</section>';
    }
    
    // Education Section
    if (!empty($education)) {
        $html .= '<section class="section">
                <h2 class="section-title">Education</h2>';
        
        foreach ($education as $edu) {
            $html .= '<div class="timeline-item">
                    <div class="timeline-header">
                        <h3 class="timeline-title">' . htmlspecialchars($edu['degree']) . '</h3>
                        <h4 class="timeline-subtitle">' . htmlspecialchars($edu['school']) . '</h4>
                        <div class="timeline-date">';
            
            if (!empty($edu['startDate'])) {
                $html .= htmlspecialchars($edu['startDate']);
            }
            
            if (!empty($edu['startDate']) && !empty($edu['endDate'])) {
                $html .= ' - ';
            }
            
            if (!empty($edu['endDate'])) {
                $html .= htmlspecialchars($edu['endDate']);
            }
            
            $html .= '</div>
                    </div>';
            
            if (!empty($edu['description'])) {
                $html .= '<p>' . nl2br(htmlspecialchars($edu['description'])) . '</p>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</section>';
    }
    
    // Projects Section
    if (!empty($projects)) {
        $html .= '<section class="section">
                <h2 class="section-title">Projects</h2>
                <div class="projects-grid">';
        
        foreach ($projects as $project) {
            $html .= '<div class="project-card">
                    <div class="project-image">';
            
            if (!empty($project['image']) && file_exists($project['image'])) {
                debug_log("Adding project image to HTML: " . $project['image']);
                $html .= '<img src="' . htmlspecialchars($project['image']) . '" alt="' . htmlspecialchars($project['title']) . '">';
            } else {
                debug_log("Using placeholder for project image");
                $html .= 'Project Image';
            }
            
            $html .= '</div>
                    <div class="project-content">
                        <h3 class="project-title">' . htmlspecialchars($project['title']) . '</h3>';
            
            if (!empty($project['description'])) {
                $html .= '<p class="project-description">' . nl2br(htmlspecialchars($project['description'])) . '</p>';
            }
            
            if (!empty($project['technologies'])) {
                $technologies = explode(',', $project['technologies']);
                $html .= '<div class="project-tech">';
                
                foreach ($technologies as $tech) {
                    $tech = trim($tech);
                    if (!empty($tech)) {
                        $html .= '<span class="tech-tag">' . htmlspecialchars($tech) . '</span>';
                    }
                }
                
                $html .= '</div>';
            }
            
            if (!empty($project['url'])) {
                $html .= '<a href="' . htmlspecialchars($project['url']) . '" target="_blank" class="project-link">View Project</a>';
            }
            
            $html .= '</div>
                </div>';
        }
        
        $html .= '</div>
            </section>';
    }
    
    $html .= '</div>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="social-links">';
    
    if (!empty($linkedin)) {
        $html .= '<a href="' . htmlspecialchars($linkedin) . '" target="_blank" class="social-link">in</a>';
    }
    
    if (!empty($github)) {
        $html .= '<a href="' . htmlspecialchars($github) . '" target="_blank" class="social-link">gh</a>';
    }
    
    if (!empty($email)) {
        $html .= '<a href="mailto:' . htmlspecialchars($email) . '" class="social-link">@</a>';
    }
    
    $html .= '</div>
            <p class="copyright">© ' . date('Y') . ' ' . htmlspecialchars($fullName) . '. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>';
    
    return $html;
}

/**
 * Generate the Executive template
 */
function generateExecutiveTemplate($data) {
    // Extract data for easier access
    extract($data);
    
    // Generate the executive template HTML similar to the modern template
    // This would include executive-specific styling and structure
    $html = '
    <style>
        /* Executive template styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Source Sans Pro", sans-serif;
            color: #1a1a1a;
            line-height: 1.6;
            background-color: #f8fafc;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: "Playfair Display", serif;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        /* Header */
        .header {
            background-color: #1e293b;
            color: white;
            padding: 4rem 0;
        }
        
        .header-content {
            display: flex;
            align-items: center;
        }
        
        .profile-image-container {
            flex-shrink: 0;
            margin-right: 3rem;
        }
        
        .profile-image {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 3px solid #cbd5e1;
            object-fit: cover;
            background-color: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #1e293b;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .name {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #f8fafc;
        }
        
        .job-title {
            font-size: 1.25rem;
            font-style: italic;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }
        
        .bio {
            max-width: 700px;
            font-size: 1.1rem;
            line-height: 1.7;
            color: #e2e8f0;
        }
        
        .contact-items {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #cbd5e1;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
        }
        
        .contact-icon {
            margin-right: 0.5rem;
        }
        
        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            padding: 4rem 0;
        }
        
        .sidebar, .content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #cbd5e1;
        }
        
        /* Skills */
        .skills-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .skill-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .skill-name {
            font-weight: 600;
        }
        
        .skill-level {
            width: 65%;
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .skill-progress {
            height: 100%;
            background-color: #1e293b;
        }
        
        /* Experience & Education */
        .timeline-item {
            margin-bottom: 2rem;
        }
        
        .timeline-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
            color: #1e293b;
        }
        
        .timeline-subtitle {
            font-style: italic;
            color: #475569;
            margin-bottom: 0.25rem;
        }
        
        .timeline-date {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.75rem;
        }
        
        /* Projects */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .project-card {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .project-image {
            width: 100%;
            height: 150px;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
        }
        
        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .project-content {
            padding: 1.5rem;
        }
        
        .project-title {
            font-weight: 700;
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }
        
        .project-description {
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 1rem;
        }
        
        .project-link {
            display: inline-block;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            border-bottom: 1px solid #1e293b;
            transition: color 0.2s;
        }
        
        .project-link:hover {
            color: #475569;
        }
        
        /* Footer */
        .footer {
            background-color: #f1f5f9;
            padding: 3rem 0;
            text-align: center;
        }
        
        .copyright {
            font-size: 0.875rem;
            color: #64748b;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-image-container {
                margin-right: 0;
                margin-bottom: 2rem;
            }
            
            .contact-items {
                justify-content: center;
            }
            
            .main-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
            ';
    
    // Add profile image if available
    if (!empty($profileImage)) {
        $html .= '<div class="profile-image-container">
                    <img src="' . htmlspecialchars($profileImage) . '" alt="' . htmlspecialchars($fullName) . '" class="profile-image">
                </div>';
    } else {
        $initial = !empty($fullName) ? substr($fullName, 0, 1) : 'P';
        $html .= '<div class="profile-image-container">
                    <div class="profile-image">' . htmlspecialchars($initial) . '</div>
                </div>';
    }
    
    $html .= '<div class="profile-info">
                    <h1 class="name">' . htmlspecialchars($fullName) . '</h1>
                    <h2 class="job-title">' . htmlspecialchars($jobTitle) . '</h2>
                    <p class="bio">' . nl2br(htmlspecialchars($bio)) . '</p>
                    <div class="contact-items">';
    
    if (!empty($email)) {
        $html .= '<div class="contact-item">
                    <span class="contact-icon">📧</span>
                    <a href="mailto:' . htmlspecialchars($email) . '" style="color: inherit; text-decoration: none;">' . htmlspecialchars($email) . '</a>
                </div>';
    }
    
    if (!empty($phone)) {
        $html .= '<div class="contact-item">
                    <span class="contact-icon">📱</span>
                    <a href="tel:' . htmlspecialchars($phone) . '" style="color: inherit; text-decoration: none;">' . htmlspecialchars($phone) . '</a>
                </div>';
    }
    
    if (!empty($location)) {
        $html .= '<div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <span>' . htmlspecialchars($location) . '</span>
                </div>';
    }
    
    $html .= '</div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content container">
        <div class="sidebar">';
    
    // Skills Section (Sidebar)
    if (!empty($skills)) {
        $html .= '<section class="section">
                <h2 class="section-title">Skills</h2>
                <div class="skills-list">';
        
        foreach ($skills as $skill) {
            $skillName = htmlspecialchars($skill['name']);
            $skillLevel = isset($skill['level']) ? htmlspecialchars($skill['level']) : '';
            
            // Calculate progress width based on skill level
            $progressWidth = '75%';
            if ($skillLevel === 'Beginner') $progressWidth = '25%';
            if ($skillLevel === 'Intermediate') $progressWidth = '50%';
            if ($skillLevel === 'Advanced') $progressWidth = '75%';
            if ($skillLevel === 'Expert') $progressWidth = '95%';
            
            $html .= '<div class="skill-item">
                        <span class="skill-name">' . $skillName . '</span>
                        <div class="skill-level">
                            <div class="skill-progress" style="width: ' . $progressWidth . '"></div>
                        </div>
                    </div>';
        }
        
        $html .= '</div>
            </section>';
    }
    
    // Education Section (Sidebar)
    if (!empty($education)) {
        $html .= '<section class="section">
                <h2 class="section-title">Education</h2>';
        
        foreach ($education as $edu) {
            $html .= '<div class="timeline-item">
                    <h3 class="timeline-title">' . htmlspecialchars($edu['degree']) . '</h3>
                    <p class="timeline-subtitle">' . htmlspecialchars($edu['school']) . '</p>
                    <p class="timeline-date">';
            
            if (!empty($edu['startDate'])) {
                $html .= htmlspecialchars($edu['startDate']);
            }
            
            if (!empty($edu['startDate']) && !empty($edu['endDate'])) {
                $html .= ' - ';
            }
            
            if (!empty($edu['endDate'])) {
                $html .= htmlspecialchars($edu['endDate']);
            }
            
            $html .= '</p>';
            
            if (!empty($edu['description'])) {
                $html .= '<p class="timeline-description">' . nl2br(htmlspecialchars($edu['description'])) . '</p>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</section>';
    }
    
    $html .= '</div>
        <div class="content">';
    
    // Experience Section (Content)
    if (!empty($experience)) {
        $html .= '<section class="section">
                <h2 class="section-title">Professional Experience</h2>';
        
        foreach ($experience as $exp) {
            $html .= '<div class="timeline-item">
                    <h3 class="timeline-title">' . htmlspecialchars($exp['title']) . '</h3>
                    <p class="timeline-subtitle">' . htmlspecialchars($exp['company']) . '</p>
                    <p class="timeline-date">';
            
            if (!empty($exp['startDate'])) {
                $html .= htmlspecialchars($exp['startDate']);
            }
            
            if (!empty($exp['startDate']) && !empty($exp['endDate'])) {
                $html .= ' - ';
            }
            
            if (!empty($exp['endDate'])) {
                $html .= htmlspecialchars($exp['endDate']);
            }
            
            $html .= '</p>';
            
            if (!empty($exp['description'])) {
                $html .= '<p class="timeline-description">' . nl2br(htmlspecialchars($exp['description'])) . '</p>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</section>';
    }
    
    // Projects Section (Content)
    if (!empty($projects)) {
        $html .= '<section class="section">
                <h2 class="section-title">Key Projects</h2>
                <div class="projects-grid">';
        
        foreach ($projects as $project) {
            $html .= '<div class="project-card">
                    <div class="project-image">';
            
            if (!empty($project['image'])) {
                $html .= '<img src="' . htmlspecialchars($project['image']) . '" alt="' . htmlspecialchars($project['title']) . '">';
            } else {
                $html .= 'Project Image';
            }
            
            $html .= '</div>
                    <div class="project-content">
                        <h3 class="project-title">' . htmlspecialchars($project['title']) . '</h3>';
            
            if (!empty($project['description'])) {
                $html .= '<p class="project-description">' . nl2br(htmlspecialchars($project['description'])) . '</p>';
            }
            
            if (!empty($project['url'])) {
                $html .= '<a href="' . htmlspecialchars($project['url']) . '" target="_blank" class="project-link">View Details</a>';
            }
            
            $html .= '</div>
                </div>';
        }
        
        $html .= '</div>
            </section>';
    }
    
    $html .= '</div>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="copyright">© ' . date('Y') . ' ' . htmlspecialchars($fullName) . '. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>';
    
    return $html;
}

/**
 * Generate the Creative Tech template
 */
function generateCreativeTechTemplate($data) {
    // Extract data for easier access
    extract($data);
    
    // Generate the creative tech template HTML similar to the modern template
    // This would include creative-tech-specific styling and structure
    $html = '
    <style>
        /* Creative Tech template styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Poppins", sans-serif;
            color: #f8fafc;
            background-color: #0f172a;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        /* Header */
        .header {
            position: relative;
            padding: 8rem 0 4rem;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'%3E%3Cg fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.1\'%3E%3Cpath opacity=\'.5\' d=\'M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
            z-index: 0;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .profile-image-container {
            margin-bottom: 2.5rem;
        }
        
        .profile-image {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            background-color: rgba(255, 255, 255, 0.1);
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .name {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #f9fafb, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            letter-spacing: -1px;
        }
        
        .job-title {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.8);
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .bio {
            max-width: 700px;
            font-size: 1.125rem;
            line-height: 1.8;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .social-links {
            display: flex;
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .social-link {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 1.25rem;
            transition: all 0.3s;
        }
        
        .social-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }
        
        /* Main Content */
        .main-content {
            padding: 6rem 0;
        }
        
        .section {
            margin-bottom: 8rem;
            position: relative;
        }
        
        .section:last-child {
            margin-bottom: 0;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        
        .section-subtitle {
            font-size: 1.125rem;
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Skills */
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 2rem;
        }
        
        .skill-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        
        .skill-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .skill-icon {
            width: 3rem;
            height: 3rem;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .skill-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .skill-level {
            font-size: 0.875rem;
            color: #94a3b8;
        }
        
        /* Experience */
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .timeline::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 2px;
            background: linear-gradient(to bottom, #7c3aed, #ec4899);
            transform: translateX(-50%);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 4rem;
            width: 100%;
        }
        
        .timeline-item:nth-child(odd) {
            padding-right: calc(50% + 2rem);
        }
        
        .timeline-item:nth-child(even) {
            padding-left: calc(50% + 2rem);
        }
        
        .timeline-item::before {
            content: "";
            position: absolute;
            top: 0;
            width: 1.5rem;
            height: 1.5rem;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            border-radius: 50%;
        }
        
        .timeline-item:nth-child(odd)::before {
            right: calc(50% - 0.75rem);
        }
        
        .timeline-item:nth-child(even)::before {
            left: calc(50% - 0.75rem);
        }
        
        .timeline-content {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s;
        }
        
        .timeline-content:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
        }
        
        .timeline-date {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .timeline-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .timeline-subtitle {
            color: #94a3b8;
            margin-bottom: 1rem;
        }
        
        .timeline-description {
            color: #cbd5e1;
            line-height: 1.6;
        }
        
        /* Projects */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .project-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .project-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .project-image {
            width: 100%;
            height: 200px;
            background-color: #1e293b;
            position: relative;
            overflow: hidden;
        }
        
        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .project-card:hover .project-image img {
            transform: scale(1.1);
        }
        
        .project-content {
            padding: 2rem;
        }
        
        .project-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .project-description {
            color: #cbd5e1;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .project-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .project-tag {
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
        }
        
        .project-link {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 2rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .project-link.primary {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: white;
        }
        
        .project-link.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }
        
        .project-link:hover {
            transform: translateY(-3px);
        }
        
        /* Footer */
        .footer {
            background: #0a0f1a;
            padding: 4rem 0;
            text-align: center;
        }
        
        .footer-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .footer-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        
        .footer-text {
            color: #94a3b8;
            margin-bottom: 2rem;
        }
        
        .footer-button {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .copyright {
            margin-top: 3rem;
            color: #64748b;
            font-size: 0.875rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .name {
                font-size: 3rem;
            }
            
            .timeline::before {
                left: 0;
            }
            
            .timeline-item:nth-child(odd),
            .timeline-item:nth-child(even) {
                padding-left: 2rem;
                padding-right: 0;
            }
            
            .timeline-item:nth-child(odd)::before,
            .timeline-item:nth-child(even)::before {
                left: -0.75rem;
                right: auto;
            }
            
            .projects-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
            ';
    
    // Add profile image if available
    if (!empty($profileImage)) {
        $html .= '<div class="profile-image-container">
                    <img src="' . htmlspecialchars($profileImage) . '" alt="' . htmlspecialchars($fullName) . '" class="profile-image">
                </div>';
    } else {
        $initial = !empty($fullName) ? substr($fullName, 0, 1) : 'P';
        $html .= '<div class="profile-image-container">
                    <div class="profile-image">' . htmlspecialchars($initial) . '</div>
                </div>';
    }
    
    $html .= '<h1 class="name">' . htmlspecialchars($fullName) . '</h1>
                <h2 class="job-title">' . htmlspecialchars($jobTitle) . '</h2>
                <p class="bio">' . nl2br(htmlspecialchars($bio)) . '</p>';
    
    // Add social links if available
    if (!empty($linkedin) || !empty($github) || !empty($email)) {
        $html .= '<div class="social-links">';
        
        if (!empty($linkedin)) {
            $html .= '<a href="' . htmlspecialchars($linkedin) . '" target="_blank" class="social-link">in</a>';
        }
        
        if (!empty($github)) {
            $html .= '<a href="' . htmlspecialchars($github) . '" target="_blank" class="social-link">gh</a>';
        }
        
        if (!empty($email)) {
            $html .= '<a href="mailto:' . htmlspecialchars($email) . '" class="social-link">@</a>';
        }
        
        $html .= '</div>';
    }
    
    $html .= '</div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">';
    
    // Skills Section
    if (!empty($skills)) {
        $html .= '<section class="section">
                <div class="section-header">
                    <h2 class="section-title">Skills & Expertise</h2>
                    <p class="section-subtitle">Technologies and tools I specialize in</p>
                </div>
                <div class="skills-grid">';
        
        foreach ($skills as $skill) {
            $skillName = htmlspecialchars($skill['name']);
            $skillLevel = isset($skill['level']) ? htmlspecialchars($skill['level']) : '';
            
            $html .= '<div class="skill-card">
                        <div class="skill-icon">' . substr($skillName, 0, 1) . '</div>
                        <h3 class="skill-name">' . $skillName . '</h3>';
            
            if (!empty($skillLevel)) {
                $html .= '<p class="skill-level">' . $skillLevel . '</p>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>
            </section>';
    }
    
    // Experience Section
    if (!empty($experience)) {
        $html .= '<section class="section">
                <div class="section-header">
                    <h2 class="section-title">Work Experience</h2>
                    <p class="section-subtitle">My professional journey</p>
                </div>
                <div class="timeline">';
        
        foreach ($experience as $index => $exp) {
            $html .= '<div class="timeline-item">
                    <div class="timeline-content">
                        <span class="timeline-date">';
            
            if (!empty($exp['startDate'])) {
                $html .= htmlspecialchars($exp['startDate']);
            }
            
            if (!empty($exp['startDate']) && !empty($exp['endDate'])) {
                $html .= ' - ';
            }
            
            if (!empty($exp['endDate'])) {
                $html .= htmlspecialchars($exp['endDate']);
            }
            
            $html .= '</span>
                        <h3 class="timeline-title">' . htmlspecialchars($exp['title']) . '</h3>
                        <h4 class="timeline-subtitle">' . htmlspecialchars($exp['company']) . '</h4>';
            
            if (!empty($exp['description'])) {
                $html .= '<p class="timeline-description">' . nl2br(htmlspecialchars($exp['description'])) . '</p>';
            }
            
            $html .= '</div>
                </div>';
        }
        
        $html .= '</div>
            </section>';
    }
    
    // Education Section
    if (!empty($education)) {
        $html .= '<section class="section">
                <div class="section-header">
                    <h2 class="section-title">Education</h2>
                    <p class="section-subtitle">Academic background and qualifications</p>
                </div>
                <div class="timeline">';
        
        foreach ($education as $index => $edu) {
            $html .= '<div class="timeline-item">
                    <div class="timeline-content">
                        <span class="timeline-date">';
            
            if (!empty($edu['startDate'])) {
                $html .= htmlspecialchars($edu['startDate']);
            }
            
            if (!empty($edu['startDate']) && !empty($edu['endDate'])) {
                $html .= ' - ';
            }
            
            if (!empty($edu['endDate'])) {
                $html .= htmlspecialchars($edu['endDate']);
            }
            
            $html .= '</span>
                        <h3 class="timeline-title">' . htmlspecialchars($edu['degree']) . '</h3>
                        <h4 class="timeline-subtitle">' . htmlspecialchars($edu['school']) . '</h4>';
            
            if (!empty($edu['description'])) {
                $html .= '<p class="timeline-description">' . nl2br(htmlspecialchars($edu['description'])) . '</p>';
            }
            
            $html .= '</div>
                </div>';
        }
        
        $html .= '</div>
            </section>';
    }
    
    // Projects Section
    if (!empty($projects)) {
        $html .= '<section class="section">
                <div class="section-header">
                    <h2 class="section-title">Featured Projects</h2>
                    <p class="section-subtitle">Showcasing my best work</p>
                </div>
                <div class="projects-grid">';
        
        foreach ($projects as $project) {
            $html .= '<div class="project-card">
                    <div class="project-image">';
            
            if (!empty($project['image'])) {
                $html .= '<img src="' . htmlspecialchars($project['image']) . '" alt="' . htmlspecialchars($project['title']) . '">';
            } else {
                $html .= '<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;">Project Image</div>';
            }
            
            $html .= '</div>
                    <div class="project-content">
                        <h3 class="project-title">' . htmlspecialchars($project['title']) . '</h3>';
            
            if (!empty($project['description'])) {
                $html .= '<p class="project-description">' . nl2br(htmlspecialchars($project['description'])) . '</p>';
            }
            
            if (!empty($project['technologies'])) {
                $technologies = explode(',', $project['technologies']);
                $html .= '<div class="project-tags">';
                
                foreach ($technologies as $tech) {
                    $tech = trim($tech);
                    if (!empty($tech)) {
                        $html .= '<span class="project-tag">' . htmlspecialchars($tech) . '</span>';
                    }
                }
                
                $html .= '</div>';
            }
            
            if (!empty($project['url'])) {
                $html .= '<a href="' . htmlspecialchars($project['url']) . '" target="_blank" class="project-link primary">View Project</a>';
            }
            
            $html .= '</div>
                </div>';
        }
        
        $html .= '</div>
            </section>';
    }
    
    $html .= '</div>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <h2 class="footer-title">Get In Touch</h2>
                <p class="footer-text">Interested in working together? Feel free to reach out.</p>';
    
    if (!empty($email)) {
        $html .= '<a href="mailto:' . htmlspecialchars($email) . '" class="footer-button">Contact Me</a>';
    }
    
    $html .= '<p class="copyright">© ' . date('Y') . ' ' . htmlspecialchars($fullName) . '. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>';
    
    return $html;
}
?>