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
$searchSql = "SELECT Platzierung .hotelid AS hotelid, Platzierung.reiseid AS reiseid, Hotel.name AS hotelname, Reise.name AS reisename FROM Platzierung
INNER JOIN Hotel ON Hotel.id = Platzierung .hotelid
INNER JOIN Reise ON Reise.id = Platzierung.reiseid";

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
  $deleteSql = "DELETE FROM Platzierung WHERE HOTELID = " . $_GET['HOTELID'] . " AND REISEID = " . $_GET['REISEID'];
 $result = mysqli_query($conn,$deleteSql);

  if (!$result) {
 die("error while deleting  "  . mysqli_error($conn));

  } else {
    header("Location: ?");
  }
}

//create platzierung
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Platzierung (HOTELID, REISEID) VALUES(" . $_POST['HOTELID'] . ", " . $_POST['REISEID'] . ")";

 $result = mysqli_query($conn, $createSql);

  if (!$result) {
  die("error while creating "  . mysqli_error($conn));
  } else {
    header("Location: ?");
  }
}

//update platzierung
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Platzierung WHERE HOTELID = " . $_GET['HOTELID'] . " AND REISEID = " . $_GET['REISEID'];
    $stmt = mysqli_query($conn, $getRowForUpdate);
    $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);;

  if (!empty($_POST)) {
    $updateSql = "UPDATE Platzierung SET HOTELID = " . $_POST['HOTELID'] . ", REISEID = " . $_POST['REISEID'] . " WHERE HOTELID = " . $_GET['HOTELID'] . " AND REISEID = " . $_GET['REISEID'];

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
$result1 = mysqli_query($conn, 'select id, name from Hotel');


//additional result for fetching reise for select dropdown
$result2 = mysqli_query($conn, 'select id, name from Reise');

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
                <a href="kunde.php">Kunde Registrieren</a>
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
          <li class="active">Platzierung</li>
        </ol>


        <?php if (!empty($error)): ?>
          <div class="alert alert-danger">
            <?=isset($error['message']) ? $error['message'] : ''?> </br>
            <small><?=isset($error['sqltext']) ? $error['sqltext'] : ''?></small> </br>
            <small><?=isset($error['offset']) ? 'Error position: ' . $error['offset'] : ''?></small>
          </div>
        <?php endif; ?>

        <div class="panel panel-default">
          <div class="panel-body">

            <form method='get'>
              <input type="hidden" name="action" value="search">

              <input class="btn btn-link" type="submit" value="Refresh" />


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
 
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>

                  <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                     <tr>
                      <td class="th1"><?= $row['hotelname'] ?></td>
                      <td><?= $row['reisename']; ?></td>
                      <td><a href="?action=update&HOTELID=<?= $row["hotelid"] ?>&REISEID=<?= $row["reiseid"] ?>">update</a></td>
                      <td><a href="?action=delete&HOTELID=<?= $row["hotelid"] ?>&REISEID=<?= $row["reiseid"] ?>">delete</a></td>
                    </tr>
                  <?php }?>
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

            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&HOTELID=' . $_GET['HOTELID'] . '&REISEID=' . $_GET['REISEID'] : 'create'?>" method='post'>

              <div class="form-group">
                <label class="col-sm-3 control-label">HOTEL</label>
                <div class="col-sm-9">
                  <select class="form-control" name='HOTELID'>
                    <?php while($row = mysqli_fetch_array($result1, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['id'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['hotelid'] === $row['id'] ? 'selected' : '') ?>><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">REISE</label>
                <div class="col-sm-9">
                  <select class="form-control" name='REISEID'>
                    <?php while($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['id'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['reiseid'] === $row['id'] ? 'selected' : '') ?>><?= $row['name'] ?></option>
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
