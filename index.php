<?php
  //pripojeni k db
  include "mysqli_connect.php";

  //kontrola pripojeni
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
  }
  $search = "";
  if (isset($_POST["submit_search"])) {
    $search = $_POST["search"];
    $sql = "SELECT * FROM courses WHERE title LIKE '%$search%'";
    if (strlen($search) == 0) {
      $sql = "SELECT * FROM courses";
    }
  } elseif (isset($_POST["submit_filter"])) {
    
    $filter_level = "";
    if ($_POST["level"] != "all_levels") {
      $filter_level = $_POST["level"];
    }

    $filter_price = "";
    if (isset($_POST["price"])) {
      if (count($_POST["price"]) == 1) {
        $filter_price = $_POST["price"][0];
      }
    }

    $is_short = false;
    $is_medium = false;
    $is_long = false;
    
    if (isset($_POST["duration"]) && count($_POST["duration"]) != 3) {
        if (count($_POST["duration"]) == 1) {
          if (in_array("short",$_POST["duration"])) {
            $sql = "SELECT * FROM courses WHERE price LIKE '%$filter_price%' AND level LIKE '%$filter_level%' AND duration < 1";
            $is_short = true;
          }
          if (in_array("medium",$_POST["duration"])) {
            $sql = "SELECT * FROM courses WHERE price LIKE '%$filter_price%' AND level LIKE '%$filter_level%' AND duration > 1 AND duration < 3";
            $is_medium = true;
          }
          if (in_array("long",$_POST["duration"])) {
            $sql = "SELECT * FROM courses WHERE price LIKE '%$filter_price%' AND level LIKE '%$filter_level%' AND duration > 3";
            $is_long = true;
          }
        } else {
          // 2
          if (in_array("short",$_POST["duration"]) && in_array("medium",$_POST["duration"])) {
            $sql = "SELECT * FROM courses WHERE price LIKE '%$filter_price%' AND level LIKE '%$filter_level%' AND duration > 0 AND duration < 3";
            $is_short = true;
            $is_medium = true;
          }
          if (in_array("medium",$_POST["duration"]) && in_array("long",$_POST["duration"])) {
            $sql = "SELECT * FROM courses WHERE price LIKE '%$filter_price%' AND level LIKE '%$filter_level%' AND duration > 1";
            $is_medium = true;
            $is_long = true;
          }
          if (in_array("long",$_POST["duration"]) && in_array("short",$_POST["duration"])) {
            $sql = "SELECT * FROM courses WHERE price LIKE '%$filter_price%' AND level LIKE '%$filter_level%' AND duration < 1 OR duration > 3";
            $is_short = true;
            $is_long = true;
          }
        }
    } else {
      $is_short = true;
      $is_medium = true;
      $is_long = true;
      $sql = "SELECT * FROM courses WHERE price LIKE '%$filter_price%' AND level LIKE '%$filter_level%'";
    }

  } else {
    $sql = "SELECT * FROM courses";
  }

  
  $result = mysqli_query($conn, $sql);

  $search_empty = false;
  if ($result->num_rows == 0) {
    $search_empty = true;
  }

  $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);

  mysqli_free_result($result);
  mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Abrakadabra | Online Courses </title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="css/all.css">

  <!-- Custom styles for this template -->
  <link href="css/styles.css" rel="stylesheet">

</head>

<body id="page-top">
  <div class="top"></div>
  <header class="text-dark">

    <?php include("menu.php"); ?>
    <div class="container text-center header text-white">

      <h1>Online Courses for everyone</h1>
      <p class="lead">Learn, build and extend yours skills with us</p>

      <div class="row align-items-center justify-content-center del">
        <div class="col-lg-8 col-sm-10">
          
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

            <div class="form-group has-search">
              <span class="fa fa-search form-control-feedback"></span>
              <input type="text" class="form-control" name="search" placeholder="Search what you're looking for" value=<?php echo $search; ?>>
              <div class="input-group-append">
                <button class="btn btn-secondary" name="submit_search" type="submit">
                  Search
                </button>
              </div>
            </div>

          </form>

        </div>
      </div>

    </div>

  </header>

  <section id="courses" class="text-dark mt-5">
    <div class="container-fluid">

      <div class="row">

        <div class="col-xl-3 col-lg-6 mx-auto">
          <h2 class="text-white mb-5">Filter results:</h2>

          <div class="filter">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
              
              <label for="level">Level</label>
              <select name="level" id="level">
                <option value="all_levels" <?php if(!isset($_POST["level"]) || $filter_level=="") echo "selected" ?>>All levels</option>
                <option value="Noob" <?php if($filter_level=="Noob") echo "selected" ?>>Noob</option>
                <option value="Beginner" <?php if($filter_level=="Beginner") echo "selected" ?>>Beginner</option>
                <option value="Intermediate" <?php if($filter_level=="Intermediate") echo "selected" ?>>Intermediate</option>
                <option value="Advanced" <?php if($filter_level=="Advanced") echo "selected" ?>>Advanced</option>
              </select>
              <br><br>

              <label for="duration">Duration</label>
              <input type="checkbox" id="duration" name="duration[]" value="short" <?php if(!isset($_POST["duration"]) || $is_short) echo "checked" ?>>
              <span> Short (< 1hr) </span> <br>
              <input type="checkbox" id="duration2" name="duration[]" value="medium" <?php if(!isset($_POST["duration"]) || $is_medium) echo "checked" ?>>
              <span> Medium (> 1-3hr)</span><br>
              <input type="checkbox" id="duration3" name="duration[]" value="long" <?php if(!isset($_POST["duration"]) || $is_long) echo "checked" ?>>
              <span> Long (> 3hr)</span>
              <br><br>

              <label for="price">Price</label>
              <?php
              if (isset($_POST["price"]) && ($filter_price != "Free" && $filter_price != "")) {
              ?>
                  <input type="checkbox" id="price" name="price[]" value="Free">
              <?php
              } else {
              ?>
                <input type="checkbox" id="price" name="price[]" value="Free" checked>
              <?php
              }
              ?>
              <span> Free</span> <br>
              <?php
              if (isset($_POST["price"]) && ($filter_price != "Paid" && $filter_price != "")) {
              ?>
                <input type="checkbox" id="price2" name="price[]" value="Paid">
              <?php
              } else {
              ?>
                <input type="checkbox" id="price2" name="price[]" value="Paid" checked>
              <?php
              }
              ?>
              <span> Paid </span><br>

              <br><br>

              <button type="submit" name="submit_filter" class="btn btn-dark">Filter</button>
            </form>
          </div>

        </div>

        <div class="col-xl-9 col-lg-6 mx-auto">
          <h2 class="text-white mb-5">Programming Courses</h2>

          <div class="row">
            <?php foreach ($courses as $course) :  ?>
              <div class="col-xl-4 col-lg-12">
                <a href="details.php?id=<?php echo $course['id_course']; ?>">
                  <div class="card mb-5">
                    <img src="img/<?php echo $course["image"]; ?>" class="card-img-top" alt="...">
                    <div class="card-edit"><img src="img/edit.svg" alt="Edit"></div>
                    <div class="card-body">
                      <h5 class="card-title"><?php echo $course["title"]; ?></h5>
                      <h6 class="mb-3"><?php echo $course["lessons_number"]; ?> lessons | <?php echo $course["duration"]; ?> hours</h6>
                      <p class="card-text"><?php echo substr($course["description"], 0, 150) . ((strlen($course["description"]) > 150) ? "..." : ""); ?></p>
                      <div class="more">
                        <!--<a class="btn btn-primary" href="details.php?id=<?php echo $course['id_course']; ?>">More info</a>-->
                        
                        <span class="level"><?php echo $course["level"]; ?>
                        <ul>
                          <?php 
                          switch ($course["level"]) {
                            case "Noob":
                              ?>
                              <li></li>
                              <li></li>
                              <li></li>
                              <?php 
                            break;
                            case "Beginner":
                              ?>
                              <li class="active"></li>
                              <li></li>
                              <li></li>
                              <?php 
                            break;
                            case "Intermediate":
                              ?>
                              <li class="active"></li>
                              <li class="active"></li>
                              <li></li>
                              <?php 
                            break;
                            case "Advanced":
                              ?>
                              <li class="active"></li>
                              <li class="active"></li>
                              <li class="active"></li>
                              <?php 
                            break;
                            default:
                              ?>
                              <li></li>
                              <?php 
                          }
                          ?>
                          </ul></span>

                        <?php 
                        if ($course["price"] == "Free") {
                        ?>
                          <span class="success"> Free </span>
                        <?php 
                        } else {
                        ?>
                          <span class="success" style="color: orange !important; border-color: orange;"> Paid </span>
                        <?php
                        }
                        ?>
                      </div>

                    </div>
                  </div>
                </a>
              </div>
            <?php endforeach; 
            if ($search_empty) {
              ?>

                <p style="color:white">Nothing to see here :)</p>
              <?php
            }
            ?>
          </div>

        </div>

      </div>

    </div>
  </section>


  <section id="carousel" style="margin-bottom:3rem;">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-xl-12 mx-auto">
          <div class="p-5 shadow rounded text-white centr" style="height: 40vh;">
            <!-- Bootstrap carousel-->
            <div class="carousel slide" id="carouselExampleIndicators" data-ride="carousel">
              <!-- Bootstrap carousel indicators [nav] -->
              <ol class="carousel-indicators mb-0">
                <li class="active" data-target="#carouselExampleIndicators" data-slide-to="0"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
              </ol>


              <!-- Bootstrap inner [slides]-->
              <div class="carousel-inner px-5 pb-4">
                <!-- Carousel slide-->
                <div class="carousel-item active">
                  <div class="media"><img class="rounded-circle img-thumbnail" src="img/carousel/man1.jpg" alt="Man" width="150">
                    <div class="media-body">
                      <blockquote class="blockquote border-0 p-0">
                        <p class="font-italic lead"> <i class="fa fa-quote-left mr-3 text-success"></i>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

                      </blockquote>
                    </div>
                  </div>
                </div>

                <!-- Carousel slide-->
                <div class="carousel-item">
                  <div class="media"><img class="rounded-circle img-thumbnail" src="img/carousel/man2.jpg" alt="Man2" width="150">
                    <div class="media-body">
                      <blockquote class="blockquote border-0 p-0">
                        <p class="font-italic lead"> <i class="fa fa-quote-left mr-3 text-success"></i>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

                      </blockquote>
                    </div>
                  </div>
                </div>

                <!-- Carousel slide-->
                <div class="carousel-item">
                  <div class="media"><img class="rounded-circle img-thumbnail" src="img/carousel/woman.jpg" alt="Woman" width="150">
                    <div class="media-body">
                      <blockquote class="blockquote border-0 p-0">
                        <p class="font-italic lead"> <i class="fa fa-quote-left mr-3 text-success"></i>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

                      </blockquote>
                    </div>
                  </div>
                </div>
              </div>



            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php
    include("footer.php");
  ?>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</body>

</html>