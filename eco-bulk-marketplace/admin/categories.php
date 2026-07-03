<?php
include "../includes/config.php";
include "../includes/functions.php";
requireAdmin();

$message = "";

if (isset($_POST["add_category"])) {
    $category_name = sanitize($_POST["category_name"]);
    $description = sanitize($_POST["description"]);

    $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $category_name, $description);

    if ($stmt->execute()) {
        $message = "Category added successfully.";
    } else {
        $message = "Category could not be added.";
    }
}

if (isset($_POST["update_category"])) {
    $category_id = $_POST["category_id"];
    $category_name = sanitize($_POST["category_name"]);
    $description = sanitize($_POST["description"]);

    $stmt = $conn->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
    $stmt->bind_param("ssi", $category_name, $description, $category_id);

    if ($stmt->execute()) {
        $message = "Category updated successfully.";
    } else {
        $message = "Category could not be updated.";
    }
}

if (isset($_GET["delete"])) {
    $category_id = $_GET["delete"];

    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        $message = "Category deleted successfully.";
    } else {
        $message = "Category cannot be deleted because products may depend on it.";
    }
}

$editCategory = null;

if (isset($_GET["edit"])) {
    $category_id = $_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $editCategory = $stmt->get_result()->fetch_assoc();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY category_id DESC");
?>

<?php include "../includes/header.php"; ?>

<div class="dash-header">
    <div>
        <p class="dash-kicker">Admin Panel</p>
        <h2 class="mb-0">Manage Categories</h2>
    </div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<?php if ($message != "") { ?>
    <div class="alert alert-info mt-3"><?php echo $message; ?></div>
<?php } ?>

<div class="row g-4 mt-1">

    <div class="col-lg-4">
        <div class="card p-4">
            <h4><?php echo $editCategory ? "Update Category" : "Add New Category"; ?></h4>

            <form method="POST">
                <?php if ($editCategory) { ?>
                    <input type="hidden" name="category_id" value="<?php echo $editCategory["category_id"]; ?>">
                <?php } ?>

                <div class="mb-3">
                    <label>Category Name</label>
                    <input type="text" name="category_name" class="form-control"
                           value="<?php echo $editCategory ? $editCategory["category_name"] : ""; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"><?php echo $editCategory ? $editCategory["description"] : ""; ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <?php if ($editCategory) { ?>
                        <button type="submit" name="update_category" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Update
                        </button>
                        <a href="categories.php" class="btn btn-secondary">Cancel</a>
                    <?php } else { ?>
                        <button type="submit" name="add_category" class="btn btn-success">
                            <i class="bi bi-plus-lg me-1"></i> Add Category
                        </button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <h3 class="dash-section-title" style="margin-top:0;">All Categories</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $categories->fetch_assoc()) { ?>
                        <tr>
                            <td><span style="color:var(--slate-dim);font-size:0.8rem;">#<?php echo $row["category_id"]; ?></span></td>
                            <td style="font-weight:600;color:var(--ink);"><?php echo $row["category_name"]; ?></td>
                            <td><?php echo $row["description"]; ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="categories.php?edit=<?php echo $row["category_id"]; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="categories.php?delete=<?php echo $row["category_id"]; ?>"
                                       onclick="return confirmDelete();" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>