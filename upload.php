
    <?php

    if ($_FILES["inp_file"]["error"] > 0)
      {
            echo "Error: " . $_FILES["inp_file"]["error"] . "<br>";
      }
    else
      {
            //$uploads_dir=$_SERVER['DOCUMENT_ROOT'];
			$uploads_dir=dirname(__FILE__);;
            $name = $_FILES["inp_file"]["name"];
             $tmp_name = $_FILES["inp_file"]["tmp_name"];
            move_uploaded_file($tmp_name, "$uploads_dir/$name");
            //echo "location: ".$_SERVER['DOCUMENT_ROOT']."index.php?uploaded=1";

            header("location: index.php?uploaded=$name");
      }
    ?>