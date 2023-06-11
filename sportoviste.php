<style>
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;  
      padding-right: 50px;
    }
    .tabulka{
      margin-left: 200px;
      width: 1700px;
    }

    h1 {
      z-index: 1;
      margin-top: 100px; /* Upravit podle potřeby */
      text-align: center;
      margin-bottom: 20px;
    }
  </style>

<?php
$titulek = "sportoviste";
require("db_conect.php");
require("top_html.phtml");
require("navigace.phtml");
?>
<h1>Sportoviště</h1>

  <main>
    <?php
    if (isset($_POST['search'])) {
        $search = mysqli_real_escape_string($dbSpojeni, $_POST['search']);
        $sql = "SELECT * FROM sportoviste WHERE nazev LIKE '%$search%' OR mesto LIKE '%$search%'";
    } else {
        $sql = "SELECT * FROM sportoviste";
    }

    $result = mysqli_query($dbSpojeni, $sql);
    ?>

    <!-- Tabulka s výpisem sportovišť -->
    <table class='tabulka'>
        <tr>
            <th>Název</th>
            <th>Město</th>
            <th>Typ sportoviště</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo ($row['nazev']); ?></td>
                <td><?php echo ($row['mesto']); ?></td>
                <td><?php echo ($row['typ_sportoviste']); ?></td>
                <td><a href="rezervace.php?id=<?php echo urlencode($row['ID_s']); ?>">Rezervovat</a></td>
            </tr>
        <?php }
        ?>
    </table>

    <?php
    mysqli_free_result($result);
    mysqli_close($dbSpojeni);
    ?>
  </main>
</div>

