<?php
include __DIR__ . '/../src/config.php';

//connection to db
$conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);

// Check connection established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//preparing sql query for hotel search
$conditions = array();
$searchHotelSql = "SELECT * FROM Hotel";

//search reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'search') {

  //prepare conditions for query if they were passed
  if (!empty($_GET['ID'])) {
    $conditions[] = "ID = " . $_GET['ID'];
  }

  if (!empty($_GET['NAME'])) {
    $conditions[] = "UPPER(NAME) like '%" . strtoupper($_GET['NAME']) . "%'";
  }

  if (!empty($_GET['STERNE'])) {
    $conditions[] = "STERNE = '" . $_GET['STERNE'] . "'";
  }

  if (!empty($_GET['verpflegung'])) {
    $conditions[] = "UPPER(NAME) like '%" . strtoupper($_GET['verpflegung']) . "%'";
  }

  if (!empty($_GET['PLZ'])) {
    $conditions[] = "UPPER(PLZ) like '%" . strtoupper($_GET['PLZ']) . "%'";
  }

  if (!empty($_GET['ORT'])) {
    $conditions[] = "UPPER(ORT) like '%" . strtoupper($_GET['ORT']) . "%'";
  }

  if (!empty($_GET['STRASSE'])) {
    $conditions[] = "UPPER(STRASSE) like '%" . strtoupper($_GET['STRASSE']) . "%'";
  }

  if (!empty($conditions)) {
    $searchHotelSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete hotel
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {

  $deleteHotelSql = "DELETE FROM Hotel WHERE ID = " . $_GET['ID'];
  $result = mysqli_query($conn,$deleteHotelSql);

  if (!$result) {
    die("error while deleting id=" . $_GET['ID']);
  } else {
    header("Location: ?");
  }
}

//create reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createHotelSql = "INSERT INTO Hotel (id, name, sterne, verpflegung, plz, ort, strasse) VALUES (" . $_POST['ID'] . ", '" . $_POST['NAME'] . "', '" . $_POST['STERNE'] . "','" . $_POST['verpflegung'] . "','" . $_POST['PLZ'] . "', '" . $_POST['ORT'] . "', '" . $_POST['STRASSE'] . "')";

  $result = mysqli_query($conn, $createHotelSql);

  if (!$result) {
    die("error while creating hotel "  . mysqli_error($conn));
  } else {
    header("Location: ?");
  }
}

//update reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Hotel WHERE ID = '" . $_GET['ID'] . "'";
  $stmt = mysqli_query($conn, $getRowForUpdate);
  $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateHotelSql = "UPDATE Hotel SET NAME = '" . $_POST['NAME'] . "', STERNE = '" . $_POST['STERNE'] . "', verpflegung = '" . $_POST['verpflegung'] . "', PLZ = '" . $_POST['PLZ'] . "', ORT = '" . $_POST['ORT'] . "',
    STRASSE = '" . $_POST['STRASSE'] . "' WHERE ID = '" . $_GET['ID'] . "'";

    $result = mysqli_query($conn, $updateHotelSql);

    if (!$result) {
      die("error while updating hotel" . mysqli_error($conn));
    } else {
      header("Location: ?");
    }
  }
}

//add ordering
$searchHotelSql .= " ORDER BY ID";
//execute sql statement
$result = mysqli_query($conn,$searchHotelSql);

?>

<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Reisebuero</title>
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
              <li class="active">
                <a href="hotel.php">Hotel</a>
              </li>
              <li>
                <a href="kunde.php">Kunde Registrieren</a>
              </li>
              <li>
                <a href="mitarbeiter.php">Mitarbeiter Registrieren</a>
              </li>
              <li>
                <a href="person.php">Person</a>
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
        <!-- navigation menu -->
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Hotel</li>
        </ol>

        <!-- main div with table -->
        <div class="panel panel-default">
          <div class="panel-body" style="height: 450px; overflow-y: auto;">

            <!-- main form with table -->
            <form id='hotel2' method='get'>
              <input type="hidden" name="action" value="search">

              <!-- refresh -->
              <input class="btn btn-link" type="submit" value="Refresh" />

              <!-- main table -->
              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <th class="th1" width="50">id</th>
                    <th class="th1" width="300">name</th>
                    <th class="th1" width="300">sterne</th>
                    <th class="th1" width="300"> verpflegung </th>
                    <th width="200">plz</th>
                    <th class="th1" width="300">ort</th>
                    <th class="th1" width="300">strasse</th>
                    <th width="50">update</th>
                    <th width="50">delete</th>
                  </tr>
                </thead>
                <tbody>

                  <!-- search -->
                  <tr>
                    <td><input name='ID' value='<?= @$_GET['ID'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='NAME' value='<?= @$_GET['NAME'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='STERNE' value='<?= @$_GET['STERNE'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='verpflegung' value='<?= @$_GET['verpflegung'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='PLZ' value='<?= @$_GET['PLZ'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='ORT' value='<?= @$_GET['ORT'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='STRASSE' value='<?= @$_GET['STRASSE'] ?: '' ?>' style="width:100%" /></td>
                    <td></td>
                    <td></td>
                  </tr>

                  <!-- parse db request -->
                  <?php
                  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)):
                  ?>
                  <tr>
                    <td class="th1"><?= $row['id'] ?></td>
                    <td class="th1"><?= $row['name'] ?></td>
                    <td class="th1"><?= $row['sterne'] ?></td>
                    <td class="th1"><?= $row['verpflegung'] ?></td>
                    <td class="th1"><?= $row['plz'] ?></td>
                    <td><?= $row['ort'] ?></td>
                    <td><?= $row['strasse'] ?></td>
                    <td><a href="?action=update&ID=<?= $row["id"] ?>">update</a></td>
                    <td><a href="?action=delete&ID=<?= $row["id"] ?>">delete</a></td>
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

        <!-- second form -->
        <div class="panel panel-default">
          <div class="panel-body">
             <!-- fields -->
             <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&ID=' . $_GET['ID'] : 'create'?>" method='post'>
            <!-- id label + input -->
             <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">id</label>
                <div class="col-sm-10">
                  <input class="form-control" name='ID' value="<?=isset($rowForUpdate) ? $rowForUpdate['id'] : ''?>" <?=(isset($_GET['action']) && $_GET['action'] == 'update' ? 'readonly' : '')?>/>
                </div>
              </div>

              <!-- hotel name label+ input -->
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">name</label>
                <div class="col-sm-10">
                  <input class="form-control" name='NAME' value="<?=isset($rowForUpdate) ? $rowForUpdate['name'] : ''?>" />
                </div>
              </div>

              <!-- sterne label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">sterne</label>
                <div class="col-sm-10">
                  <select name="STERNE" class="form-control">
                    <option value="1" <?= isset($rowForUpdate) && $rowForUpdate['sterne'] === '1' ? 'selected' : '' ?>>1</option>
                    <option value="2" <?= isset($rowForUpdate) && $rowForUpdate['sterne'] === '2' ? 'selected' : '' ?>>2</option>
                    <option value="3" <?= isset($rowForUpdate) && $rowForUpdate['sterne'] === '3' ? 'selected' : '' ?>>3</option>
                    <option value="4" <?= isset($rowForUpdate) && $rowForUpdate['sterne'] === '4' ? 'selected' : '' ?>>4</option>
                    <option value="5" <?= isset($rowForUpdate) && $rowForUpdate['sterne'] === '5' ? 'selected' : '' ?>>5</option>
                  </select>
                </div>
              </div>

              <!-- verpflegung label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">verpflegung</label>
                <div class="col-sm-10">
                  <select name="verpflegung" class="form-control">
                    <option value="BB" <?= isset($rowForUpdate) && $rowForUpdate['verpflegung'] === 'BB' ? 'selected' : '' ?>>BB</option>
                    <option value="HP" <?= isset($rowForUpdate) && $rowForUpdate['verpflegung'] === 'HP' ? 'selected' : '' ?>>HP</option>
                    <option value="All" <?= isset($rowForUpdate) && $rowForUpdate['verpflegung'] === 'All' ? 'selected' : '' ?>>All</option>
                  </select>
                </div>
              </div>

              <!-- plz label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">plz</label>
                <div class="col-sm-10">
                  <input class="form-control" name='PLZ' value="<?=isset($rowForUpdate) ? $rowForUpdate['plz'] : ''?>" />
                </div>
              </div>

              <!-- ort label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">ort</label>
                <div class="col-sm-10">
                  <input class="form-control" name='ORT' value="<?=isset($rowForUpdate) ? $rowForUpdate['ort'] : ''?>" />
                </div>
              </div>

              <!-- strasse label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">strasse</label>
                <div class="col-sm-10">
                  <input class="form-control" name='STRASSE' value="<?=isset($rowForUpdate) ? $rowForUpdate['strasse'] : ''?>" />
                </div>
              </div>

              <!-- send and reset buttons -->
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
