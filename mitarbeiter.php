<?php
 
    include __DIR__ . '/../src/config.php';
    
    //connection to db
    $conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


//preparing sql query for mitarbeiter search
$conditions = array();
//$searchSql = "SELECT * FROM mitarbeiter";

$searchSql = "SELECT Mitarbeiter.personid AS personid, Mitarbeiter.steuernummer AS steuernummer, Mitarbeiter.gehalt AS gehalt,
Mitarbeiter.beschaeftigungRBid AS beschaeftigungRBid, Person.name AS name1, Reisebuero.id AS name2 FROM Mitarbeiter
INNER JOIN Reisebuero ON Reisebuero.id = Mitarbeiter.beschaeftigungRBid
INNER JOIN Person ON Person.id = Mitarbeiter.personid";


//search mitarbeiter
if (!empty($_GET['action']) && $_GET['action'] == 'search') {
  //prepare conditions for query if they was passed
  if (!empty($_GET['PERSONID'])) {
    $conditions[] = "PERSONID = " . $_GET['PERSONID'];
  }

  if (!empty($_GET['STEUERNUMMER'])) {
    $conditions[] = "STEUERNUMMER = " . $_GET['STEUERNUMMER'];
  }

  if (!empty($_GET['GEHALT'])) {
    $conditions[] = "GEHALT = " . $_GET['GEHALT'];
  }

  if (!empty($_GET['BESCHAEFTIGUNGRBID'])) {
    $conditions[] = "UPPER(BESCHAEFTIGUNGRBID) like '%" . strtoupper($_GET['BESCHAEFTIGUNGRBID']) . "%'";
  }

  if (!empty($conditions)) {
    $searchSql .= " WHERE " . implode(' AND ', $conditions);
  }
}

//delete mitarbeiter
if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $deleteSql = "DELETE FROM Mitarbeiter WHERE PERSONID = " . $_GET['PERSONID'];

 $result = mysqli_query($conn,$deleteSql);

  if (!$result) {
     die("error while deleting mitarbeiter" . $_GET['ID']);
      
  } else {
    header("Location: ?");
  }
}

//create mitarbeiter
if (!empty($_GET['action']) && $_GET['action'] == 'create') {
  $createSql = "INSERT INTO Mitarbeiter (PERSONID, STEUERNUMMER, GEHALT, BESCHAEFTIGUNGRBID) VALUES(" . $_POST['PERSONID'] . ", " . $_POST['STEUERNUMMER'] . ", " . $_POST['GEHALT'] . ", '" . $_POST['BESCHAEFTIGUNGRBID'] . "')";

  $result = mysqli_query($conn, $createSql);

  if (!$result) {
    die("error while creating mitarbeiter "  . mysqli_error($conn));
  } else {
    header("Location: ?");
  }
}

//update mitarbeiter
if (!empty($_GET['action']) && $_GET['action'] == 'update') {
  $getRowForUpdate = "SELECT * FROM Mitarbeiter WHERE PERSONID = " . $_GET['PERSONID'];
  $stmt = mysqli_query($conn, $getRowForUpdate);
  $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);


  if (!empty($_POST)) {
    $updateSql = "UPDATE Mitarbeiter SET STEUERNUMMER = " . $_POST['STEUERNUMMER'] . ", GEHALT = " . $_POST['GEHALT'] . ", BESCHAEFTIGUNGRBID = '" . $_POST['BESCHAEFTIGUNGRBID'] . "' WHERE PERSONID = " . $_GET['PERSONID'];

      $result = mysqli_query($conn, $updateSql);

    if (!$result) {
      die("error while updating mitarbeiter" . mysqli_error($conn));
    } else {
      header("Location: ?");
    }
  }
}

//add order for beautify
$searchSql .= " ORDER BY PERSONID";


//parse and execute sql statement
$result = mysqli_query($conn,$searchSql);

//additional result for fetching persons for select dropdown

$result2 = mysqli_query($conn, 'select id, name from Person');

$result3 = mysqli_query($conn, 'select id, name from Reisebuero');
/**
if (!$result) {
  die("error while adding mitarbeiter" . mysqli_error($conn));
}

if (!$result2) {
  die("error while adding person" . mysqli_error($conn));
}

if (!$result3) {
 die("error while adding reisebuero" . mysqli_error($conn));
}**/
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
              <li>
                <a href="kunde.php">Kunde</a>
              </li>
              <li class="active">
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
        <!-- навигация -->
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Mitarbeiter</li>
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
                    <th class="th1" width="50">PERSONID</th>
                    <th class="th1" width="50">PERSON</th>
                    <th class="th1" width="300">STEUERNUMMER</th>
                    <th class="th1" width="300">GEHALT</th>
                    <th class="th1" width="300">BESCHAEFTIGUNGRBID</th>
                    <th width="50">update</th> 
                    <th width="50">delete</th>      
                  </tr>
                </thead>
                <tbody>
                  <!-- строка с поиском -->
                  <tr>
                    <td><input name='PERSONID' value='<?= @$_GET['PERSONID'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='PERSON' value='<?= @$_GET['PERSON'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='STEUERNUMMER' value='<?= @$_GET['STEUERNUMMER'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='GEHALT' value='<?= @$_GET['GEHALT'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='BESCHAEFTIGUNGRBID' value='<?= @$_GET['BESCHAEFTIGUNGRBID'] ?: '' ?>' style="width:100%" /></td>
                    <td></td>
                    <td></td>
                  </tr>

                  <!-- вывод строк с информацией из базы -->
				  
                  <?php 
				  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				  echo $row;
				  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                    <tr>
                      <td class="th1"><?= $row['personid'] ?></td>  
					  <td><?= $row['name1'] ?></td>					  
                      <td><?= $row['steuernummer'] ?></td>
                      <td><?= $row['gehalt'] ?></td>
					  <td><?= $row['name2'] ?></td>                    
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
        
        <!-- вторая панель с формой -->
        <div class="panel panel-default">
          <div class="panel-body">
            
            <!-- форма -->
            <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&PERSONID=' . $_GET['PERSONID'] : 'create'?>" method='post'>
              <div class="form-group">
                <label class="col-sm-3 control-label">PERSON</label>
                <div class="col-sm-9">
                  <select class="form-control" name='PERSONID' >
                    <?php while($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['id'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['personid'] === $row['id'] ? 'selected' : '') ?>><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">STEUERNUMMER</label>
                <div class="col-sm-9">
                  <input class="form-control" name='STEUERNUMMER' value="<?=isset($rowForUpdate) ? $rowForUpdate['steuernummer'] : ''?>" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">GEHALT</label>
                <div class="col-sm-9">
                  <input class="form-control" name='GEHALT' value="<?=isset($rowForUpdate) ? $rowForUpdate['gehalt'] : ''?>" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">BESCHAEFTIGUNGRBID</label>
                <div class="col-sm-9">
                  <select class="form-control" name='BESCHAEFTIGUNGRBID'>
                    <?php while($row = mysqli_fetch_array($result3, MYSQLI_ASSOC)): ?>
                      <option value="<?= $row['id'] ?>" <?= (isset($rowForUpdate) && $rowForUpdate['beschaeftigungRBid'] === $row['id'] ? 'selected' : '') ?>><?= $row['name'] ?></option>
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
