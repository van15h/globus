<?php

    include __DIR__ . '/../src/config.php';
    //connection to db
    $conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


//preparing sql query for beratung search
$conditions = array();
$searchSql = "SELECT * FROM Beratung";

//search beratung
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  if (!empty($_GET['KUNDENUMMER'])) {
    $conditions[] = "KUNDENUMMER = " . $_GET['KUNDENUMMER'];
  }

  if (!empty($_GET['REISEID'])) {
    $conditions[] = "REISEID = " . $_GET['REISEID'];
  }

  if (!empty($_GET['STEUERNUMMER'])) {
    $conditions[] = "STEUERNUMMER = " . $_GET['STEUERNUMMER'];
  }

  if (!empty($conditions)) {
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete beratung
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM beratung WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND STEUERNUMMER = " . $_GET['STEUERNUMMER'];

   $result = mysqli_query($conn,$deleteSql);

  if (!$result) {
    die("error while deleting from beratung "  . mysqli_error($conn));
  } else {
    header("Location: ?");
  }
}

//create beratung
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Beratung (KUNDENUMMER, REISEID, STEUERNUMMER) VALUES(" . $_POST['KUNDENUMMER'] . ", " . $_POST['REISEID'] . ", " . $_POST['STEUERNUMMER'] . ")";

  $result = mysqli_query($conn, $createSql);

  if (!$result) {
    die("error while creating in beratung"  . mysqli_error($conn));
  } else {
    header("Location: ?");
  }
}

//update beratung
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Beratung WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND STEUERNUMMER = " . $_GET['STEUERNUMMER'];

    $stmt = mysqli_query($conn, $getRowForUpdate);
    $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateSql = "UPDATE Beratung SET KUNDENUMMER = " . $_POST['KUNDENUMMER'] . ", REISEID = " . $_POST['REISEID'] . ", STEUERNUMMER = " . $_POST['STEUERNUMMER'] . " WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND STEUERNUMMER = " . $_GET['STEUERNUMMER'];

  $result = mysqli_query($conn, $updateSql);

      if (!$result) {
        die("error while updating beratung " . mysqli_error($conn));
      }
      else{
        header("Location: ?");
    }
  }
}

//add order for beautify
$searchSql .= " ORDER BY STEUERNUMMER, KUNDENUMMER, REISEID";

  //execute sql statement
  $result = mysqli_query($conn,$searchSql);

//additional result for fetching kunde for select dropdown
$result2 = mysqli_query($conn, 'select KUNDENUMMER from Kunde');

//additional result for fetching reise for select dropdown
$result3 = mysqli_query($conn, 'select ID, NAME from Reise');

//additional result for fetching mitarbeiter for select dropdown
$result4 = mysqli_query($conn, 'select STEUERNUMMER from Mitarbeiter');

if (!$result) {
  $error = mysqli_query($stmt);
}

if (!$result2) {
  $error = mysqli_query($stmt2);
}

if (!$result3) {
  $error = mysqli_query($stmt3);
}

if (!$result4) {
  $error = mysqli_query($stmt4);
}

?>

<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Beratung</title>
  </head>

  <body>
    <div class="container">
      <br>

      <div class="col-md-3">
        <ul class="nav nav-pills nav-stacked">
              <li class="active">
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
          <li class="active">Beratung</li>
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
                    <th class="th1" width="300">STEUERNUMMER</th>
                    <th class="th1" width="300">KUNDE</th>
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
                    <td></td>
                  </tr>

                  <!-- вывод строк с информацией из базы -->
                  <?php while($row = mysqli_fetch_array($stmt, OCI_ASSOC)): ?>
                    <tr>
                      <td>
                        <?php
                          while($row4 = mysqli_fetch_array($stmt4, OCI_ASSOC)) {
                            echo $row4['STEUERNUMMER'] === $row['STEUERNUMMER'] ? $row4['STEUERNUMMER'] : '';
                          }

                          //rewind cursor
                          mysqli_query($stmt4);
                        ?>
                      </td>
                      <td>
                        <?php
                          while($row2 = mysqli_fetch_array($stmt2, OCI_ASSOC)) {
                            echo $row2['KUNDENUMMER'] === $row['KUNDENUMMER'] ? $row2['KUNDENUMMER'] : '';
                          }

                          //rewind cursor
                          mysqli_query($stmt2);
                        ?>
                      </td>
                      <td>
                        <?php
                          while($row3 = mysqli_fetch_array($stmt3, OCI_ASSOC)) {
                            echo $row3['ID'] === $row['REISEID'] ? $row3['NAME'] : '';
                          }

                          //rewind cursor
                          mysqli_query($stmt3);
                        ?>
                      </td>
                      <td><a href="?action=update&KUNDENUMMER=<?= $row["KUNDENUMMER"] ?>&REISEID=<?= $row["REISEID"] ?>&STEUERNUMMER=<?= $row["STEUERNUMMER"] ?>">update</a></td>
                      <td><a href="?action=delete&KUNDENUMMER=<?= $row["KUNDENUMMER"] ?>&REISEID=<?= $row["REISEID"] ?>&STEUERNUMMER=<?= $row["STEUERNUMMER"] ?>">delete</a></td>
                    </tr>
                  <?php endwhile; ?>

                </tbody>
              </table>
            </form>
          </div>
        </div>



        <?php  oci_free_statement($stmt); ?>

        <!-- вторая панель с формой -->
        <div class="panel panel-default">
          <div class="panel-body">

            <!-- форма -->
            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&KUNDENUMMER=' . $_GET['KUNDENUMMER'] . '&REISEID=' . $_GET['REISEID'] . '&STEUERNUMMER=' . $_GET['STEUERNUMMER'] : 'create'?>" method='post'>

              <div class="form-group">
                <label class="col-sm-3 control-label">STEUERNUMMER</label>
                <div class="col-sm-9">
                  <select class="form-control" name='STEUERNUMMER'>
                    <?php while($row = mysqli_fetch_array($stmt4, OCI_ASSOC)): ?>
                      <option value="<?= $row['STEUERNUMMER'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['STEUERNUMMER'] === $row['STEUERNUMMER'] ? 'selected' : '') ?>><?= $row['STEUERNUMMER'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">KUNDE</label>
                <div class="col-sm-9">
                  <select class="form-control" name='KUNDENUMMER'>
                    <?php while($row = mysqli_fetch_array($stmt2, OCI_ASSOC)): ?>
                      <option value="<?= $row['KUNDENUMMER'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['KUNDENUMMER'] === $row['KUNDENUMMER'] ? 'selected' : '') ?>><?= $row['KUNDENUMMER'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">REISE</label>
                <div class="col-sm-9">
                  <select class="form-control" name='REISEID'>
                    <?php while($row = mysqli_fetch_array($stmt3, OCI_ASSOC)): ?>
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
