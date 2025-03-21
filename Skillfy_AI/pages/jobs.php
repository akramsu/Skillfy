<?php
session_start();

// Execute the Python script
$command = "python ../src/main.py --default-index 7"; // 7 is the index for LinkedIn Jobs
$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin
   1 => array("pipe", "w"),  // stdout
   2 => array("pipe", "w")   // stderr
);

$process = proc_open($command, $descriptorspec, $pipes, realpath('./'));

if (is_resource($process)) {
    // Close pipes
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    // Close process
    proc_close($process);
}

// Redirect to Streamlit app
header("Location: http://localhost:8501");
exit();
?>
