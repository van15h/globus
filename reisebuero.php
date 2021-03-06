<?php
//connection to db
include __DIR__ . '/../src/config.php';

//connection to db
$conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);

// Check connection established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//preparing sql query for reisebuero search
$conditions = array();
$searchSql = "SELECT * FROM Reisebuero";

//search reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  //prepare conditions for query if they was passed
  if (!empty($_GET['ID'])) {
    $conditions[] = "ID = " . $_GET['ID'];
  }

  if (!empty($_GET['NAME'])) {
    $conditions[] = "UPPER(NAME) like '%" . strtoupper($_GET['NAME']) . "%'";
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

  if (!empty($_GET['KONTODATEN'])) {
    $conditions[] = "UPPER(KONTODATEN) like '%" . strtoupper($_GET['KONTODATEN']) . "%'";
  }

  if (!empty($conditions)) {
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM Reisebuero WHERE ID = " . $_GET['ID'];

  $result = mysqli_query($conn, $deleteSql);

  if (!$result) {
     die("error while deleting id=" . $_GET['ID']);
  } else {
    header("Location: ?");
  }
}

//create reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Reisebuero (ID, NAME, PLZ, ORT, STRASSE, KONTODATEN) VALUES(" . $_POST['ID'] . ", '" . $_POST['NAME'] . "', '" . $_POST['PLZ'] . "', '" . $_POST['ORT'] . "', '" . $_POST['STRASSE'] . "', '" . $_POST['KONTODATEN'] . "')";

  $result = mysqli_query($conn, $createSql);

  if (!$result) {
     die("error while creating Reisebuero"  . mysqli_error($conn));
  }
}

//update reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Reisebuero WHERE ID = '" . $_GET['ID'] . "'";
  $stmt = mysqli_query($conn, $getRowForUpdate);

  $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateSql = "UPDATE Reisebuero SET NAME = '" . $_POST['NAME'] . "', PLZ = '" . $_POST['PLZ'] . "', ORT = '" . $_POST['ORT'] . "', STRASSE = '" . $_POST['STRASSE'] . "', KONTODATEN = '" . $_POST['KONTODATEN'] . "' WHERE ID = '" . $_GET['ID'] . "'";

    $result = mysqli_query($conn, $updateSql);

    if (!$result) {
      die("error while updating reisebuero" . mysqli_error($conn));
    } else {
      header("Location: ?");
    }
  }
}

//add order for beautify
$searchSql .= " ORDER BY ID";

//parse and execute sql statement
$result = mysqli_query($conn, $searchSql);

if (!$result) {
  die("error while get info from Reisebuero"  . mysqli_error($conn));
}
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
              <li>
                <a href="platzierung.php">Platzierung</a>
              </li>
              <li>
                <a href="reise.php">Reise</a>
              </li>
              <li class="active">
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
          <li class="active">Reisebuero</li>
        </ol>

        
        <div class="panel panel-default">
          <div class="panel-body">

         
            <form id='hotel2' method='get'>
              <input type="hidden" name="action" value="search">

             
              <input class="btn btn-link" type="submit" value="Refresh" />

            
              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <th class="th1" width="50">ID</th>
                    <th class="th1" width="300">name</th>
                    <th width="200">plz</th>
                    <th class="th1" width="300">ort</th>
                    <th class="th1" width="300">strasse</th>
                    <th class="th1" width="300">kontodaten</th>
                    <th width="50">update</th>
                    <th width="50">delete</th>
                  </tr>
                </thead>
                <tbody>
               
                  <tr>
                    <td><input name='ID' value='<?= @$_GET['ID'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='NAME' value='<?= @$_GET['NAME'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='PLZ' value='<?= @$_GET['PLZ'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='ORT' value='<?= @$_GET['ORT'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='STRASSE' value='<?= @$_GET['STRASSE'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='KONTODATEN' value='<?= @$_GET['KONTODATEN'] ?: '' ?>' style="width:100%" /></td>
                    <td></td>
                    <td></td>
                  </tr>

                 
                  <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)): ?>
                  <tr>
                    <td class="th1"><?= $row['id'] ?></td>
                    <td class="th1"><?= $row['name'] ?></td>
                    <td class="th1"><?= $row['plz'] ?></td>
                    <td><?= $row['ort'] ?></td>
                    <td><?= $row['strasse'] ?></td>
                    <td><?= $row['kontodaten'] ?></td>
                    <td><a href="?action=update&ID=<?= $row["id"] ?>">update</a></td>
                    <td><a href="?action=delete&ID=<?= $row["id"] ?>">delete</a></td>
                  </tr>
                  <?php endwhile; ?>

                </tbody>
              </table>
            </form>
          </div>
        </div>

       
        <div class="panel panel-default">
          <div class="panel-body">

        
            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&ID=' . $_GET['ID'] : 'create'?>" method='post'>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">id</label>
                <div class="col-sm-10">
                  <input class="form-control" name='ID' value="<?=isset($rowForUpdate) ? $rowForUpdate['id'] : ''?>" <?=(isset($_GET['action']) && $_GET['action'] == 'update' ? 'readonly' : '')?>/>
                </div>
              </div>

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">name</label>
                <div class="col-sm-10">
                  <input class="form-control" name='NAME' value="<?=isset($rowForUpdate) ? $rowForUpdate['name'] : ''?>" />
                </div>
              </div>

             
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">plz</label>
                <div class="col-sm-10">
                  <input class="form-control" name='PLZ' value="<?=isset($rowForUpdate) ? $rowForUpdate['plz'] : ''?>" />
                </div>
              </div>

           
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">ort</label>
                <div class="col-sm-10">
                  <input class="form-control" name='ORT' value="<?=isset($rowForUpdate) ? $rowForUpdate['ort'] : ''?>" />
                </div>
              </div>

        
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">strasse</label>
                <div class="col-sm-10">
                  <input class="form-control" name='STRASSE' value="<?=isset($rowForUpdate) ? $rowForUpdate['strasse'] : ''?>" />
                </div>
              </div>


              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">kontodaten</label>
                <div class="col-sm-10">
                  <input class="form-control" name='KONTODATEN' value="<?=isset($rowForUpdate) ? $rowForUpdate['kontodaten'] : ''?>" />
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
