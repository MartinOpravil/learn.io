<?php
$pridano = "";
//kdyz se zmackne tlacitko upload
if (isset($_POST["upload"])) {

    //cesta k ulozeni nahraneho obrazku
    $target = "img/" . basename($_FILES["image"]["name"]);

    //pripojeni k db
    include "mysqli_connect.php";

    //kontrola pripojeni
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    //sber dat z formulare
    $title = $_POST["title"];
    $lessons_number = $_POST["lessons_number"];
    $duration = $_POST["duration"];
    $price = $_POST["price"];
    $level = $_POST["level"];
    $description = $_POST["description"];
    $image = $_FILES["image"]["name"];

    //dotaz
    $sql = "INSERT INTO courses (title, lessons_number, duration, price, level, description, image) VALUES ('$title', '$lessons_number', '$duration', '$price', '$level','$description','$image')";

    //ulozi data do tabulky a provede kontrolu
    if (mysqli_query($conn, $sql)) :

        //success¨
        $pridano = "<span style='color:#fff'>New course was added.</span>";
    else :
        echo "query error:" . mysqli_error($conn);
    endif;

    //presunuti uploadnuteho obrazku do slozky img
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
        $msg = "Image uploaded successfully";
    } else {
        $msg = "There was a problem with upload image";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<?php
include("header.php");
?>



<body id="page-top">
    <div class="top"></div>

    <?php
    include("menu.php");
    ?>

    <div class="container-fluid add text-white">
        <div class="row no-gutter" style="margin: 0 !important;">
            <div class="d-none d-md-flex col-xl-6 bg-image"></div>
            <div class="col-xl-6">
                <div class="add-new d-flex align-items-center py-5">
                    <div class="container">
                        <div class="row">
                            <div class="mx-auto">
                                <h1 class="mb-4">Add a new course</h1>
                                <form action="" method="post" enctype="multipart/form-data">

                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control " name="title" placeholder="Enter title" required>

                                    </div>


                                    <div class="form-together" style="display: flex; justify-content:space-between;">
                                        <div class="form-group">
                                            <label for="lessons">Number of lessons</label>
                                            <input type="number" class="form-control " name="lessons_number" placeholder="Enter number of lessons" required>

                                        </div>


                                        <div class="form-group">
                                            <label for="time">Duration</label>
                                            <input type="number" class="form-control " name="duration" placeholder="Enter duration" required>

                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="price">Price</label>
                                        <select name="price" id="price">
                                            <option value="Free">Free</option>
                                            <option value="Paid">Paid</option>


                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="level" style="display: block;">Level</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="level" id="inlineRadio1" value="Noob">
                                            <label class="form-check-label" for="inlineRadio1">Noob</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="level" id="inlineRadio2" value="Beginner">
                                            <label class="form-check-label" for="inlineRadio2">Beginner</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="level" id="inlineRadio3" value="Intermediate">
                                            <label class="form-check-label" for="inlineRadio3">Intermediate</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="level" id="inlineRadio4" value="Advanced">
                                            <label class="form-check-label" for="inlineRadio4">Advanced</label>
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label for="desc">Description</label>
                                        <textarea class="form-control" name="description" rows="3" required placeholder="Say something description this..."></textarea>
                                    </div>

                                    <div class="form-group files">
                                        <label>Upload Your File </label>
                                        <input type="file" class="form-control-file" name="image" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary submit" name="upload">Submit</button>
                                    <br>
                                    <?php echo $pridano; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>