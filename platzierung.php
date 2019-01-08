

<?php
    include __DIR__ . '/../src/config.php';
    
    //connection to db
    $conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    

//preparing sql query for platzierung search
$conditions = array();
$searchSql = "SELECT * FROM platzierung";

//search platzierung
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  if (!empty($_GET['HOTELID'])) {
    $conditions[] = "HOTELID = " . $_GET['HOTELID'];
  }

  if (!empty($_GET['REISEID'])) {
    $conditions[] = "REISEID = " . $_GET['REISEID'];
  }

  if (!empty($conditions)) {
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete platzierung
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM platzierung WHERE HOTELID = " . $_GET['HOTELID'] . " AND REISEID = " . $_GET['REISEID'];

 $result = mysqli_query($conn,$deleteSql);

  if (!$result) {
 die("error while deleting  "  . mysqli_error($conn));
      
  } else {
    header("Location: ?");
  }
}

//create platzierung
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO platzierung (HOTELID, REISEID) VALUES(" . $_POST['HOTELID'] . ", " . $_POST['REISEID'] . ")";

 $result = mysqli_query($conn, $createSql);
    
  if (!$result) {
  die("error while creating "  . mysqli_error($conn));
  } else {
    header("Location: ?");
  }
}

//update platzierung
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM platzierung WHERE HOTELID = " . $_GET['HOTELID'] . " AND REISEID = " . $_GET['REISEID'];
    $stmt = mysqli_query($conn, $getRowForUpdate);
    $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);;

  if (!empty($_POST)) {
    $updateSql = "UPDATE platzierung SET HOTELID = " . $_POST['HOTELID'] . ", REISEID = " . $_POST['REISEID'] . " WHERE HOTELID = " . $_GET['HOTELID'] . " AND REISEID = " . $_GET['REISEID'];

      $result = mysqli_query($conn, $updateSql);
      
      if (!$result) {
          die("error while updating " . mysqli_error($conn));
          
      } else {
      header("Location: ?");
    }
  }
}

//add order for beautify
$searchSql .= " ORDER BY HOTELID, REISEID";


//parse and execute sql statement
$result = mysqli_query($conn,$searchSql);

//additional result for fetching hotels for select dropdown
$result1 = mysqli_query($conn, 'select ID, NAME from hotel');


//additional result for fetching reise for select dropdown
$result2 = mysqli_query($conn, 'select ID, NAME from reise');

if (!$result) {
 die("error while adding " . mysqli_error($conn));
}

if (!$result1) {
   die("error while adding hotel " . mysqli_error($conn));
}
?>

<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Platzierung</title>
  </head>

  <body>
    <div class="container">
      <br>

      <div class="col-md-3">
        <ul class="nav nav-pills nav-stacked">
          <li>
                <a href="beratung.php">Beratung</a>
              </li>
              <li>
                <a href="buchung.php">Buchung</a>
              </li>
              <li>
                <a href="hotel.php">Hotel</a>
              </li>
              <li>
                <a href="kunde.php">Kunde</a>
              </li>
              <li>
                <a href="mitarbeiter.php">Mitarbeiter Registrieren</a>
              </li>
              <li>
                <a href="personen.php">Personen</a>
              </li>
              <li class="active">
                <a href="platzierung.php">Platzierung</a>
              </li>
              <li>
                <a href="procedure.php">Procedure</a>
              </li>
              <li>
                <a href="reise.php">Reise</a>
              </li>
              <li>
                <a href="reisebuero.php">Reisebuero</a>
              </li>
              <li>
                <a href="zimmer.php">Zimmer</a>
              </li>
        </ul>
      </div>

      <div class="col-md-9">
        <!-- навигация -->
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Platzierung</li>
        </ol>

        <!-- ошибки если есть -->

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger">
            <?=isset($error['message']) ? $error['message'] : ''?> </br>
            <small><?=isset($error['sqltext']) ? $error['sqltext'] : ''?></small> </br>
            <small><?=isset($error['offset']) ? 'Error position: ' . $error['offset'] : ''?></small>
          </div>
        <?php endif; ?>

        <!-- основная панель с таблицей -->
        <div class="panel panel-default">
          <div class="panel-body">

            <!-- основная панель с таблицей -->
            <form method='get'>
              <input type="hidden" name="action" value="search">

              <!-- кнопка с обновлением -->
              <input class="btn btn-link" type="submit" value="Refresh" />

              <!-- основная таблица -->
              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <th class="th1" width="300">HOTEL</th>
                    <th class="th1" width="300">REISE</th>
                    <th width="50">update</th> 
                    <th width="50">delete</th>      
                  </tr>
                </thead>
                <tbody>
                  <!-- строка с поиском -->
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>

                  <!-- вывод строк с информацией из базы -->
                  <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)): ?>
                    <tr>
                      <td>
                        <?php
                          while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                            echo $row2['ID'] === $row['HOTELID'] ? $row2['NAME'] : '';
                          }

                          //rewind cursor
                          oci_execute($stmt2);
                        ?>
                      </td>
                      <td>
                        <?php
                          while($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {
                            echo $row3['ID'] === $row['REISEID'] ? $row3['NAME'] : '';
                          }

                          //rewind cursor
                         // oci_execute($stmt3);
                            mysqli_query($result3);
                        ?>
                      </td>
                      <td><a href="?action=update&HOTELID=<?= $row["HOTELID"] ?>&REISEID=<?= $row["REISEID"] ?>">update</a></td>
                      <td><a href="?action=delete&HOTELID=<?= $row["HOTELID"] ?>&REISEID=<?= $row["REISEID"] ?>">delete</a></td>
                    </tr>
                  <?php endwhile; ?>

                </tbody>
              </table>
            </form>
          </div>
        </div>
        

        
        <?php
            mysqli_free_result($result2);
            mysqli_close($conn);
            mysqli_free_result($result1);
            mysqli_close($conn);
            mysqli_free_result($result);
            mysqli_close($conn);
            ?>

        <!-- вторая панель с формой -->
        <div class="panel panel-default">
          <div class="panel-body">
            
            <!-- форма -->
            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&HOTELID=' . $_GET['HOTELID'] . '&REISEID=' . $_GET['REISEID'] : 'create'?>" method='post'>

              <div class="form-group">
                <label class="col-sm-3 control-label">HOTEL</label>
                <div class="col-sm-9">
                  <select class="form-control" name='HOTELID'>
                    <?php while($row = oci_fetch_array($stmt2, OCI_ASSOC)): ?>
                      <option value="<?= $row['ID'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['HOTELID'] === $row['ID'] ? 'selected' : '') ?>><?= $row['NAME'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">REISE</label>
                <div class="col-sm-9">
                  <select class="form-control" name='REISEID'>
                    <?php while($row = oci_fetch_array($stmt3, OCI_ASSOC)): ?>
                      <option value="<?= $row['ID'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['REISEID'] === $row['ID'] ? 'selected' : '') ?>><?= $row['NAME'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <!-- строка с кнопками отправки и сброса формы -->
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="submit" class="btn btn-default">Save</button>
                  <a class="btn" href="?">Cancel</a>
                </div>
              </div>
            </form>

          </div>
        </div>

        <?php  oci_free_statement($stmt2); ?>
        
      </div>
    </div>
</body>
</html>
