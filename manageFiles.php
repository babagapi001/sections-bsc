<?php
// Set the directory where files are stored
$directory = "txt_files";

// Ensure the directory exists
if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}

// Helper function to send JSON response
function sendJsonResponse($success, $data = []) {
    echo json_encode(['success' => $success, 'data' => $data]);
    exit;
}

// List all .txt files in the directory (optional search)
if ($_GET['action'] == 'list') {
    $search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
    $files = array_filter(scandir($directory), function($file) use ($search) {
        return strpos($file, '.txt') !== false && (!$search || strpos(strtolower($file), $search) !== false);
    });
    sendJsonResponse(true, array_values($files)); // Return file list
}

// Add a new .txt file
if ($_GET['action'] == 'add') {
    $fileName = basename($_GET['filename']);
    if (strpos($fileName, '.txt') === false) {
        sendJsonResponse(false); // Ensure the file ends with .txt
    }
    $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
    if (file_put_contents($filePath, "") !== false) {
        sendJsonResponse(true);
    } else {
        sendJsonResponse(false);
    }
}

// Edit a file name
if ($_GET['action'] == 'edit') {
    $oldName = basename($_GET['old']);
    $newName = basename($_GET['new']);
    if (strpos($newName, '.txt') === false) {
        sendJsonResponse(false); // Ensure the new name ends with .txt
    }
    $oldFilePath = $directory . DIRECTORY_SEPARATOR . $oldName;
    $newFilePath = $directory . DIRECTORY_SEPARATOR . $newName;
    if (rename($oldFilePath, $newFilePath)) {
        sendJsonResponse(true);
    } else {
        sendJsonResponse(false);
    }
}

// Delete a file
if ($_GET['action'] == 'delete') {
    $fileName = basename($_GET['filename']);
    $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($filePath) && unlink($filePath)) {
        sendJsonResponse(true);
    } else {
        sendJsonResponse(false);
    }
}
?>
