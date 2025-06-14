<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "gshare";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,3307);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle music deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['file_path'])) {
    $filePath = $_POST['file_path'];
    $response = [];

    // Delete file from the folder
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            // Now delete the record from the database
            $stmt = $conn->prepare("DELETE FROM files WHERE file_path = ?");
            $stmt->bind_param("s", $filePath);

            if ($stmt->execute()) {
                $response['status'] = 'success';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to delete record from database.';
            }

            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to delete file from folder.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'File does not exist.';
    }

    echo json_encode($response);
    $conn->close();
    exit();
}

// Fetch only music (audio) files from the database
$sql = "SELECT file_name, file_path FROM files WHERE file_type = 'audio'";
$result = $conn->query($sql);

$audios = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $audios[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Music</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/css/lightgallery.min.css">
    <link rel="stylesheet" href="access.css">
</head>
<body>
<button type="button" id="buttonid">Go To Dashboard</button>
<div id="gallery">
    <?php foreach ($audios as $audio): ?>
        <div class="gallery-item">
            <a href="<?php echo htmlspecialchars($audio['file_path']); ?>" data-lg-size="1920-1080">
                <audio controls oncontextmenu="return confirmDelete('<?php echo htmlspecialchars($audio['file_name']); ?>', '<?php echo htmlspecialchars($audio['file_path']); ?>');">
                    <source src="<?php echo htmlspecialchars($audio['file_path']); ?>" type="audio/mpeg">
                    Your browser does not support the audio tag.
                </audio>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/lightgallery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/zoom/lg-zoom.min.js"></script>

<script>
$(document).ready(function() {
    $('#gallery').lightGallery({
        selector: '.gallery-item a',
        mode: 'lg-fade',
        download: false, 
        counter: true, 
        zoom: true, 
        enableSwipe: true,
        autoplay: true, 
        fullScreen: true,
    });
});

function confirmDelete(fileName, filePath) {
    if (confirm("Are you sure you want to delete '" + fileName + "'?")) {
        // User confirmed deletion
        deleteAudio(filePath);
    }
    return false; // Prevent the browser context menu from showing up
}

function deleteAudio(filePath) {
    $.ajax({
        type: 'POST',
        url: 'accessphoto.php', // Current PHP file itself
        data: { action: 'delete', file_path: filePath },
        success: function(response) {
            let jsonResponse = JSON.parse(response);
            if (jsonResponse.status === 'success') {
                // Remove the deleted audio from the DOM
                $('audio source[src="' + filePath + '"]').closest('.gallery-item').remove();
                alert('Audio deleted successfully.');
            } else {
                alert('Failed to delete audio: ' + jsonResponse.message);
            }
        },
        error: function() {
            alert('Error while deleting audio. Please try again.');
        }
    });
}

document.querySelector("#buttonid").addEventListener("click",()=>{
    window.location.href="dashboard.php";
});
</script>

</body>
</html>
