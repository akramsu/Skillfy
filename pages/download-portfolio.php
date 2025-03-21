<?php
// Set error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if a file parameter is provided
if (isset($_GET['file'])) {
    $filename = basename($_GET['file']); // Sanitize the filename
    $filepath = 'downloads/' . $filename;
    
    // Check if the file exists
    if (file_exists($filepath)) {
        // Create a temporary directory for the ZIP file
        $tempDir = 'temp/' . uniqid();
        if (!file_exists('temp/')) {
            mkdir('temp/', 0755, true);
        }
        mkdir($tempDir, 0755, true);
        
        // Copy the portfolio HTML file to the temp directory
        copy($filepath, $tempDir . '/index.html');
        
        // Create a README file with instructions
        $readme = "# Portfolio Website

";
        $readme .= "This is a portfolio website generated by Skillfy Portfolio Generator.

";
        $readme .= "## How to Use

";
        $readme .= "1. Extract all files from this ZIP archive to a directory on your computer.
";
        $readme .= "2. Open the 'index.html' file in a web browser to view your portfolio locally.
";
        $readme .= "3. To publish your portfolio online, upload all files to a web hosting service.

";
        $readme .= "## Customization

";
        $readme .= "You can customize this portfolio by editing the HTML and CSS in the index.html file.
";
        $readme .= "If you need to make changes to your information, it's recommended to use the Skillfy Portfolio Generator again.

";
        $readme .= "## Credits

";
        $readme .= "Created with Skillfy Portfolio Generator
";
        
        file_put_contents($tempDir . '/README.md', $readme);
        
        // Create a directory for assets
        mkdir($tempDir . '/assets', 0755, true);
        
        // Copy any images used in the portfolio
        $html = file_get_contents($filepath);
        
        // Update the HTML to include proper viewport meta tag for responsiveness
        if (strpos($html, '<meta name="viewport"') === false) {
            $html = str_replace('<meta charset="UTF-8">', 
                                '<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">', 
                                $html);
        }
        
        // Save the updated HTML file
        file_put_contents($tempDir . '/index.html', $html);
        
        // Extract image paths from HTML
        preg_match_all('/src="([^"]+)"/', $html, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $imagePath) {
                // Skip data URLs and external URLs
                if (strpos($imagePath, 'data:') === 0 || strpos($imagePath, 'http') === 0) {
                    continue;
                }
                
                // Create the directory structure if needed
                $imageDir = $tempDir . '/assets';
                if (!file_exists($imageDir)) {
                    mkdir($imageDir, 0755, true);
                }
                
                // Copy the image file if it exists
                if (file_exists($imagePath)) {
                    copy($imagePath, $imageDir . '/' . basename($imagePath));
                    
                    // Update the image path in the HTML
                    $html = str_replace('src="' . $imagePath . '"', 'src="assets/' . basename($imagePath) . '"', $html);
                }
            }
            
            // Save the updated HTML file again with fixed image paths
            file_put_contents($tempDir . '/index.html', $html);
        }
        
        // Create a ZIP file
        $zipFilename = 'portfolio_' . date('Ymd_His') . '.zip';
        $zipFilepath = 'downloads/' . $zipFilename;
        
        // Try to use ZipArchive if available
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($zipFilepath, ZipArchive::CREATE) === TRUE) {
                // Add files to the ZIP
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($tempDir),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($tempDir) + 1);
                        
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                
                $zip->close();
                
                // Clean up the temporary directory
                deleteDirectory($tempDir);
                
                // Set headers for download
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
                header('Content-Length: ' . filesize($zipFilepath));
                header('Pragma: no-cache');
                header('Expires: 0');
                
                // Output the file
                readfile($zipFilepath);
                
                // Delete the ZIP file after download
                unlink($zipFilepath);
                exit;
            } else {
                // Fall back to alternative method if ZipArchive fails to open
                createZipAlternative($tempDir, $zipFilepath);
            }
        } else {
            // Use alternative method if ZipArchive is not available
            createZipAlternative($tempDir, $zipFilepath);
        }
    } else {
        echo 'Portfolio file not found. Please generate your portfolio again.';
    }
} else {
    // Process the form submission for direct download
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Include the generate-portfolio.php file to use its functions
        require_once 'generate-portfolio.php';
        
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
                    if (isset($_FILES['projects']) && isset($_FILES['projects']['name'][$index]['image']) && $_FILES['projects']['error'][$index]['image'] === UPLOAD_ERR_OK) {
                        $projectImageTmp = $_FILES['projects']['tmp_name'][$index]['image'];
                        $projectImageName = basename($_FILES['projects']['name'][$index]['image']);
                        $projectImageName = uniqid() . '_' . $projectImageName;
                        $projectImagePath = 'uploads/projects/' . $projectImageName;
                        
                        if (move_uploaded_file($projectImageTmp, $projectImagePath)) {
                            $projectImage = $projectImagePath;
                        }
                    }
                    
                    $proj['image'] = $projectImage;
                    $projects[] = $proj;
                }
            }
        }
        
        // Handle profile image upload
        $profileImage = '';
        if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
            $profileImageTmp = $_FILES['profileImage']['tmp_name'];
            $profileImageName = basename($_FILES['profileImage']['name']);
            $profileImageName = uniqid() . '_' . $profileImageName;
            $profileImagePath = 'uploads/profile/' . $profileImageName;
            
            if (move_uploaded_file($profileImageTmp, $profileImagePath)) {
                $profileImage = $profileImagePath;
            }
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
            mkdir('downloads/', 0755, true);
        }
        
        // Save the portfolio HTML to a file
        file_put_contents($portfolioPath, $portfolioHtml);
        
        // Redirect to download the file
        header('Location: download-portfolio.php?file=' . $portfolioFilename);
        exit;
    } else {
        echo 'Invalid request. Please use the portfolio generator form.';
    }
}

/**
 * Alternative method to create a ZIP file without using ZipArchive
 */
function createZipAlternative($sourceDir, $zipFilepath) {
    // Create a temporary file for the zip command output
    $tempOutputFile = tempnam(sys_get_temp_dir(), 'zip_output');
    
    // Try to use the zip command if available
    if (function_exists('exec')) {
        // Change to the source directory
        $currentDir = getcwd();
        chdir($sourceDir);
        
        // Use the zip command to create the ZIP file
        $command = "zip -r " . escapeshellarg($currentDir . '/' . $zipFilepath) . " .";
        exec($command . " 2>" . escapeshellarg($tempOutputFile), $output, $returnCode);
        
        // Change back to the original directory
        chdir($currentDir);
        
        if ($returnCode === 0) {
            // Clean up the temporary directory
            deleteDirectory($sourceDir);
            
            // Set headers for download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFilepath) . '"');
            header('Content-Length: ' . filesize($zipFilepath));
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Output the file
            readfile($zipFilepath);
            
            // Delete the ZIP file after download
            unlink($zipFilepath);
            unlink($tempOutputFile);
            exit;
        }
    }
    
    // If we get here, both ZipArchive and the zip command failed
    // Create a simple tar file as a last resort
    if (function_exists('exec')) {
        // Change to the source directory
        $currentDir = getcwd();
        chdir(dirname($sourceDir));
        
        // Use the tar command to create a tar file
        $tarFilepath = str_replace('.zip', '.tar', $zipFilepath);
        $command = "tar -cf " . escapeshellarg($currentDir . '/' . $tarFilepath) . " " . escapeshellarg(basename($sourceDir));
        exec($command . " 2>" . escapeshellarg($tempOutputFile), $output, $returnCode);
        
        // Change back to the original directory
        chdir($currentDir);
        
        if ($returnCode === 0) {
            // Clean up the temporary directory
            deleteDirectory($sourceDir);
            
            // Set headers for download
            header('Content-Type: application/x-tar');
            header('Content-Disposition: attachment; filename="portfolio_' . date('Ymd_His') . '.tar"');
            header('Content-Length: ' . filesize($tarFilepath));
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Output the file
            readfile($tarFilepath);
            
            // Delete the tar file after download
            unlink($tarFilepath);
            unlink($tempOutputFile);
            exit;
        }
    }
    
    // If all methods fail, provide a direct download of the HTML file
    $htmlFilePath = $sourceDir . '/index.html';
    if (file_exists($htmlFilePath)) {
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="portfolio.html"');
        header('Content-Length: ' . filesize($htmlFilePath));
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output the file
        readfile($htmlFilePath);
        
        // Clean up
        deleteDirectory($sourceDir);
        unlink($tempOutputFile);
        exit;
    }
    
    // If everything fails, show an error message
    echo 'Unable to create a downloadable file. Please try again or contact the administrator.';
    unlink($tempOutputFile);
}

/**
 * Recursively delete a directory and its contents
 */
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    
    return rmdir($dir);
}
?>