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
// works only for id
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  if (!empty($_GET['KUNDE'])) {
    $conditions[] = "KUNDENUMMER = " . $_GET['KUNDE'];
  }

  if (!empty($conditions)) {
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }

}

//delete buchung
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM Buchung WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND REISEBUEROID = " . $_GET['REISEBUEROID'];

  $result = mysqli_query($conn,$deleteSql);

  if (!$result) {
    die("error while creating booking" . mysqli_error($conn));
  }
}

//create buchung
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Buchung (KUNDENUMMER, REISEID, REISEBUEROID) VALUES(" . $_POST['KUNDENUMMER'] . ", " . $_POST['REISEID'] . ", " . $_POST['REISEBUEROID'] . ")";

   $result = mysqli_query($conn, $createSql);

  if (!$result) {
    die("error while creating booking" . mysqli_error($conn));
  }
}

//update buchung
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Buchung WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND REISEBUEROID = " . $_GET['REISEBUEROID'];
  $stmt = mysqli_query($conn, $getRowForUpdate);
  //oci_execute($stmt);
  $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateSql = "UPDATE Buchung SET KUNDENUMMER = " . $_POST['KUNDENUMMER'] . ", REISEID = " . $_POST['REISEID'] . ", REISEBUEROID = " . $_POST['REISEBUEROID'] . " WHERE KUNDENUMMER = " . $_GET['KUNDENUMMER'] . " AND REISEID = " . $_GET['REISEID'] . " AND REISEBUEROID = " . $_GET['REISEBUEROID'];


    $result = mysqli_query($conn, $updateSql);

    if (!$result) {
      die("error while updating booking" . mysqli_error($conn));
    } else {
      header("Location: ?" . mysqli_error($conn));
    }
  }
}

//add order for beautify
$searchSql .= " ORDER BY KUNDENUMMER, REISEID, REISEBUEROID";

//parse and execute sql statement
$result = mysqli_query($conn,$searchSql);

//additional result for fetching kunde for select dropdown
$result2 = mysqli_query($conn, 'select kundenummer from Kunde');

//additional result for fetching reise for select dropdown
$result3 = mysqli_query($conn, 'select id, name from Reise');

//additional result for fetching reisebuero for select dropdown
$result4 = mysqli_query($conn, 'select id, name from Reisebuero');


if (!$result) {
      die("error while get from buchung" . mysqli_error($conn));
    }

if (!$result2) {
   die("error while get from Kunde" . mysqli_error($conn));
    }

    if (!$result3) {
      die("error while get from Reise" . mysqli_error($conn));
       }

       if (!$result4) {
        die("error while get From" . mysqli_error($conn));
         }

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
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Buchung</li>
        </ol>

        <div class="panel panel-default">
          <div class="panel-body">

            <form method='get'>
              <input type="hidden" name="action" value="search">

              <input class="btn btn-link" type="submit" value="Refresh" />

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
                  <tr>
                    <td><input name='KUNDE' value='<?= @$_GET['KUNDE'] ?: '' ?>' style="width:100%" /></td>
                  </tr>

                  <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)):
                    ?>
                    <tr>
                      <td>
                        <?php
                          while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                            echo $row2['kundenummer'] === $row['kundenummer'] ? $row2['kundenummer'] : '';
                          }

                          $result2 = mysqli_query($conn, 'select kundenummer from Kunde');
                        ?>
                      </td>
                      <td>
                        <?php
                          while($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC)) {
                            echo $row3['id'] === $row['reiseid'] ? $row3['name'] : '';
                          }

                         $result3 = mysqli_query($conn, 'select id, name from Reise');
                        ?>
                      </td>
                      <td>
                        <?php
                          while($row4 = mysqli_fetch_array($result4, MYSQLI_ASSOC)) {
                            echo $row4['id'] === $row['reisebueroid'] ? $row4['name'] : '';
                          }

                         $result4 = mysqli_query($conn, 'select id, name from Reisebuero');
                        ?>
                      </td>
                      <td><a href="?action=update&KUNDENUMMER=<?= $row["kundenummer"] ?>&REISEID=<?= $row["reiseid"] ?>&REISEBUEROID=<?= $row["reisebueroid"] ?>">update</a></td>
                      <td><a href="?action=delete&KUNDENUMMER=<?= $row["kundenummer"] ?>&REISEID=<?= $row["reiseid"] ?>&REISEBUEROID=<?= $row["reisebueroid"] ?>">delete</a></td>
                    </tr>
                  <?php endwhile; ?>

                </tbody>
              </table>
            </form>
          </div>
        </div>

        <?php
            mysqli_free_result($result);
            mysqli_close($conn);
            ?>

        <div class="panel panel-default">
          <div class="panel-body">

            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&KUNDENUMMER=' . $_GET['KUNDENUMMER'] . '&REISEID=' . $_GET['REISEID'] . '&REISEBUEROID=' . $_GET['REISEBUEROID'] : 'create'?>" method='post'>

              <div class="form-group">
                <label class="col-sm-3 control-label">KUNDE</label>
                <div class="col-sm-9">
                  <select class="form-control" name='KUNDENUMMER'>
                    <?php
                    while($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['kundenummer'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['kundenummer'] === $row['kundenummer'] ? 'selected' : '') ?>><?= $row['kundenummer'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">REISE</label>
                <div class="col-sm-9">
                  <select class="form-control" name='REISEID'>
                    <?php while($row = mysqli_fetch_array($result3, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['id'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['reiseid'] === $row['id'] ? 'selected' : '') ?>><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">REISEBUERO</label>
                <div class="col-sm-9">
                  <select class="form-control" name='REISEBUEROID'>
                    <?php while($row = mysqli_fetch_array($result4, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['id'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['reisebuero'] === $row['id'] ? 'selected' : '') ?>><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

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
