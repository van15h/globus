<?php
    include __DIR__ . '/../src/config.php';

    //connection to db
    $conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


//preparing sql query for person search
$conditions = array();
$searchSql = "SELECT * FROM Person";

//search person
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  //prepare conditions for query if they was passed
  if (!empty($_GET['ID'])) {
    $conditions[] = "ID = " . $_GET['ID'];
  }

  if (!empty($_GET['NAME'])) {
    $conditions[] = "UPPER(NAME) like '%" . strtoupper($_GET['NAME']) . "%'";
  }

  if (!empty($_GET['SVNummer'])) {
    $conditions[] = "UPPER(NAME) like '%" . strtoupper($_GET['SVNummer']) . "%'";
  }

  if (!empty($_GET['GEBURTSDATUM'])) {
    $conditions[] = "GEBURTSDATUM = '" . strtoupper($_GET['GEBURTSDATUM']) . "'";
  }

  if (!empty($_GET['EMAIL'])) {
    $conditions[] = "UPPER(EMAIL) like '%" . strtoupper($_GET['EMAIL']) . "%'";
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
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete person
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM Person WHERE ID = " . $_GET['ID'];
  $result = mysqli_query($conn,$deleteSql);

  if (!$result) {
       die("error while deleting id=" . $_GET['ID']);
           } else {
    header("Location: ?");
  }
}

//create person
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Person (id, name, SVNummer, geburtsdatum, email, plz, ort, strasse) VALUES(" . $_POST['ID'] . ", '" . $_POST['NAME'] . "', '" . $_POST['SVNummer'] . "', '" . $_POST['GEBURTSDATUM'] . "', '" . $_POST['EMAIL'] . "', '" . $_POST['PLZ'] . "', '" . $_POST['ORT'] . "', '" . $_POST['STRASSE'] . "')";

    $result = mysqli_query($conn,$createSql);
  if (!$result) {
     die("error while creating person". mysqli_error($conn));
  } else {
    header("Location: ?");
  }

}

//update person
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Person WHERE ID = '" . $_GET['ID'] . "'";
  $stmt = mysqli_query($conn, $getRowForUpdate);
    $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateSql = "UPDATE Person SET NAME = '" . $_POST['NAME'] . "', SVNummer = '" . $_POST['SVNummer'] . "', GEBURTSDATUM = '" . $_POST['GEBURTSDATUM'] . "', EMAIL = '" . $_POST['EMAIL'] . "', PLZ = '" . $_POST['PLZ'] . "', ORT = '" . $_POST['ORT'] . "', STRASSE = '" . $_POST['STRASSE'] . "' WHERE ID = '" . $_GET['ID'] . "'";

       $result = mysqli_query($conn, $updateSql);

    if (!$result) {
        die("error while updating person". mysqli_error($conn));
    } else {
      header("Location: ?");
    }
  }
}

    //add order for beautify
    $searchSql .= " ORDER BY ID";

    $result = mysqli_query($conn,$searchSql);

    if (!$result) {
      die("error while adding person". mysqli_error($conn));
    }
?>

<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Personen</title>
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
              <li class="active">
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
          <li class="active">Personen</li>
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
                    <th class="th1" width="300">NAME</th>
                    <th class="th1" width="300">SVNummer</th>
                    <th class="th1" width="300">GEBURTSDATUM</th>
                    <th class="th1" width="300">EMAIL</th>
                    <th width="200">PLZ</th>
                    <th class="th1" width="300">ORT</th>
                    <th class="th1" width="300">STRASSE</th>
                    <th width="50">update</th>
                    <th width="50">delete</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- строка с поиском -->
                  <tr>
                    <td><input name='ID' value='<?= @$_GET['ID'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='NAME' value='<?= @$_GET['NAME'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='SVNummer' value='<?= @$_GET['SVNummer'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='GEBURTSDATUM' value='<?= @$_GET['GEBURTSDATUM'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='EMAIL' value='<?= @$_GET['EMAIL'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='PLZ' value='<?= @$_GET['PLZ'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='ORT' value='<?= @$_GET['ORT'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='STRASSE' value='<?= @$_GET['STRASSE'] ?: '' ?>' style="width:100%" /></td>
                    <td></td>
                    <td></td>
                  </tr>

                  <!-- вывод строк с информацией из базы -->
                  <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)): ?>
                  <tr>
                    <td class="th1"><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['SVNummer'] ?></td>
                    <td><?= $row['geburtsdatum'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['plz'] ?></td>
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

        <!-- вторая панель с формой -->
        <div class="panel panel-default">
          <div class="panel-body">

            <!-- форма -->
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
                <label for="inputEmail3" class="col-sm-2 control-label">SVNummer</label>
                <div class="col-sm-10">
                  <input class="form-control" name='SVNummer' value="<?=isset($rowForUpdate) ? $rowForUpdate['SVNummer'] : ''?>" />
                </div>
              </div>

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">geburtsdatum</label>
                <div class="col-sm-10">
                  <input class="form-control" name='GEBURTSDATUM' value="<?=isset($rowForUpdate) ? $rowForUpdate['geburtsdatum'] : ''?>" />
                </div>
              </div>

              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">email</label>
                <div class="col-sm-10">
                  <input class="form-control" name='EMAIL' value="<?=isset($rowForUpdate) ? $rowForUpdate['email'] : ''?>" />
                </div>
              </div>

              <!-- строка с plz label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">plz</label>
                <div class="col-sm-10">
                  <input class="form-control" name='PLZ' value="<?=isset($rowForUpdate) ? $rowForUpdate['plz'] : ''?>" />
                </div>
              </div>

              <!-- строка с ort label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">ort</label>
                <div class="col-sm-10">
                  <input class="form-control" name='ORT' value="<?=isset($rowForUpdate) ? $rowForUpdate['ort'] : ''?>" />
                </div>
              </div>

              <!-- строка с ort label + input -->
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">strasse</label>
                <div class="col-sm-10">
                  <input class="form-control" name='STRASSE' value="<?=isset($rowForUpdate) ? $rowForUpdate['strasse'] : ''?>" />
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
