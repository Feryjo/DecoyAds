<?php
// نام فایل ذخیره‌سازی داده‌ها
$filename = 'vid.txt';

// خواندن داده‌ها از فایل
function loadVideos($filename) {
    if (file_exists($filename)) {
        $data = file_get_contents($filename);
        return json_decode($data, true) ?: [];
    }
    return [];
}

// ذخیره داده‌ها در فایل
function saveVideos($filename, $videos) {
    $data = json_encode($videos, JSON_PRETTY_PRINT);
    file_put_contents($filename, $data);
}

// عملیات افزودن ویدئو
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_video'])) {
    $url = trim($_POST['url']);
    $title = trim($_POST['title']);
    if ($url && $title) {
        $videos = loadVideos($filename);
        $newVideo = [
            'id' => uniqid(),
            'url' => $url,
            'title' => $title,
            'dateAdded' => date('c') // تاریخ به فرمت ISO 8601
        ];
        $videos[] = $newVideo;
        saveVideos($filename, $videos);
        echo "<p style='color:green;'>Video added successfully!</p>";
    } else {
        echo "<p style='color:red;'>Please fill in all fields.</p>";
    }
}

// عملیات حذف ویدئو
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_video'])) {
    $videoId = $_POST['video_id'];
    if ($videoId) {
        $videos = loadVideos($filename);
        $videos = array_filter($videos, function ($video) use ($videoId) {
            return $video['id'] !== $videoId;
        });
        saveVideos($filename, $videos);
        echo "<p style='color:green;'>Video deleted successfully!</p>";
    } else {
        echo "<p style='color:red;'>Invalid video ID.</p>";
    }
}

// بارگذاری داده‌ها برای نمایش
$videos = loadVideos($filename);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .video-list {
            margin-top: 20px;
        }
        .video-item {
            background-color: #fff;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Video Manager</h1>

    <!-- فرم افزودن ویدئو -->
    <form method="POST" action="">
        <h2>Add New Video</h2>
        <label for="url">Video URL:</label>
        <input type="text" id="url" name="url" required>
        <label for="title">Video Title:</label>
        <input type="text" id="title" name="title" required>
        <button type="submit" name="add_video">Add Video</button>
    </form>

    <!-- فرم حذف ویدئو -->
    <?php if (!empty($videos)): ?>
        <form method="POST" action="">
            <h2>Delete Video</h2>
            <label for="video_id">Select Video:</label>
            <select id="video_id" name="video_id" required>
                <?php foreach ($videos as $video): ?>
                    <option value="<?php echo htmlspecialchars($video['id']); ?>">
                        <?php echo htmlspecialchars($video['title']); ?> (<?php echo htmlspecialchars($video['url']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="delete_video">Delete Video</button>
        </form>
    <?php endif; ?>

    <!-- لیست ویدئوها -->
    <div class="video-list">
        <h2>Video List</h2>
        <?php if (empty($videos)): ?>
            <p>No videos found.</p>
        <?php else: ?>
            <?php foreach ($videos as $video): ?>
                <div class="video-item">
                    <strong><?php echo htmlspecialchars($video['title']); ?></strong><br>
                    URL: <a href="<?php echo htmlspecialchars($video['url']); ?>" target="_blank"><?php echo htmlspecialchars($video['url']); ?></a><br>
                    Added on: <?php echo htmlspecialchars($video['dateAdded']); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
