<?php
    include __DIR__ . '/../src/config.php';
    
    //connection to db
    $conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }



//preparing sql query for kunde search
$conditions = array();
$searchSql = "SELECT Kunde.personid AS personid, Kunde.kundenummer AS kundenummer, Kunde.telefonnummer AS telefonnummer, Kunde.kontodaten AS kontodaten, Person.name AS name FROM Kunde INNER JOIN Person ON Person.id = Kunde.personid
";

//search kunde
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  //prepare conditions for query if they was passed
  if (!empty($_GET['PERSONID'])) {
    $conditions[] = "PERSONID = " . $_GET['PERSONID'];
  }

  if (!empty($_GET['KUNDENUMMER'])) {
    $conditions[] = "KUNDENUMMER = " . $_GET['KUNDENUMMER'];
  }

  if (!empty($_GET['TELEFONNUMMER'])) {
    $conditions[] = "TELEFONNUMMER = " . $_GET['TELEFONNUMMER'];
  }
  
  if (!empty($_GET['PERSON'])) {
    $conditions[] = "name = '" . $_GET['PERSON'] . "'";
  }

  if (!empty($_GET['KONTODATEN'])) {
    $conditions[] = "UPPER(KONTODATEN) like '%" . strtoupper($_GET['KONTODATEN']) . "%'";
  }

  if (!empty($conditions)) {
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete kunde
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM Kunde WHERE PERSONID = " . $_GET['PERSONID'];

  //$stmt = @oci_parse($conn, $deleteSql);
  //$result = @oci_execute($stmt);

    $result = mysqli_query($conn,$deleteSql);
    
  if (!$result) {
      die("error while deleting kunde" . $_GET['ID']);
  } else {
    header("Location: ?");
  }
}

//create kunde
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Kunde (PERSONID, KUNDENUMMER, TELEFONNUMMER, KONTODATEN) VALUES(" . $_POST['PERSONID'] . ", " . $_POST['KUNDENUMMER'] . ", " . $_POST['TELEFONNUMMER'] . ", '" . $_POST['KONTODATEN'] . "')";

  //$stmt = @oci_parse($conn, $createSql);
  //$result = @oci_execute($stmt);

  $result = mysqli_query($conn, $createSql);
  if (!$result) {
    die("error while creating kunde". mysqli_error($conn));
  } else {
    header("Location: ?");
  }
}

//update kunde
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Kunde WHERE PERSONID = " . $_GET['PERSONID'];
  //$stmt = oci_parse($conn, $getRowForUpdate);
  //oci_execute($stmt);
  //$rowForUpdate = oci_fetch_array($stmt, OCI_ASSOC);

    $stmt = mysqli_query($conn, $getRowForUpdate);
    $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);
    
  if (!empty($_POST)) {
    $updateSql = "UPDATE Kunde SET KUNDENUMMER = " . $_POST['KUNDENUMMER'] . ", TELEFONNUMMER = " . $_POST['TELEFONNUMMER'] . ", KONTODATEN = '" . $_POST['KONTODATEN'] . "' WHERE PERSONID = " . $_GET['PERSONID'];

   // $stmt = @oci_parse($conn, $updateSql);
   // $result = @oci_execute($stmt);

   $result = mysqli_query($conn, $updateSql);
    if (!$result) {
        die("error while updating kunde". mysqli_error($conn));

    } else {
      header("Location: ?");
    }
  }
}

//add order for beautify
$searchSql .= " ORDER BY PERSONID";


//parse and execute sql statement
//$stmt = oci_parse($conn, $searchSql);
//$result = oci_execute($stmt);

    $result = mysqli_query($conn,$searchSql);
    
//additional result for fetching persons for select dropdown
//$stmt2 = oci_parse($conn, 'select ID, NAME from person');
//$result2 = oci_execute($stmt2);

      $result2 = mysqli_query($conn,'select id, name from Person');
    
/*if (!$result) {
die("error while search kunde");
}

if (!$result2) {
    die("error while adding kunde");

}*/
?>

<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Mitarbeiter</title>
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
              <li class="active">
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
              <li>
                <a href="reisebuero.php">Reisebuero</a>
              </li>
              <li>
                <a href="zimmer.php">Zimmer</a>
              </li>
        </ul>
      </div>

      <div class="col-md-9">
        <!-- Ð½Ð°Ð²Ð¸Ð³Ð°Ñ†Ð¸Ñ -->
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Mitarbeiter</li>
        </ol>

        <!-- Ð¾ÑˆÐ¸Ð±ÐºÐ¸ ÐµÑÐ»Ð¸ ÐµÑÑ‚ÑŒ -->

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger">
            <?=isset($error['message']) ? $error['message'] : ''?> </br>
            <small><?=isset($error['sqltext']) ? $error['sqltext'] : ''?></small> </br>
            <small><?=isset($error['offset']) ? 'Error position: ' . $error['offset'] : ''?></small>
          </div>
        <?php endif; ?>

        <!-- Ð¾ÑÐ½Ð¾Ð²Ð½Ð°Ñ Ð¿Ð°Ð½ÐµÐ»ÑŒ Ñ Ñ‚Ð°Ð±Ð»Ð¸Ñ†ÐµÐ¹ -->
        <div class="panel panel-default">
          <div class="panel-body">

            <!-- Ð¾ÑÐ½Ð¾Ð²Ð½Ð°Ñ Ð¿Ð°Ð½ÐµÐ»ÑŒ Ñ Ñ‚Ð°Ð±Ð»Ð¸Ñ†ÐµÐ¹ -->
            <form id='hotel2' method='get'>
              <input type="hidden" name="action" value="search">

              <!-- ÐºÐ½Ð¾Ð¿ÐºÐ° Ñ Ð¾Ð±Ð½Ð¾Ð²Ð»ÐµÐ½Ð¸ÐµÐ¼ -->
              <input class="btn btn-link" type="submit" value="Refresh" />

              <!-- Ð¾ÑÐ½Ð¾Ð²Ð½Ð°Ñ Ñ‚Ð°Ð±Ð»Ð¸Ñ†Ð° -->
              <table class="table table-striped table-responsive">
                <thead>
                  <tr>
                    <th class="th1" width="50">PERSONID</th>
                    <th class="th1" width="50">PERSON</th>
                    <th class="th1" width="300">KUNDENUMMER</th>
                    <th class="th1" width="300">TELEFONNUMMER</th>
                    <th class="th1" width="300">KONTODATEN</th>
                    <th width="50">update</th> 
                    <th width="50">delete</th>      
                  </tr>
                </thead>
                <tbody>
                  <!-- ÑÑ‚Ñ€Ð¾ÐºÐ° Ñ Ð¿Ð¾Ð¸ÑÐºÐ¾Ð¼ -->
                  <tr>
                    <td><input name='PERSONID' value='<?= @$_GET['PERSONID'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='PERSON' value='<?= @$_GET['PERSON'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='KUNDENUMMER' value='<?= @$_GET['KUNDENUMMER'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='TELEFONNUMMER' value='<?= @$_GET['TELEFONNUMMER'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='KONTODATEN' value='<?= @$_GET['KONTODATEN'] ?: '' ?>' style="width:100%" /></td>
                    <td></td>
                    <td></td>
                  </tr>

                  <!-- Ð²Ñ‹Ð²Ð¾Ð´ ÑÑ‚Ñ€Ð¾Ðº Ñ Ð¸Ð½Ñ„Ð¾Ñ€Ð¼Ð°Ñ†Ð¸ÐµÐ¹ Ð¸Ð· Ð±Ð°Ð·Ñ‹ -->
                  <?php 
				  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				  echo $row;
				  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                    <tr>
                      <td class="th1"><?= $row['personid'] ?></td>
                      <td><?= $row['name'] ?></td>
                      <td><?= $row['kundenummer'] ?></td>
                      <td><?= $row['telefonnummer'] ?></td>
                      <td><?= $row['kontodaten'] ?></td>
                      <td><a href="?action=update&PERSONID=<?= $row["personid"] ?>">update</a></td>
                      <td><a href="?action=delete&PERSONID=<?= $row["personid"] ?>">delete</a></td>
                    </tr>
                  <?php } ?>

                </tbody>
              </table>
            </form>
          </div>
        </div>
        

        
        <?php
            //oci_free_statement($stmt);
            mysqli_free_result($result);
            mysqli_close($conn);
            
            ?>

        <!-- Ð²Ñ‚Ð¾Ñ€Ð°Ñ Ð¿Ð°Ð½ÐµÐ»ÑŒ Ñ Ñ„Ð¾Ñ€Ð¼Ð¾Ð¹ -->
        <div class="panel panel-default">
          <div class="panel-body">
            
            <!-- Ñ„Ð¾Ñ€Ð¼Ð° -->
            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&PERSONID=' . $_GET['PERSONID'] : 'create'?>" method='post'>
              <div class="form-group">
                <label class="col-sm-3 control-label">PERSON</label>
                <div class="col-sm-9">
                  <select class="form-control" name='PERSONID' >
                    <?php while($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['id'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['personid'] === $row['ID'] ? 'selected' : '') ?>><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">KUNDENUMMER</label>
                <div class="col-sm-9">
                  <input class="form-control" name='KUNDENUMMER' value="<?=isset($rowForUpdate) ? $rowForUpdate['KUNDENUMMER'] : ''?>" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">TELEFONNUMMER</label>
                <div class="col-sm-9">
                  <input class="form-control" name='TELEFONNUMMER' value="<?=isset($rowForUpdate) ? $rowForUpdate['TELEFONNUMMER'] : ''?>" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">KONTODATEN</label>
                <div class="col-sm-9">
                  <input class="form-control" name='KONTODATEN' value="<?=isset($rowForUpdate) ? $rowForUpdate['KONTODATEN'] : ''?>" />
                </div>
              </div>

              <!-- ÑÑ‚Ñ€Ð¾ÐºÐ° Ñ ÐºÐ½Ð¾Ð¿ÐºÐ°Ð¼Ð¸ Ð¾Ñ‚Ð¿Ñ€Ð°Ð²ÐºÐ¸ Ð¸ ÑÐ±Ñ€Ð¾ÑÐ° Ñ„Ð¾Ñ€Ð¼Ñ‹ -->
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
