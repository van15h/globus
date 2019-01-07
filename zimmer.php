
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
$searchSql = "SELECT * FROM Zimmer";

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

  if (!empty($_GET['HOTELID'])) {
    $conditions[] = "HOTELID = " . $_GET['HOTELID'];
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
  $getRowForUpdate = "SELECT * FROM zimmer WHERE ID = " . $_GET['ID'];
  $stmt = mysqli_query($conn, $getRowForUpdate);
  oci_execute($stmt);
  $rowForUpdate = mysqli_fetch_array($stmt, MYSQLI_ASSOC);

  if (!empty($_POST)) {
    $updateSql = "UPDATE zimmer SET NUMMER = " . $_POST['NUMMER'] . ", VARIATION = '" . $_POST['VARIATION'] . "', HOTELID = " . $_POST['HOTELID'] . " WHERE ID = " . $_GET['ID'];
	
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

//additional result for fetching persons for select dropdown
$result2 = mysqli_query($conn, 'select ID, NAME from Hotel');

/**
if (!$result) {
  $error = oci_error($stmt);
}

if (!$result2) {
  $error = oci_error($stmt2);
}
**/
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
                <a href="procedure.php">Procedure</a>
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
        <!-- навигация -->
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Zimmer</li>
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
                    <th class="th1" width="50">ID</th>
                    <th class="th1" width="300">HOTEL</th>
                    <th class="th1" width="300">NUMMER</th>
                    <th class="th1" width="300">VARIATION</th>
                    <th width="50">update</th> 
                    <th width="50">delete</th>      
                  </tr>
                </thead>
                <tbody>
                  <!-- строка с поиском -->
                  <tr>
                    <td><input name='ID' value='<?= @$_GET['id'] ?: '' ?>' style="width:100%" /></td>
                    <td></td>
                    <td><input name='NUMMER' value='<?= @$_GET['nummer'] ?: '' ?>' style="width:100%" /></td>
                    <td><input name='VARIATION' value='<?= @$_GET['variation'] ?: '' ?>' style="width:100%" /></td>
                    <td></td>
                    <td></td>
                  </tr>

                  <!-- вывод строк с информацией из базы -->
                  <?php while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)): ?>
                    <tr>
                      <td class="th1"><?= $row['id'] ?></td>
                      <td>
                        <?php
                          while($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                            echo $row2['id'] === $row['hotelid'] ? $row2['name'] : '';
                          }

                          //rewind cursor
						   mysqli_query($conn, 'select NAME from Hotel');
                      
                        ?>
                      </td>
                      <td><?= $row['nummer'] ?></td>
                      <td><?= $row['variation'] ?></td>
                      <td><a href="?action=update&ID=<?= $row["id"] ?>">update</a></td>
                      <td><a href="?action=delete&ID=<?= $row["id"] ?>">delete</a></td>
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
                <label class="col-sm-3 control-label">ID</label>
                <div class="col-sm-9">
                  <input class="form-control" name='ID' value="<?=isset($rowForUpdate) ? $rowForUpdate['ID'] : ''?>" <?=(isset($_GET['action']) && $_GET['action'] == 'update' ? 'readonly' : '')?>/>
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
                  <input class="form-control" name='NUMMER' value="<?=isset($rowForUpdate) ? $rowForUpdate['NUMMER'] : ''?>" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">VARIATION</label>
                <div class="col-sm-9">
                  <select name="VARIATION" class="form-control">
                    <option value="EZ" <?= (isset($rowForUpdate) && $rowForUpdate['VARIATION'] === 'EZ' ? 'selected' : '')?>>EZ</option>
                    <option value="DZ" <?= (isset($rowForUpdate) && $rowForUpdate['VARIATION'] === 'DZ' ? 'selected' : '')?>>DZ</option>
                    <option value="Suit" <?= (isset($rowForUpdate) && $rowForUpdate['VARIATION'] === 'Suit' ? 'selected' : '')?>>Suit</option>
                    <option value="DeLUX" <?= (isset($rowForUpdate) && $rowForUpdate['VARIATION'] === 'DeLUX' ? 'selected' : '')?>>DeLUX</option>
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
