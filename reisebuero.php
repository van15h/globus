
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
  $deleteSql = "DELETE FROM reisebuero WHERE ID = " . $_GET['ID'];

  $stmt = @oci_parse($conn, $deleteSql);
  $result = @oci_execute($stmt);

  if (!$result) {
    $error = oci_error($stmt);
  } else {
    header("Location: ?");
  }
}

//create reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Reisebuero (ID, NAME, PLZ, ORT, STRASSE, KONTODATEN) VALUES(" . $_POST['ID'] . ", '" . $_POST['NAME'] . "', '" . $_POST['PLZ'] . "', '" . $_POST['ORT'] . "', '" . $_POST['STRASSE'] . "', '" . $_POST['KONTODATEN'] . "')";

  
  $result = @mysqli_query($conn, $createSql);

  if (!$result) {
     die("error while creating Reisebuero"  . mysqli_error($conn));
  } 
   /**else {
    header("Location: ?");
  }**/
}

//update reisebuero
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM reisebuero WHERE ID = '" . $_GET['ID'] . "'";
  $stmt = mysqli_query($conn, $getRowForUpdate);
  oci_execute($stmt);
  $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateSql = "UPDATE reisebuero SET NAME = '" . $_POST['NAME'] . "', PLZ = '" . $_POST['PLZ'] . "', ORT = '" . $_POST['ORT'] . "', STRASSE = '" . $_POST['STRASSE'] . "', KONTODATEN = '" . $_POST['KONTODATEN'] . "' WHERE ID = '" . $_GET['ID'] . "'";

    
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
/**
if (!$result) {
  $error = oci_error($stmt);
}
**/
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
                <a href="procedure.php">Procedure</a>
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
        <!-- навигация -->
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Reisebuero</li>
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
            <form id='hotel2' method='get'>
              <input type="hidden" name="action" value="search">

              <!-- кнопка с обновлением -->
              <input class="btn btn-link" type="submit" value="Refresh" />

              <!-- основная таблица -->
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
                  <!-- строка с поиском -->
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

                  <!-- вывод строк с информацией из базы -->
                  <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)): ?>
                  <tr>
                    <td class="th1"><?= $row['id'] ?></td>
                    <td class="th1"><?= $row['name'] ?></td>
                    <td class="th1"><?= $row['plz'] ?></td>
                    <td><?= $row['ort'] ?></td>
                    <td><?= $row['strasse'] ?></td>
                    <td><?= $row['kontodaten'] ?></td>
                    <td><a href="?action=update&ID=<?= $row["ID"] ?>">update</a></td>
                    <td><a href="?action=delete&ID=<?= $row["ID"] ?>">delete</a></td>
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
            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&ID=' . $_GET['ID'] : 'create'?>" method='post'>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">id</label>
                <div class="col-sm-10">
                  <input class="form-control" name='ID' value="<?=isset($rowForUpdate) ? $rowForUpdate['ID'] : ''?>" <?=(isset($_GET['action']) && $_GET['action'] == 'update' ? 'readonly' : '')?>/>
                </div>
              </div>

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">name</label>
                <div class="col-sm-10">
                  <input class="form-control" name='NAME' value="<?=isset($rowForUpdate) ? $rowForUpdate['NAME'] : ''?>" />
                </div>
              </div>

              <!-- строка с plz label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">plz</label>
                <div class="col-sm-10">
                  <input class="form-control" name='PLZ' value="<?=isset($rowForUpdate) ? $rowForUpdate['PLZ'] : ''?>" />
                </div>
              </div>

              <!-- строка с ort label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">ort</label>
                <div class="col-sm-10">
                  <input class="form-control" name='ORT' value="<?=isset($rowForUpdate) ? $rowForUpdate['ORT'] : ''?>" />
                </div>
              </div>

              <!-- строка с ort label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">strasse</label>
                <div class="col-sm-10">
                  <input class="form-control" name='STRASSE' value="<?=isset($rowForUpdate) ? $rowForUpdate['STRASSE'] : ''?>" />
                </div>
              </div>

              <!-- строка с ort label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">kontodaten</label>
                <div class="col-sm-10">
                  <input class="form-control" name='KONTODATEN' value="<?=isset($rowForUpdate) ? $rowForUpdate['KONTODATEN'] : ''?>" />
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
