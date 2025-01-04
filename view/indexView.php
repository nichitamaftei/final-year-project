<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css">
        <title>Index</title>
    </head>

    <body>

        <h1>Departments</h1>
    
        <?php foreach ($arrayOfDepartments as $index => $department): // loops through each department in the data and displays it?> 
            <form method="POST" action="index.php">
                <button type="submit" name="dept" value="<?= $index; ?>">
                    <?= $department['name']; ?>
                </button>
            </form>
        <?php endforeach; ?>

        <p> Department =  <?= $departmentName // displays the current department for debugging ?> </p>

    </body>

</html>