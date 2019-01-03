
<?php
    
include __DIR__ . '/../src/config.php';

    //connection to db
    $conn = mysqli_connect(HOST_NAME, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    
    //preparing sql query for reise search
    $conditions = array();
    $searchSql = "SELECT * FROM reise";
    
    //search reise
    if (!empty($_GET['action']) && $_GET['action'] == 'search') {
        //prepare conditions for query if they was passed
        if (!empty($_GET['ID'])) {
            $conditions[] = "ID = " . $_GET['ID'];
        }
        
        if (!empty($_GET['NAME'])) {
            $conditions[] = "UPPER(NAME) like '%" . strtoupper($_GET['NAME']) . "%'";
        }
        
        if (!empty($_GET['EINREISEDATUM'])) {
            $conditions[] = "EINREISEDATUM = '" . $_GET['EINREISEDATUM'] . "'";
        }
        
        if (!empty($_GET['REISEDAUER'])) {
            $conditions[] = "UPPER(REISEDAUER) like '%" . strtoupper($_GET['REISEDAUER']) . "%'";
        }
        
        if (!empty($_GET['PREIS'])) {
            $conditions[] = "PREIS = " . $_GET['PREIS'];
        }
        
        if (!empty($conditions)) {
            $searchSql .= " WHERE " . implode(' AND ', $conditions);
        }
    }
    
    //delete reise
    if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
        $deleteSql = "DELETE FROM reise WHERE ID = " . $_GET['ID'];
        
      //  $stmt = @oci_parse($conn, $deleteSql);
     // $result = @oci_execute($stmt);
     
        $result = mysqli_query($conn,$deleteSql);
        if (!$result) {
            die("error while deleting id=" . $_GET['ID']);
        }
        // else {
        //   header("Location: ?");
        // }
    }
    //create reise
    if (!empty($_GET['action']) && $_GET['action'] == 'create') {
        $createSql = "INSERT INTO reise (NAME, EINREISEDATUM, REISEDAUER, PREIS) VALUES('" . $_POST['NAME'] . "', '" . $_POST['EINREISEDATUM'] . "', '" . $_POST['REISEDAUER'] . "', " . $_POST['PREIS'] . ")";
        
        $stmt = @oci_parse($conn, $createSql);
        $result = @oci_execute($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
        } else {
            header("Location: ?");
        }
    }
    
    //update reise
    if (!empty($_GET['action']) && $_GET['action'] == 'update') {
        $getRowForUpdate = "SELECT * FROM reise WHERE ID = '" . $_GET['ID'] . "'";
        $stmt = oci_parse($conn, $getRowForUpdate);
        oci_execute($stmt);
        $rowForUpdate = oci_fetch_array($stmt, OCI_ASSOC);
        
        if (!empty($_POST)) {
            $updateSql = "UPDATE reise SET NAME = '" . $_POST['NAME'] . "', EINREISEDATUM = '" . $_POST['EINREISEDATUM'] . "', REISEDAUER = '" . $_POST['REISEDAUER'] . "', PREIS = " . $_POST['PREIS'] . " WHERE ID = '" . $_GET['ID'] . "'";
            
            $stmt = @oci_parse($conn, $updateSql);
            $result = @oci_execute($stmt);
            
            if (!$result) {
                $error = oci_error($stmt);
            } else {
                header("Location: ?");
            }
        }
    }
    
    //add order for beautify
    $searchSql .= " ORDER BY ID";
    
    
    //parse and execute sql statement
    $result = mysqli_query($conn,$searchSql);
   
    //$stmt = oci_parse($conn, $searchSql);
    //$result = oci_execute($stmt);
    
   // if (!$result) {
   //     $error = oci_error($stmt);
   // }
    ?>

<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title>Reise</title>
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
        <li class="active">
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
<li class="active">Reise</li>
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
            <th width="200">EINREISEDATUM</th>
            <th class="th1" width="300">REISEDAUER</th>
            <th class="th1" width="300">PREIS</th>
            <th width="50">update</th>
            <th width="50">delete</th>
        </tr>
    </thead>
<tbody>
<!-- строка с поиском -->
    <tr>
        <td><input name='ID' value='<?= @$_GET['ID'] ?: '' ?>' style="width:100%" /></td>
        <td><input name='NAME' value='<?= @$_GET['NAME'] ?: '' ?>' style="width:100%" /></td>
        <td><input name='EINREISEDATUM' value='<?= @$_GET['EINREISEDATUM'] ?: '' ?>' style="width:100%" /></td>
        <td><input name='REISEDAUER' value='<?= @$_GET['REISEDAUER'] ?: '' ?>' style="width:100%" /></td>
        <td><input name='PREIS' value='<?= @$_GET['PREIS'] ?: '' ?>' style="width:100%" /></td>
        <td></td>
        <td></td>
    </tr>

<!-- вывод строк с информацией из базы -->
    <?php
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)):
    ?>
    <tr>
        <td class="th1"><?= $row['id'] ?></td>
        <td class="th1"><?= $row['name'] ?></td>
        <td class="th1"><?= $row['einreisedatum'] ?></td>
        <td class="th1"><?= $row['reisedauer'] ?></td>
        <td class="th1"><?= $row['preis'] ?></td>
        <td><a href="?action=update&ID=<?= $row["ID"] ?>">update</a></td>
        <td><a href="?action=delete&ID=<?= $row["ID"] ?>">delete</a></td>
    </tr>
    <?php endwhile; ?>

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
    <form class="form-horizontal" action="?action=<?=isset($_GET['action']) ? $_GET['action'] . '&ID=' . $_GET['ID'] : 'create'?>" method='post'>
        <div class="form-group">
            <label class="col-sm-3 control-label">id</label>
        <div class="col-sm-9">
    <input class="form-control" name='ID' value="<?=isset($rowForUpdate) ? $rowForUpdate['ID'] : ''?>" <?=(isset($_GET['action']) && $_GET['action'] == 'update' ? 'readonly' : '') ?> />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">name</label>
    <div class="col-sm-9">
        <input class="form-control" name='NAME' value="<?=isset($rowForUpdate) ? $rowForUpdate['NAME'] : ''?>" />
    </div>
</div>

<!-- строка с plz label + input -->
    <div class="form-group">
        <label class="col-sm-3 control-label">EINREISEDATUM</label>
    <div class="col-sm-9">
        <input class="form-control" name='EINREISEDATUM' value="<?=isset($rowForUpdate) ? $rowForUpdate['EINREISEDATUM'] : ''?>" />
    </div>
</div>

<!-- строка с ort label + input -->
    <div class="form-group">
        <label class="col-sm-3 control-label">REISEDAUER</label>
    <div class="col-sm-9">
        <input class="form-control" name='REISEDAUER' value="<?=isset($rowForUpdate) ? $rowForUpdate['REISEDAUER'] : ''?>" />
    </div>
</div>

<!-- строка с ort label + input -->
    <div class="form-group">
        <label class="col-sm-3 control-label">PREIS</label>
    <div class="col-sm-9">
        <input class="form-control" name='PREIS' value="<?=isset($rowForUpdate) ? $rowForUpdate['PREIS'] : ''?>" />
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
