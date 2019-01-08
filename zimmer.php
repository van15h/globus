<?php
//connection to db
include __DIR__ . '/../src/config.php';

//connection to db
$conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);

// Check connection established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//preparing sql query for zimmer search
$conditions = array();
$searchSql = "SELECT Zimmer.id  AS id, Zimmer.variation AS variation, Zimmer.nummer AS nummer, Hotel.name AS name FROM Zimmer INNER JOIN Hotel ON Hotel.id = Zimmer.hotelid";

//search zimmer
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  //prepare conditions for query if they was passed
  if (!empty($_GET['ID'])) {
    $conditions[] = "ID = " . $_GET['ID'];
  }

  if (!empty($_GET['NUMMER'])) {
    $conditions[] = "NUMMER = " . $_GET['NUMMER'];
  }

  if (!empty($_GET['VARIATION'])) {
    $conditions[] = "UPPER(VARIATION) like '%" . strtoupper($_GET['VARIATION']) . "%'";
  }

  if (!empty($_GET['HOTEL'])) {
    $conditions[] = "name = '" . $_GET['HOTEL'] . "'";
  }

  if (!empty($conditions)) {
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete zimmer
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM Zimmer WHERE ID = " . $_GET['ID'];

  $result = mysqli_query($conn, $deleteSql);

  if (!$result) {
    die("error while deleting id=" . $_GET['ID']);
  } else {
    header("Location: ?");
  }
}

//create zimmer
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Zimmer (ID, NUMMER, VARIATION, HOTELID) VALUES(" . $_POST['ID'] . ", " . $_POST['NUMMER'] . ", '" . $_POST['VARIATION'] . "', " . $_POST['HOTELID'] . ")";

  $result = mysqli_query($conn, $createSql);

  if (!$result) {
    die("error while creating zimmer"  . mysqli_error($conn));
  } else {
    header("Location: ?");
  }
}

//update zimmer
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Zimmer WHERE ID = " . $_GET['ID'];
  $stmt = mysqli_query($conn, $getRowForUpdate);
  $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateSql = "UPDATE Zimmer SET NUMMER = " . $_POST['NUMMER'] . ", VARIATION = '" . $_POST['VARIATION'] . "', HOTELID = " . $_POST['HOTELID'] . " WHERE ID = " . $_GET['ID'];

    $result = mysqli_query($conn, $updateSql);

    if (!$result) {
      die("error while updating zimmer" . mysqli_error($conn));
    } else {
      header("Location: ?");
    }
  }
}

//add order for beautify
$searchSql .= " ORDER BY ID";

//parse and execute sql statement

$result = mysqli_query($conn, $searchSql);

//additional result for fetching hotels for select dropdown
$result2 = mysqli_query($conn, 'select id, name from Hotel');

/*
if (!$result) {
  $error = oci_error($stmt);
}

if (!$result2) {
  $error = oci_error($stmt2);
}
*/
?>

<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Zimmer</title>
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
                <a href="mitarbeiter.php">Mitarbeiter</a>
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
              <li class="active">
                <a href="zimmer.php">Zimmer</a>
              </li>
        </ul>
      </div>

      <div class="col-md-9">
        <!-- navigation -->
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Zimmer</li>
        </ol>

        <!-- main panel -->
        <div class="panel panel-default">
          <div class="panel-body" style="height: 450px; overflow-y: auto;">

            <!-- form with table -->
            <form method='get'>
              <input type="hidden" name="action" value="search">

              <!-- refresh -->
              <input class="btn btn-link" type="submit" value="Refresh" />

              <!-- table -->
              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <th class="th1" width="50">ID</th>
                    <th class="th1" width="300">Hotel</th>
                    <th class="th1" width="300">Nummer</th>
                    <th class="th1" width="300">Variation</th>
                    <th width="50">Update</th>
                    <th width="50">Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- search -->
                  <tr>
                    <td><input name='ID' value='<?= @$_GET['id'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='HOTEL' value='<?= @$_GET['name'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='NUMMER' value='<?= @$_GET['nummer'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='VARIATION' value='<?= @$_GET['variation'] ?: '' ?>' style="width:100%" /></td>
                    <td></td>
                    <td></td>
                  </tr>

                  <!-- parse query with results -->
                  <?php
                  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
                    <tr>
                      <td class="th1"><?= $row['id'] ?></td>
                      <td><?= $row['name']; ?></td>
                      <td><?= $row['nummer'] ?></td>
                      <td><?= $row['variation'] ?></td>
                      <td><a href="?action=update&ID=<?= $row["id"] ?>">Update</a></td>
                      <td><a href="?action=delete&ID=<?= $row["id"] ?>">Delete</a></td>
                    </tr>
                        <?php } ?>

                </tbody>
              </table>
            </form>
          </div>
        </div>

        <!-- вторая панель с формой -->
        <div class="panel panel-default">
          <div class="panel-body">

            <!-- форма -->
            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&ID=' . $_GET['ID'] : 'create'?>" method='post'>
              <div class="form-group">
                <label class="col-sm-3 control-label">ID</label>
                <div class="col-sm-9">
                  <input class="form-control" name='ID' value="<?=isset($rowForUpdate) ? $rowForUpdate['id'] : ''?>" <?=(isset($_GET['action']) && $_GET['action'] == 'update' ? 'readonly' : '')?>/>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">HOTEL</label>
                <div class="col-sm-9">
                  <select class="form-control" name='HOTELID'>
                    <?php while($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['id'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['hotelid'] === $row['id'] ? 'selected' : '') ?>><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">NUMMER</label>
                <div class="col-sm-9">
                  <input class="form-control" name='NUMMER' value="<?=isset($rowForUpdate) ? $rowForUpdate['nummer'] : ''?>" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">VARIATION</label>
                <div class="col-sm-9">
                  <select name="VARIATION" class="form-control">
                    <option value="EZ" <?= (isset($rowForUpdate) && $rowForUpdate['variation'] === 'EZ' ? 'selected' : '')?>>EZ</option>
                    <option value="DZ" <?= (isset($rowForUpdate) && $rowForUpdate['variation'] === 'DZ' ? 'selected' : '')?>>DZ</option>
                    <option value="Suit" <?= (isset($rowForUpdate) && $rowForUpdate['variation'] === 'Suit' ? 'selected' : '')?>>Suit</option>
                    <option value="DeLUX" <?= (isset($rowForUpdate) && $rowForUpdate['variation'] === 'DeLUX' ? 'selected' : '')?>>DeLUX</option>
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
