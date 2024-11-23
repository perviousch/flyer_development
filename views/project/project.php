<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    header("Location: ../../login.php");
    exit;
}

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    header("Location: management_dashboard.php");
    exit;
}

// Fetch project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: management_dashboard.php");
    exit;
}

// Handle CSV upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if (($handle = fopen($_FILES['csv_file']['tmp_name'], "r")) !== FALSE) {
        $stmt = $pdo->prepare("INSERT INTO product_data (project_id, product_code, product_name, category_name, catalogue_name, bulk_price, current_sp, promo_sp, page_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Skip the header row
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $stmt->execute([
                $project_id,
                $data[0], // product_code
                $data[1], // product_name
                $data[2], // category_name
                $data[3], // catalogue_name
                $data[4], // bulk_price (now VARCHAR)
                $data[5], // current_sp
                $data[6], // promo_sp
                $data[7]  // page_number (now VARCHAR)
            ]);
        }
        fclose($handle);
    }
}

// Fetch product data
$stmt = $pdo->prepare("SELECT * FROM product_data WHERE project_id = ? ORDER BY page_number, product_name");
$stmt->execute([$project_id]);
$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details - Flyer Development System</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Project Details: <?php echo htmlspecialchars($project['name']); ?></h1>
        <nav>
            <ul>
                <li><a href="management_dashboard.php">Back to Dashboard</a></li>
                <li><a href="../../logout.php">Logout</a></li>
            </ul>
        </nav>

        <section id="project-info">
            <h2>Project Information</h2>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($project['project_date']); ?></p>
            <p><strong>Last Update:</strong> <?php echo htmlspecialchars($project['updated_at']); ?></p>
            <p><strong>Comment:</strong> <?php echo htmlspecialchars($project['comment']); ?></p>
        </section>

        <section id="pdf-preview">
            <h2>PDF Preview</h2>
            <?php if ($project['pdf_file_path']): ?>
                <embed src="<?php echo htmlspecialchars($project['pdf_file_path']); ?>" type="application/pdf" width="100%" height="600px" />
                <p><a href="<?php echo htmlspecialchars($project['pdf_file_path']); ?>" download>Download PDF</a></p>
            <?php else: ?>
                <p>No PDF uploaded yet.</p>
            <?php endif; ?>
        </section>

        <section id="csv-upload">
            <h2>Upload Product Data</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">Upload CSV:</label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                </div>
                <button type="submit">Upload CSV</button>
            </form>
        </section>

        <section id="product-list">
            <h2>Product List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Category Name</th>
                        <th>Catalog Name</th>
                        <th>Bulk Price</th>
                        <th>Current Price</th>
                        <th>Promo Price</th>
                        <th>Page Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['product_code']); ?></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['catalogue_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['bulk_price']); ?></td>
                            <td><?php echo htmlspecialchars($product['current_sp']); ?></td>
                            <td><?php echo htmlspecialchars($product['promo_sp']); ?></td>
                            <td><?php echo htmlspecialchars($product['page_number']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>