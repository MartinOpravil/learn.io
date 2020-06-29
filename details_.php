<?php
include("header.php");
include("menu.php");

if (!isset($_GET["id"])) {
    ?>
    <p style="color:white">Nothing to see here :)</p>
    <?php
} else {
    include "mysqli_connect.php";

    //kontrola pripojeni
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    $id = $_GET["id"];


    if (isset($_POST["submit_save"])) {
        //echo $_POST["title"] . ", " . $_POST["lessons_number"] . ", " . $_POST["duration"] . ", " . $_POST["price"] . ", " . $_POST["level"] . ", " . $_POST["description"];
        $title = $_POST["title"];
        $lessons_number = $_POST["lessons_number"];
        $duration = $_POST["duration"];
        $price = $_POST["price"];
        $level = $_POST["level"];
        $description = $_POST["description"];
        $image = $_FILES["image"]["name"];
        $backup_image = $_POST["backup_image"];
        //print_r("Update img: " . $image . " to je on.");

        //cesta k ulozeni nahraneho obrazku
        $target = "img/" . basename($_FILES["image"]["name"]);


        if ($image == "") {
            $sql = "UPDATE courses SET title='$title', lessons_number='$lessons_number', duration='$duration', price='$price', level='$level', description='$description' WHERE id_course='$id'";
        } else {
            $sql = "UPDATE courses SET title='$title', lessons_number='$lessons_number', duration='$duration', price='$price', level='$level', description='$description', image='$image' WHERE id_course='$id'";
        }

        if(mysqli_query($conn, $sql)) {
            echo "Course updated";
        } else {
            echo mysqli_error($conn);
        }

        //presunuti uploadnuteho obrazku do slozky img
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
            $msg = "Image uploaded successfully";
            unlink("img/" . $backup_image);
        } else {
            $msg = "There was a problem with upload image";
        }
        header("location:index.php");
    }

    if (isset($_POST["submit_delete"])) {
        $backup_image = $_POST["backup_image"];
        
        $sql = "DELETE FROM courses WHERE id_course='$id'";
        if(mysqli_query($conn, $sql)) {
            echo "Course deleted";
            unlink("img/" . $backup_image);
            header("location:index.php");
        } else {
            echo mysqli_error($conn);
        }
    }

    $sql = "SELECT * FROM courses WHERE id_course=$id";
    $result = mysqli_query($conn, $sql);
    $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $course = $courses[0];

    mysqli_free_result($result);
    mysqli_close($conn);
}

?>
<div class="details" style="margin:0 5rem;">
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-xl-10 col-lg-10 col-sm-12 mx-auto">
                <div class="card">

                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="own-pad mx-4 mt-4 mb-5">
                                            
                                    <div class="form-group">
                                        <label for="title">Title:</label>
                                        <input type="text" class="form-control " name="title" value="<?php echo $course["title"]; ?>">

                                    </div>


                                    <div class="form-together" style="display: flex;">
                                        <div class="form-group">
                                            <label for="lessons">Number of lessons:</label>
                                            <input type="number" class="form-control " name="lessons_number" placeholder="Enter number of lessons" value="<?php echo $course["lessons_number"]; ?>" required>

                                        </div>

                                        <div class="form-group">
                                            <label for="time">Duration:</label>
                                            <input type="number" class="form-control " name="duration" placeholder="Enter duration" value="<?php echo $course["duration"]; ?>" required>

                                        </div>
                                    </div>


                                    <div class="form-group mt-2">
                                        <label for="price">Price:</label>
                                        <select name="price" id="price">
                                            <option value="Free" <?php if ($course["price"]=="Free") echo "selected" ?>>Free</option>
                                            <option value="Paid" <?php if ($course["price"]=="Paid") echo "selected" ?>>Paid</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="level" style="display: block;">Level:</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="level" id="inlineRadio1" value="Noob" <?php if($course["level"]=="Noob") echo "checked" ?>>
                                            <label class="form-check-label" for="inlineRadio1">Noob</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="level" id="inlineRadio2" value="Beginner" <?php if($course["level"]=="Beginner") echo "checked" ?>>
                                            <label class="form-check-label" for="inlineRadio2">Beginner</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="level" id="inlineRadio2" value="Intermediate" <?php if($course["level"]=="Intermediate") echo "checked" ?>>
                                            <label class="form-check-label" for="inlineRadio2">Intermediate</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="level" id="inlineRadio2" value="Advanced" <?php if($course["level"]=="Advanced") echo "checked" ?>>
                                            <label class="form-check-label" for="inlineRadio2">Advanced</label>
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label for="desc">Description:</label>
                                        <textarea class="form-control" name="description" rows="3" placeholder="Say something..." required><?php echo $course["description"] ?></textarea>
                                    </div>
                            </div>
                            <div class="action">
                                <button type="submit" class="btn btn-success mr-3" name="submit_save">Save</button>
                                <button type="submit" class="btn btn-danger" onClick="return confirm('Are you sure you want to delete?')" name="submit_delete">Delete</button>
                            </div>
                        </div>
                        <div class="card-top">

                            <img src="./img/<?php echo $course["image"]; ?>" class="card-img-top" alt="...">

                            <div class="form-group files">
                                <input type="file" class="form-control-file" name="image" value="">
                            </div>
                        </div>
                        <input type="hidden" name="backup_image" value="<?php echo $course["image"]; ?>">
                    </form>
                </div>
            </div>

            
        </div>


    </div>
</div>
<?php
include("footer.php");
?>