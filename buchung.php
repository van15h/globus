
<?php
//connection to db
include __DIR__ . '/../src/config.php';

//connection to db
$conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


//preparing sql query for buchung search
$conditions = array();
$searchSql = "SELECT * FROM Buchung";

//search buchung
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  if (!empty($_GET['KUNDENUMMER'])) {
    $conditions[] = "KUNDENUMMER = " . $_GET['KUNDENUMMER'];
  }

  if (!empty($_GET['REISEID'])) {
    $conditions[] = "REISEID = " . $_GET['REISEID'];
  }

  if (!empty($_GET['REISEBUEROID'])) {
    $conditions[] = "REISEBUEROID = " . $_GET['REISEBUEROID'];
  }

  if (!empty($conditions)) {
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete buchung
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM buchung WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND REISEBUEROID = " . $_GET['REISEBUEROID'];

 
  
  $result = mysqli_query($conn,$deleteSql);

  if (!$result) {
    die("error while creating booking");
  } else {
    header("Location: ?");
  }
}

//create buchung
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO buchung (KUNDENUMMER, REISEID, REISEBUEROID) VALUES(" . $_POST['KUNDENUMMER'] . ", " . $_POST['REISEID'] . ", " . $_POST['REISEBUEROID'] . ")";

   $result = mysqli_query($conn, $createSql);

  if (!$result) {
    die("error while creating booking");
  } else {
    header("Location: ?");
  }
}

//update buchung
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM buchung WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND REISEBUEROID = " . $_GET['REISEBUEROID'];
  $stmt = mysqli_query($conn, $getRowForUpdate);
  //oci_execute($stmt);
  $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateSql = "UPDATE buchung SET KUNDENUMMER = " . $_POST['KUNDENUMMER'] . ", REISEID = " . $_POST['REISEID'] . ", REISEBUEROID = " . $_POST['REISEBUEROID'] . " WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND REISEBUEROID = " . $_GET['REISEBUEROID'];

   
    $result = mysqli_query($conn, $updateSql);

    if (!$result) {
      die("error while updating booking");
    } else {
      header("Location: ?");
    }
  }
}

//add order for beautify
$searchSql .= " ORDER BY KUNDENUMMER, REISEID, REISEBUEROID";
$result = mysqli_query($conn,$searchSql);

//parse and execute sql statement
//$stmt = oci_parse($conn, $searchSql);
//$result = oci_execute($stmt);

//additional result for fetching kunde for select dropdown
//$stmt2 = oci_parse($conn, 'select KUNDENUMMER from kunde');
//$result2 = oci_execute($stmt2);
$result2 = mysqli_query($conn, 'select KUNDENUMMER from kunde');

//additional result for fetching reise for select dropdown
//$stmt3 = oci_parse($conn, 'select ID, NAME from reise');
//$result3 = oci_execute($stmt3);
$result3 = mysqli_query($conn, 'select ID, NAME from reise');

//additional result for fetching reisebuero for select dropdown
//$stmt4 = oci_parse($conn, 'select ID, NAME from reisebuero');
//$result4 = oci_execute($stmt4);

$result4 = mysqli_query($conn, 'select ID, NAME from reisebuero');
/**
if (!$result) {
      die("error while ....booking");
    } else {
      header("Location: ?");
    }
	
if (!$result2) {
   die("error while ....booking");
    } else {
      header("Location: ?");
    }
/
if (!$result) {
  $error = oci_error($stmt);
}

if (!$result2) {
  $error = oci_error($stmt2);
}

if (!$result3) {
  $error = oci_error($stmt3);
}

if (!$result4) {
  $error = oci_error($stmt4);
}
**/
?>

<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Buchung</title>
  </head>

  <body>
    <div class="container">
      <br>

      <div class="col-md-3">
        <ul class="nav nav-pills nav-stacked">
              <li>
                <a href="beratung.php">Beratung</a>
              </li>
              <li class="active">
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
              <li>
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
          <li class="active">Buchung</li>
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
                    <th class="th1" width="300">KUNDE</th>
                    <th class="th1" width="300">REISE</th>
                    <th class="th1" width="300">REISEBUERO</th>
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
                    <td></td>
                  </tr>

                  <!-- вывод строк с информацией из базы -->
                  <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)): ?>
                    <tr>
                      <td>
                        <?php
                          while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                            echo $row2['kundenummer'] === $row['kundenummer'] ? $row2['kundenummer'] : '';
                          }

                          //rewind cursor
                          //oci_execute($stmt2);
                        ?>
                      </td>
                      <td>
                        <?php
                          while($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {
                            echo $row3['ID'] === $row['REISEID'] ? $row3['NAME'] : '';
                          }

                          //rewind cursor
                         // oci_execute($stmt3);
                        ?>
                      </td>
                      <td>
                        <?php
                          while($row4 = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {
                            echo $row4['ID'] === $row['REISEBUEROID'] ? $row4['NAME'] : '';
                          }

                          //rewind cursor
                         // oci_execute($stmt4);
                        ?>
                      </td>
                      <td><a href="?action=update&KUNDENUMMER=<?= $row["KUNDENUMMER"] ?>&REISEID=<?= $row["REISEID"] ?>&REISEBUEROID=<?= $row["REISEBUEROID"] ?>">update</a></td>
                      <td><a href="?action=delete&KUNDENUMMER=<?= $row["KUNDENUMMER"] ?>&REISEID=<?= $row["REISEID"] ?>&REISEBUEROID=<?= $row["REISEBUEROID"] ?>">delete</a></td>
                    </tr>
                  <?php endwhile; ?>

                </tbody>
              </table>
            </form>
          </div>
        </div>
        

        
     

        <!-- вторая панель с формой -->
        <div class="panel panel-default">
          <div class="panel-body">
            
            <!-- форма -->
            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&KUNDENUMMER=' . $_GET['KUNDENUMMER'] . '&REISEID=' . $_GET['REISEID'] . '&REISEBUEROID=' . $_GET['REISEBUEROID'] : 'create'?>" method='post'>

              <div class="form-group">
                <label class="col-sm-3 control-label">KUNDE</label>
                <div class="col-sm-9">
                  <select class="form-control" name='KUNDENUMMER'>
                    <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['KUNDENUMMER'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['KUNDENUMMER'] === $row['KUNDENUMMER'] ? 'selected' : '') ?>><?= $row['KUNDENUMMER'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">REISE</label>
                <div class="col-sm-9">
                  <select class="form-control" name='REISEID'>
                    <?php while($row = mysqli_fetch_array($result3, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['ID'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['REISEID'] === $row['ID'] ? 'selected' : '') ?>><?= $row['NAME'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">REISEBUERO</label>
                <div class="col-sm-9">
                  <select class="form-control" name='REISEBUEROID'>
                    <?php while($row = mysqli_fetch_array($result4, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['ID'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['REISEBUEROID'] === $row['ID'] ? 'selected' : '') ?>><?= $row['NAME'] ?></option>
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

       
        
      </div>
    </div>
</body>
</html>
